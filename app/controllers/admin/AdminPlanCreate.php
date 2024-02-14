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

class AdminPlanCreate extends Controller {

    public function index() {

        if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
            /* Get the available taxes from the system */
            $taxes = db()->get('taxes');

            /* Get the available codes from the system */
            $codes = db()->get('codes');
        }

        $additional_domains = db()->where('is_enabled', 1)->where('type', 1)->get('domains');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = input_clean($_POST['name']);
            $_POST['description'] = input_clean($_POST['description']);
            $_POST['monthly_price'] = (float) $_POST['monthly_price'];
            $_POST['annual_price'] = (float) $_POST['annual_price'];
            $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];

            /* Determine the enabled QR codes */
            $enabled_qr_codes = [];

            foreach(array_keys((require APP_PATH . 'includes/qr_code.php')['type']) as $key) {
                $enabled_qr_codes[$key] = (bool) isset($_POST['enabled_qr_codes']) && in_array($key, $_POST['enabled_qr_codes']);
            }

            $_POST['settings'] = [
                'qr_codes_limit'                    => (int) $_POST['qr_codes_limit'],
                'links_limit'                       => (int) $_POST['links_limit'],
                'projects_limit'                    => (int) $_POST['projects_limit'],
                'pixels_limit'                      => (int) $_POST['pixels_limit'],
                'domains_limit'                     => (int) $_POST['domains_limit'],
                'teams_limit'                       => (int) $_POST['teams_limit'],
                'team_members_limit'                => (int) $_POST['team_members_limit'],
                'statistics_retention'              => (int) $_POST['statistics_retention'],
                'additional_domains'                => $_POST['additional_domains'] ?? [],
                'analytics_is_enabled'              => (bool) isset($_POST['analytics_is_enabled']),
                'custom_url_is_enabled'             => (bool) isset($_POST['custom_url_is_enabled']),
                'password_protection_is_enabled'    => (bool) isset($_POST['password_protection_is_enabled']),
                'sensitive_content_is_enabled'      => (bool) isset($_POST['sensitive_content_is_enabled']),
                'cloaking_is_enabled'               => (bool) isset($_POST['cloaking_is_enabled']),
                'app_linking_is_enabled'            => (bool) isset($_POST['app_linking_is_enabled']),
                'api_is_enabled'                    => (bool) isset($_POST['api_is_enabled']),
                'affiliate_commission_percentage'   => (int) $_POST['affiliate_commission_percentage'],
                'no_ads'                            => (bool) isset($_POST['no_ads']),
                'qr_reader_is_enabled'              => (bool) isset($_POST['qr_reader_is_enabled']),
                'enabled_qr_codes'                  => $enabled_qr_codes,
            ];

            $_POST['settings'] = json_encode($_POST['settings']);

            $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? null : $_POST['color'];
            $_POST['status'] = (int) $_POST['status'];
            $_POST['order'] = (int) $_POST['order'];
            $_POST['trial_days'] = (int) $_POST['trial_days'];
            $_POST['taxes_ids'] = json_encode($_POST['taxes_ids'] ?? []);
            $_POST['codes_ids'] = json_encode($_POST['codes_ids'] ?? []);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('plans', [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'monthly_price' => $_POST['monthly_price'],
                    'annual_price' => $_POST['annual_price'],
                    'lifetime_price' => $_POST['lifetime_price'],
                    'settings' => $_POST['settings'],
                    'taxes_ids' => $_POST['taxes_ids'],
                    'codes_ids' => $_POST['codes_ids'],
                    'color' => $_POST['color'],
                    'status' => $_POST['status'],
                    'order' => $_POST['order'],
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('plans');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('admin/plans');
            }
        }


        /* Main View */
        $data = [
            'taxes' => $taxes ?? null,
            'codes' => $codes ?? null,
            'additional_domains' => $additional_domains,
        ];

        $view = new \Altum\View('admin/plan-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
