<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Date;
use Altum\Models\User;
use Altum\Title;
use MaxMind\Db\Reader;

class Link extends Controller {
    public $link = null;
    public $link_user = null;
    public $has_access = null;

    public function index() {

        $this->link = \Altum\Router::$data['link'];

        /* Make sure the link is active */
        if(!$this->link->is_enabled) {
            redirect();
        }

        $this->link_user = (new User())->get_user_by_user_id($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->link_user->status != 1) {
            redirect();
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($this->link_user);

        /* Parse some details */
        foreach(['settings', 'pixels_ids'] as $key) {
            $this->link->{$key} = json_decode($this->link->{$key});
        }

        /* Check for temporary URL */
        if(isset($this->link->settings->pageviews_limit) && $this->link->settings->pageviews_limit) {
            $current_pageviews = db()->where('link_id', $this->link->link_id)->getValue('links', 'pageviews');
        }

        if (
            (
                $this->link->settings->schedule && !empty($this->link->settings->start_date) && !empty($this->link->settings->end_date) &&
                (
                    \Altum\Date::get('', null) < \Altum\Date::get($this->link->settings->start_date, null, \Altum\Date::$default_timezone) ||
                    \Altum\Date::get('', null) > \Altum\Date::get($this->link->settings->end_date, null, \Altum\Date::$default_timezone)
                )
            )
            || (isset($current_pageviews) && $current_pageviews >= $this->link->settings->pageviews_limit)
        ) {
            if($this->link->settings->expiration_url) {
                header('Location: ' . $this->link->settings->expiration_url, true, $this->link->settings->http_status_code ?? 301);
                die();
            } else {
                redirect();
            }
        }

        /* Check if the user has access to the link */
        $this->has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->link_user->plan_settings->password_protection_is_enabled) {
            $this->has_access = true;
        }

        /* Set the default language of the user, including the link timezone */
        \Altum\Language::set_by_name($this->link_user->language);

        /* Check if the password form is submitted */
        if(!$this->has_access && !empty($_POST)) {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                Alerts::add_field_error('password', l('l_link.password.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->link_user->plan_settings->sensitive_content_is_enabled) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Display the password form */
        if(!$this->has_access) {
            /* Set a custom title */
            Title::set(l('l_link.password.title'));

            /* Main View */
            $data = [
                'link' => $this->link,
            ];

            $view = new \Altum\View('l/partials/password', (array) $this);
            $this->add_view_content('content', $view->run($data));
        }

        else if(!$can_see_content) {

            /* Set a custom title */
            Title::set(l('l_link.sensitive_content.title'));

            /* Main View */
            $view = new \Altum\View('l/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        /* No password or access granted */
        else {

            $this->create_statistics();
            $this->process_redirect();

        }

    }

    /* Insert statistics log */
    private function create_statistics() {

        $cookie_name = 'l_statistics_' . $this->link->link_id;

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if(!$this->link_user->plan_settings->analytics_is_enabled) {
            return;
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        try {
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
        } catch(\Exception $exception) {
            /* :) */
        }
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

        if(!isset($referrer)) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if the referrer comes from the same location */
        if(isset($referrer) && isset($referrer['host']) && $referrer['host'] == parse_url($this->link->full_url)['host']) {
            $is_unique = 0;

            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && in_array($_GET['referrer'], ['qr', 'link'])) {
            $referrer = [
                'host' => $_GET['referrer'],
                'path' => null
            ];
        }

        $utm_source = $_GET['utm_source'] ?? null;
        $utm_medium = $_GET['utm_medium'] ?? null;
        $utm_campaign = $_GET['utm_campaign'] ?? null;

        /* Insert the log */
        db()->insert('statistics', [
            'link_id' => $this->link->link_id,
            'country_code' => $country_code,
            'city_name' => $city_name,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'referrer_host' => $referrer['host'],
            'referrer_path' => $referrer['path'],
            'device_type' => $device_type,
            'browser_language' => $browser_language,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'is_unique' => $is_unique,
            'datetime' => Date::$date,
        ]);

        /* Add the unique hit to the link table as well */
        db()->where('link_id', $this->link->link_id)->update('links', ['pageviews' => db()->inc()]);

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        /* Check for targeting */
        if($this->link->settings->targeting_type == 'country_code') {
            /* Detect the location */
            try {
                $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get(get_ip());
            } catch(\Exception $exception) {
                /* :) */
            }
            $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($country_code == $value->key) {
                    $this->redirect_to(
                        $value->value,
                        $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                        $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
                    );
                }
            }
        }

        if($this->link->settings->targeting_type == 'device_type') {
            $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($device_type == $value->key) {
                    $this->redirect_to(
                        $value->value,
                        $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                        $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
                    );
                }
            }
        }

        if($this->link->settings->targeting_type == 'browser_language') {
            $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($browser_language == $value->key) {
                    $this->redirect_to(
                        $value->value,
                        $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                        $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
                    );
                }
            }
        }

        if($this->link->settings->targeting_type == 'rotation') {
            $total_chances = 0;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                $total_chances += $value->key;
            }

            $chosen_winner = rand(0, $total_chances);

            $start = 0;
            $end = 0;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                $end += $value->key;

                if($chosen_winner >= $start && $chosen_winner <= $end) {
                    $this->redirect_to(
                        $value->value,
                        $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                        $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
                    );
                }

                $start += $value->key;
            }
        }

        if($this->link->settings->targeting_type == 'os_name') {
            /* Detect extra details about the user */
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            $os_name = $whichbrowser->os->name ?? null;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($os_name == $value->key) {
                    $this->redirect_to(
                        $value->value,
                        $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                        $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
                    );
                }
            }
        }

        /* :) */
        $this->redirect_to(
            $this->link->location_url,
            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
            $this->user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled ? $this->link->settings->app_linking : false,
        );
    }

    private function redirect_to($location_url, $cloaking = false, $app_linking = false) {
        /* App deep linking automatic detection */
        if($app_linking) {
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            $os_name = $whichbrowser->os->name ?? null;

            if($os_name == 'iOS') {
                $location_url = $app_linking->ios_location_url;
            }

            if($os_name == 'Android') {
                $location_url = $app_linking->android_location_url;
            }
        }

        if(count($this->link->pixels_ids) || $cloaking) {

            /* Get the needed pixels */
            $pixels = count($this->link->pixels_ids) ? (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids_and_user_id($this->link->pixels_ids, $this->link->user_id) : [];

            /* Prepare the pixels view */
            $pixels_view = new \Altum\View('l/partials/pixels');
            $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels]));

            /* Prepare the view */
            $pixels_redirect_wrapper = new \Altum\View('l/pixels_redirect_wrapper', (array) $this);
            echo $pixels_redirect_wrapper->run(['location_url' => $location_url, 'cloaking' => $cloaking, 'pixels' => $pixels]);
            die();
        } else {
            header('Location: ' . $location_url, true, $this->link->settings->http_status_code ?? 301); die();
        }
    }
}
