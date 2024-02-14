<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Date;
use Altum\Response;
use Altum\Traits\Apiable;

class ApiLinks extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

                break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {
                    $this->patch();
                } else {
                    $this->post();
                }

                break;

            case 'DELETE':
                $this->delete();
                break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('link_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);
        $filters->process();

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/links?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `links`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->link_id,
                'project_id' => (int) $row->project_id,
                'domain_id' => (int) $row->domain_id,
                'pixels_ids' => json_decode($row->pixels_ids),
                'url' => $row->url,
                'location_url' => $row->location_url,
                'settings' => json_decode($row->settings),
                'pageviews' => (int) $row->pageviews,
                'is_enabled' => $row->is_enabled,
                'last_datetime,' => $row->last_datetime,
                'datetime' => $row->datetime
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $link->link_id,
            'project_id' => (int) $link->project_id,
            'domain_id' => (int) $link->domain_id,
            'pixels_ids' => json_decode($link->pixels_ids),
            'url' => $link->url,
            'location_url' => $link->location_url,
            'settings' => json_decode($link->settings),
            'pageviews' => (int) $link->pageviews,
            'is_enabled' => $link->is_enabled,
            'last_datetime,' => $link->last_datetime,
            'datetime' => $link->datetime
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->getValue('links', 'count(`link_id`)');

        if($this->api_user->plan_settings->links_limit != -1 && $total_rows >= $this->api_user->plan_settings->links_limit) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        /* Check for any errors */
        $required_fields = ['location_url'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->api_user);

        /* Get available projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        /* Get available pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->api_user->user_id);

        $_POST['domain_id'] = isset($_POST['domain_id']) ? (isset($domains[$_POST['domain_id']]) ? (int) $_POST['domain_id'] : null) : null;

        if(!$_POST['domain_id'] && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        /* Location & url */
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['url'] = !empty($_POST['url']) && $this->api_user->plan_settings->custom_url_is_enabled ? get_slug($_POST['url'], '-', false) : false;
        $this->check_location_url($_POST['location_url']);

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
            $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;

            if($is_existing_link) {
                $this->response_error(l('links.error_message.url_exists'), 401);
            }

            if(array_key_exists($_POST['url'], \Altum\Router::$routes['']) || in_array($_POST['url'], \Altum\Language::$active_languages) || file_exists(ROOT_PATH . $_POST['url'])) {
                $this->response_error(l('links.error_message.blacklisted_url'), 401);
            }

            if(in_array($_POST['url'], explode(',', settings()->links->blacklisted_keywords))) {
                $this->response_error(l('links.error_message.blacklisted_keyword'), 401);
            }
        }

        if(!$_POST['url']) {
            $is_existing_link = true;

            /* Generate random url if not specified */
            while($is_existing_link) {
                $_POST['url'] = mb_strtolower(string_generate(settings()->links->random_url_length ?? 7));

                $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;
            }
        }

        /* Process the rest of the data */
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : 1;

        $_POST['schedule'] = (int) (bool) $_POST['schedule'];
        if($_POST['schedule'] && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        $_POST['expiration_url'] = get_url($_POST['expiration_url'] ?? null);
        $_POST['pageviews_limit'] = isset($_POST['pageviews_limit']) ? (int) $_POST['pageviews_limit'] : null;
        $this->check_location_url($_POST['expiration_url'], true);

        /* Pixels */
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Project */
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        /* Sensitive content */
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        /* App linking check */
        $_POST['app_linking_is_enabled'] = (bool) isset($_POST['app_linking_is_enabled']);

        $app_linking = [
            'ios_location_url' => null,
            'android_location_url' => null,
            'app' => null,
        ];

        if($_POST['app_linking_is_enabled']) {
            $supported_apps = require APP_PATH . 'includes/app_linking.php';
            foreach($supported_apps as $app_key => $app) {
                foreach($app['formats'] as $format => $targets) {
                    if(str_contains($_POST['location_url'], str_replace('%s', '', $format))) {
                        preg_match('/' . $targets['regex'] . '/', $_POST['location_url'], $match);

                        if(isset($match[1])) {
                            $app_linking['ios_location_url'] = sprintf($targets['iOS'], $match[1]);
                            $app_linking['android_location_url'] = sprintf($targets['Android'], $match[1]);
                            $app_linking['app'] = $app_key;
                        }

                        break 2;
                    }
                }
            }
        }

        /* Cloaking */
        $_POST['cloaking_is_enabled'] = (bool) isset($_POST['cloaking_is_enabled']);
        $_POST['cloaking_title'] = input_clean($_POST['cloaking_title'], 70);
        $cloaking_favicon = \Altum\Uploads::process_upload(null, 'favicons', 'cloaking_favicon', 'cloaking_favicon_remove', settings()->links->favicon_size_limit, 'json_error');

        /* HTTP */
        $_POST['http_status_code'] = in_array($_POST['http_status_code'], [301, 302, 307, 308]) ? (int) $_POST['http_status_code'] : 301;

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = isset($_POST['targeting_type']) && in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : 'false';

        $settings = [
            'schedule' => $_POST['schedule'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'pageviews_limit' => $_POST['pageviews_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'targeting_type' => $_POST['targeting_type'],
            'app_linking_is_enabled' => $_POST['app_linking_is_enabled'],
            'app_linking' => $app_linking,
            'cloaking_is_enabled' => $_POST['cloaking_is_enabled'],
            'cloaking_title' => $_POST['cloaking_title'],
            'cloaking_favicon' => $cloaking_favicon,
            'http_status_code' => $_POST['http_status_code'],
        ];

        /* Process the targeting */
        foreach($targeting_types as $targeting_type) {
            ${'targeting_' . $targeting_type} = [];

            if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                foreach ($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                    if(empty(trim($_POST['targeting_' . $targeting_type . '_value'][$key]))) continue;

                    ${'targeting_' . $targeting_type}[] = [
                        'key' => trim(query_clean($value)),
                        'value' => get_url($_POST['targeting_' . $targeting_type . '_value'][$key]),
                    ];
                }

                $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
            }
        }

        $settings = json_encode($settings);

        /* Prepare the statement and execute query */
        $link_id = db()->insert('links', [
            'user_id' => $this->api_user->user_id,
            'project_id' => $_POST['project_id'],
            'domain_id' => $_POST['domain_id'],
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $_POST['url'],
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'is_enabled' => $_POST['is_enabled'],
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('links?user_id=' . $this->api_user->user_id);
        \Altum\Cache::$adapter->deleteItem('links_total?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $link_id
        ];

        Response::jsonapi_success($data, null, 201);
    }

    private function patch() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }
        $link->settings = json_decode($link->settings);
        $link->pixels_ids = json_decode($link->pixels_ids);

        if(isset($_POST['domain_id']) && $_POST['domain_id'] == 0 && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            $this->response_error(l('create_link_modal.error_message.main_domain_is_disabled'), 401);
        }

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->api_user);

        /* Get available projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        /* Get available pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->api_user->user_id);

        $_POST['domain_id'] = isset($_POST['domain_id']) ? (isset($domains[$_POST['domain_id']]) ? (int) $_POST['domain_id'] : null) : $link->domain_id;

        /* Location & url */
        $_POST['location_url'] = get_url($_POST['location_url'] ?? $link->location_url);
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : $link->url;
        $this->check_location_url($_POST['location_url']);

        /* Check for duplicate url if needed */
        if(
            ($_POST['url'] && $this->api_user->plan_settings->custom_url_is_enabled && $_POST['url'] != $link->url)
            || ($link->domain_id != $_POST['domain_id'])
        ) {
            $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
            $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;

            if($is_existing_link) {
                $this->response_error(l('links.error_message.url_exists'), 401);
            }

            if(array_key_exists($_POST['url'], \Altum\Router::$routes['']) || in_array($_POST['url'], \Altum\Language::$active_languages) || file_exists(ROOT_PATH . $_POST['url'])) {
                $this->response_error(l('links.error_message.blacklisted_url'), 401);
            }

            if(in_array($_POST['url'], explode(',', settings()->links->blacklisted_keywords))) {
                $this->response_error(l('links.error_message.blacklisted_keyword'), 401);
            }
        }

        if(!$_POST['url']) {
            $is_existing_link = true;

            /* Generate random url if not specified */
            while($is_existing_link) {
                $_POST['url'] = mb_strtolower(string_generate(settings()->links->random_url_length ?? 7));

                $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;
            }
        }

        /* Process the rest of the data */
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : $link->is_enabled;

        $_POST['schedule'] = isset($_POST['schedule']) ? (int) (bool) $_POST['schedule'] : $link->settings->schedule;
        if($_POST['schedule'] && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $link->settings->start_date;
            $_POST['end_date'] = $link->settings->end_date;
        }

        $_POST['expiration_url'] = get_url($_POST['expiration_url'] ?? $link->settings->expiration_url);
        $_POST['pageviews_limit'] = isset($_POST['pageviews_limit']) ? (int) $_POST['pageviews_limit'] : $link->settings->pageviews_limit;
        $this->check_location_url($_POST['expiration_url'], true);

        /* Existing pixels */
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Project */
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $link->settings->password;

        /* Sensitive content */
        $_POST['sensitive_content'] = isset($_POST['sensitive_content']) ? (bool) isset($_POST['sensitive_content']) : $link->settings->sensitive_content;

        /* App linking check */
        $_POST['app_linking_is_enabled'] = isset($_POST['app_linking_is_enabled']) ? (bool) isset($_POST['app_linking_is_enabled']) : $link->settings->app_linking_is_enabled;

        $app_linking = [
            'ios_location_url' => null,
            'android_location_url' => null,
            'app' => null,
        ];

        if($_POST['app_linking_is_enabled']) {
            $supported_apps = require APP_PATH . 'includes/app_linking.php';
            foreach($supported_apps as $app_key => $app) {
                foreach($app['formats'] as $format => $targets) {
                    if(str_contains($_POST['location_url'], str_replace('%s', '', $format))) {
                        preg_match('/' . $targets['regex'] . '/', $_POST['location_url'], $match);

                        if(isset($match[1])) {
                            $app_linking['ios_location_url'] = sprintf($targets['iOS'], $match[1]);
                            $app_linking['android_location_url'] = sprintf($targets['Android'], $match[1]);
                            $app_linking['app'] = $app_key;
                        }

                        break 2;
                    }
                }
            }
        }

        /* Cloaking */
        $_POST['cloaking_is_enabled'] = isset($_POST['cloaking_is_enabled']) ? (bool) isset($_POST['cloaking_is_enabled']) : $link->settings->cloaking_is_enabled;
        $_POST['cloaking_title'] = isset($_POST['cloaking_title']) ? input_clean($_POST['cloaking_title'], 70) : $link->settings->cloaking_title;;
        $link->settings->cloaking_favicon = \Altum\Uploads::process_upload($link->settings->cloaking_favicon, 'favicons', 'cloaking_favicon', 'cloaking_favicon_remove', settings()->links->favicon_size_limit, 'json_error');

        /* HTTP */
        $_POST['http_status_code'] = isset($_POST['http_status_code']) && in_array($_POST['http_status_code'], [301, 302, 307, 308]) ? (int) $_POST['http_status_code'] : $link->settings->http_status_code;;

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = isset($_POST['targeting_type']) && in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : $link->settings->targeting_type;

        $settings = [
            'schedule' => $_POST['schedule'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'pageviews_limit' => $_POST['pageviews_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'targeting_type' => $_POST['targeting_type'],
            'app_linking_is_enabled' => $_POST['app_linking_is_enabled'],
            'app_linking' => $app_linking,
            'cloaking_is_enabled' => $_POST['cloaking_is_enabled'],
            'cloaking_title' => $_POST['cloaking_title'],
            'cloaking_favicon' => $link->settings->cloaking_favicon,
            'http_status_code' => $_POST['http_status_code'],
        ];

        /* Process the targeting */
        foreach($targeting_types as $targeting_type) {
            ${'targeting_' . $targeting_type} = [];

            if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                foreach ($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                    if(empty(trim($_POST['targeting_' . $targeting_type . '_value'][$key]))) continue;

                    ${'targeting_' . $targeting_type}[] = [
                        'key' => trim(query_clean($value)),
                        'value' => get_url($_POST['targeting_' . $targeting_type . '_value'][$key]),
                    ];
                }

                $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
            }
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('link_id', $link->link_id)->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $_POST['domain_id'],
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $_POST['url'],
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'is_enabled' => $_POST['is_enabled'],
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link_id);
        \Altum\Cache::$adapter->deleteItem('links?user_id=' . $link->user_id);

        /* Prepare the data */
        $data = [
            'id' => $link->link_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }

        /* Delete the resource */
        (new \Altum\Models\Link())->delete($link->link_id);

        http_response_code(200);
        die();

    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            $this->response_error(l('global.error_message.empty_fields'), 401);
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            $this->response_error(l('links.error_message.invalid_location_url'), 401);
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, explode(',', settings()->links->blacklisted_domains))) {
            $this->response_error(l('links.error_message.blacklisted_domain'), 401);
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                $this->response_error(l('links.error_message.blacklisted_location_url'), 401);
            }
        }
    }
}
