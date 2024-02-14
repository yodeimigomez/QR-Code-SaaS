<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\Domain;
use Altum\Response;
use Altum\Traits\Apiable;

class AdminApiDomains extends Controller {
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
        $filters->set_default_order_by('domain_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);
        $filters->process();

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains`")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/domains?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `domains`
            WHERE
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->domain_id,
                'user_id' => (int) $row->user_id,
                'scheme' => $row->scheme,
                'host' => $row->host,
                'custom_index_url' => $row->custom_index_url,
                'custom_not_found_url' => $row->custom_not_found_url,
                'is_enabled' => (bool) $row->is_enabled,
                'last_datetime' => $row->last_datetime,
                'datetime' => $row->datetime,
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

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $domain->domain_id,
            'user_id' => (int) $domain->user_id,
            'scheme' => $domain->scheme,
            'host' => $domain->host,
            'custom_index_url' => $domain->custom_index_url,
            'custom_not_found_url' => $domain->custom_not_found_url,
            'is_enabled' => (bool) $domain->is_enabled,
            'last_datetime' => $domain->last_datetime,
            'datetime' => $domain->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for any errors */
        $required_fields = ['host'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        if(db()->where('host', $_POST['host'])->where('is_enabled', 1)->has('domains')) {
            $this->response_error(l('domains.error_message.host_exists'), 401);
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? $_POST['scheme'] : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host'] ?? null));
        $_POST['custom_index_url'] = trim(filter_var($_POST['custom_index_url'] ?? null, FILTER_SANITIZE_URL));
        $_POST['custom_not_found_url'] = trim(filter_var($_POST['custom_not_found_url'] ?? null, FILTER_SANITIZE_URL));
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : 1;
        $type = 1;

        /* Prepare the statement and execute query */
        $domain_id = db()->insert('domains', [
            'user_id' => $this->api_user->user_id,
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'is_enabled' => $_POST['is_enabled'],
            'type' => $type,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItems(['domains?user_id=' . $this->api_user->user_id, 'domains_total?user_id=' . $this->api_user->user_id]);
        \Altum\Cache::$adapter->deleteItemsByTag('domains?user_id=' . $this->api_user->user_id);
        \Altum\Cache::$adapter->deleteItem('available_additional_domains');

        /* Prepare the data */
        $data = [
            'id' => $domain_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->where('user_id', $this->api_user->user_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            $this->return_404();
        }

        if($domain->host != $_POST['host'] && db()->where('host', $_POST['host'])->where('is_enabled', 1)->has('domains')) {
            $this->response_error(l('domains.error_message.host_exists'), 401);
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? $_POST['scheme'] : $domain->scheme;
        $_POST['host'] = mb_strtolower(trim($_POST['host'] ?? null));
        $_POST['custom_index_url'] = trim(filter_var($_POST['custom_index_url'] ?? $domain->custom_index_url, FILTER_SANITIZE_URL));
        $_POST['custom_not_found_url'] = trim(filter_var($_POST['custom_not_found_url'] ?? $domain->custom_not_found_url, FILTER_SANITIZE_URL));
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : $domain->is_enabled;

        /* Database query */
        db()->where('domain_id', $domain->domain_id)->update('domains', [
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'is_enabled' => $_POST['is_enabled'],
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('domains?user_id=' . $domain->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain->domain_id);
        \Altum\Cache::$adapter->deleteItem('available_additional_domains');

        /* Prepare the data */
        $data = [
            'id' => $domain->domain_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            $this->return_404();
        }

        (new Domain())->delete($domain->domain_id);

        http_response_code(200);
        die();

    }

}
