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
use Altum\Title;


class AdminSettings extends Controller {

    public function index() {
        redirect('admin/settings/main');
    }

    private function process() {
        $method	= (isset(\Altum\Router::$method) && file_exists(THEME_PATH . 'views/admin/settings/partials/' . \Altum\Router::$method . '.php')) ? \Altum\Router::$method : 'main';
        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Set a custom title */
        Title::set(sprintf(l('admin_settings.title'), l('admin_settings.' . $method . '.tab')));

        /* Method View */
        $view = new \Altum\View('admin/settings/partials/' . $method, (array) $this);
        $this->add_view_content('method', $view->run());

        /* Main View */
        $view = new \Altum\View('admin/settings/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'method' => $method,
            'payment_processors' => $payment_processors,
        ]));
    }

    private function update_settings($key, $value) {
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('`key`', $key)->update('settings', ['value' => $value]);

            $this->after_update_settings($key);
        }

        redirect('admin/settings/' . $key);
    }

    private function after_update_settings($key) {

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('settings');

        /* Set a nice success message */
        Alerts::add_success(l('global.success_message.update2'));

        /* Refresh the page */
        redirect('admin/settings/' . $key);

    }

    public function main() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Make sure there is way to auto redirect yourself to the homepage infinitely */
            if($_POST['index_url']) {
                $site_url_parsed = parse_url(SITE_URL);
                $index_url_parsed = parse_url(settings()->main->index_url);

                if($site_url_parsed['host'] == $index_url_parsed['host'] && ($site_url_parsed['path'] == $index_url_parsed['path'] || $site_url_parsed['path'] == $index_url_parsed['path'] . '/')) {
                    $_POST['index_url'] = null;
                }
            }

            /* Uploads processing */
            foreach(['logo_light', 'logo_dark', 'logo_email', 'favicon', 'opengraph'] as $image_key) {
                settings()->main->{$image_key} = \Altum\Uploads::process_upload(settings()->main->{$image_key}, $image_key, $image_key, $image_key . '_remove', null);
            }

            $_POST['force_https_is_enabled'] = (bool) $_POST['force_https_is_enabled'];
            if(!string_starts_with('https://', SITE_URL)) {
                $_POST['force_https_is_enabled'] = false;
            }

            /* :) */
            $value = json_encode([
                'title' => $_POST['title'],
                'default_language' => $_POST['default_language'],
                'default_theme_style' => $_POST['default_theme_style'],
                'default_timezone' => $_POST['default_timezone'],
                'index_url' => $_POST['index_url'],
                'terms_and_conditions_url' => $_POST['terms_and_conditions_url'],
                'privacy_policy_url' => $_POST['privacy_policy_url'],
                'not_found_url' => $_POST['not_found_url'],
                'se_indexing' => (bool) $_POST['se_indexing'],
                'display_index_plans' => (bool) $_POST['display_index_plans'],
                'display_index_testimonials' => (bool) $_POST['display_index_testimonials'],
                'display_index_faq' => (bool) $_POST['display_index_faq'],
                'default_results_per_page' => (int) $_POST['default_results_per_page'],
                'default_order_type' => $_POST['default_order_type'],
                'auto_language_detection_is_enabled' => (bool) $_POST['auto_language_detection_is_enabled'],
                'blog_is_enabled' => (bool) $_POST['blog_is_enabled'],
                'api_is_enabled' => (bool) $_POST['api_is_enabled'],
                'theme_style_change_is_enabled' => (bool) $_POST['theme_style_change_is_enabled'],
                'logo_light' => settings()->main->logo_light ?? '',
                'logo_dark' => settings()->main->logo_dark ?? '',
                'logo_email' => settings()->main->logo_email ?? '',
                'opengraph' => settings()->main->opengraph ?? '',
                'favicon' => settings()->main->favicon ?? '',
                'openai_api_key' => $_POST['openai_api_key'],
                'openai_model' => $_POST['openai_model'],
                'force_https_is_enabled' => $_POST['force_https_is_enabled'],
                'broadcasts_statistics_is_enabled' => (bool) $_POST['broadcasts_statistics_is_enabled'],
            ]);

            $this->update_settings('main', $value);
        }
    }

    public function users() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_countries'] = $_POST['blacklisted_countries'] ?? [];

            $value = json_encode([
                'email_confirmation' => (bool) $_POST['email_confirmation'],
                'welcome_email_is_enabled' => (bool) $_POST['welcome_email_is_enabled'],
                'register_is_enabled' => (bool) $_POST['register_is_enabled'],
                'register_only_social_logins' => (bool) $_POST['register_only_social_logins'],
                'register_display_newsletter_checkbox' => (bool) $_POST['register_display_newsletter_checkbox'],
                'auto_delete_unconfirmed_users' => (int) $_POST['auto_delete_unconfirmed_users'],
                'auto_delete_inactive_users' => (int) $_POST['auto_delete_inactive_users'],
                'user_deletion_reminder' => (int) $_POST['user_deletion_reminder'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_countries' => $_POST['blacklisted_countries'],
                'login_lockout_is_enabled' => (bool) $_POST['login_lockout_is_enabled'],
                'login_lockout_max_retries' => (int) $_POST['login_lockout_max_retries'] < 1 ? 1 : (int) $_POST['login_lockout_max_retries'],
                'login_lockout_time' => (int) $_POST['login_lockout_time'] < 1 ? 1 : (int) $_POST['login_lockout_time'],
                'lost_password_lockout_is_enabled' => (bool) $_POST['lost_password_lockout_is_enabled'],
                'lost_password_lockout_max_retries' => (int) $_POST['lost_password_lockout_max_retries'] < 1 ? 1 : (int) $_POST['lost_password_lockout_max_retries'],
                'lost_password_lockout_time' => (int) $_POST['lost_password_lockout_time'] < 1 ? 1 : (int) $_POST['lost_password_lockout_time'],
                'resend_activation_lockout_is_enabled' => (bool) $_POST['resend_activation_lockout_is_enabled'],
                'resend_activation_lockout_max_retries' => (int) $_POST['resend_activation_lockout_max_retries'] < 1 ? 1 : (int) $_POST['resend_activation_lockout_max_retries'],
                'resend_activation_lockout_time' => (int) $_POST['resend_activation_lockout_time'] < 1 ? 1 : (int) $_POST['resend_activation_lockout_time'],
                'register_lockout_is_enabled' => (bool) $_POST['register_lockout_is_enabled'],
                'register_lockout_max_registrations' => (int) $_POST['register_lockout_max_registrations'] < 1 ? 1 : (int) $_POST['register_lockout_max_registrations'],
                'register_lockout_time' => (int) $_POST['register_lockout_time'] < 1 ? 1 : (int) $_POST['register_lockout_time'],
            ]);

            $this->update_settings('users', $value);
        }
    }

    public function content() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'blog_is_enabled' => (bool) $_POST['blog_is_enabled'],
                'blog_share_is_enabled' => (bool) $_POST['blog_share_is_enabled'],
                'blog_search_widget_is_enabled' => (bool) $_POST['blog_search_widget_is_enabled'],
                'blog_categories_widget_is_enabled' => (bool) $_POST['blog_categories_widget_is_enabled'],
                'blog_popular_widget_is_enabled' => (bool) $_POST['blog_popular_widget_is_enabled'],
                'blog_views_is_enabled' => (bool) $_POST['blog_views_is_enabled'],

                'pages_is_enabled' => (bool) $_POST['pages_is_enabled'],
                'pages_share_is_enabled' => (bool) $_POST['pages_share_is_enabled'],
                'pages_popular_widget_is_enabled' => (bool) $_POST['pages_popular_widget_is_enabled'],
                'pages_views_is_enabled' => (bool) $_POST['pages_views_is_enabled'],
            ]);

            $this->update_settings('content', $value);
        }
    }

    public function payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['type'] = in_array($_POST['type'], ['one_time', 'recurring', 'both']) ? input_clean($_POST['type']) : 'both';
            $_POST['codes_is_enabled'] = (bool)$_POST['codes_is_enabled'];
            $_POST['taxes_and_billing_is_enabled'] = (bool)$_POST['taxes_and_billing_is_enabled'];
            $_POST['invoice_is_enabled'] = (bool) $_POST['invoice_is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'type' => $_POST['type'],
                'default_payment_frequency' => $_POST['default_payment_frequency'],
                'currency' => $_POST['currency'],
                'codes_is_enabled' => $_POST['codes_is_enabled'],
                'taxes_and_billing_is_enabled' => $_POST['taxes_and_billing_is_enabled'],
                'invoice_is_enabled' => $_POST['invoice_is_enabled'],
                'user_plan_expiry_reminder' => (int) $_POST['user_plan_expiry_reminder'],
            ]);

            $this->update_settings('payment', $value);
        }
    }

    public function paypal() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'client_id' => $_POST['client_id'],
                'secret' => $_POST['secret'],
            ]);

            $this->update_settings('paypal', $value);
        }
    }

    public function stripe() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('stripe', $value);
        }
    }

    public function offline_payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['proof_size_limit'] = $_POST['proof_size_limit'] > get_max_upload() || $_POST['proof_size_limit'] < 0 ? get_max_upload() : (float) $_POST['proof_size_limit'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'instructions' => $_POST['instructions'],
                'proof_size_limit' => $_POST['proof_size_limit'],
            ]);

            $this->update_settings('offline_payment', $value);
        }
    }

    public function coinbase() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('coinbase', $value);
        }
    }

    public function payu() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['secure', 'sandbox']) ? input_clean($_POST['mode']) : 'secure';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'merchant_pos_id' => $_POST['merchant_pos_id'],
                'signature_key' => $_POST['signature_key'],
                'oauth_client_id' => $_POST['oauth_client_id'],
                'oauth_client_secret' => $_POST['oauth_client_secret'],
            ]);

            $this->update_settings('payu', $value);
        }
    }

    public function paystack() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'public_key' => $_POST['public_key'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('paystack', $value);
        }
    }

    public function razorpay() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'key_id' => $_POST['key_id'],
                'key_secret' => $_POST['key_secret'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('razorpay', $value);
        }
    }

    public function mollie() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
            ]);

            $this->update_settings('mollie', $value);
        }
    }

    public function yookassa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'shop_id' => $_POST['shop_id'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('yookassa', $value);
        }
    }

    public function crypto_com() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('crypto_com', $value);
        }
    }

    public function paddle() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'vendor_id' => $_POST['vendor_id'],
                'api_key' => $_POST['api_key'],
                'public_key' => $_POST['public_key'],
            ]);

            $this->update_settings('paddle', $value);
        }
    }

    public function mercadopago() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'access_token' => $_POST['access_token'],
            ]);

            $this->update_settings('mercadopago', $value);
        }
    }

    public function affiliate() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('affiliate')) {
                redirect('admin/settings/affiliate');
            }

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['commission_type'] = in_array($_POST['commission_type'], ['once', 'forever']) ? input_clean($_POST['commission_type']) : 'once';
            $_POST['tracking_type'] = in_array($_POST['tracking_type'], ['first', 'last']) ? input_clean($_POST['tracking_type']) : 'first';
            $_POST['tracking_duration'] = (int) $_POST['tracking_duration'] >= 1 ? (int) $_POST['tracking_duration'] : 30;
            $_POST['minimum_withdrawal_amount'] = (float) $_POST['minimum_withdrawal_amount'];
            $_POST['withdrawal_notes'] = input_clean($_POST['withdrawal_notes']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'commission_type' => $_POST['commission_type'],
                'tracking_type' => $_POST['tracking_type'],
                'tracking_duration' => $_POST['tracking_duration'],
                'minimum_withdrawal_amount' => $_POST['minimum_withdrawal_amount'],
                'withdrawal_notes' => $_POST['withdrawal_notes'],
            ]);

            $this->update_settings('affiliate', $value);
        }
    }

    public function business() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['brand_name'] = input_clean($_POST['brand_name']);

            $value = json_encode([
                'brand_name' => $_POST['brand_name'],
                'invoice_nr_prefix' => $_POST['invoice_nr_prefix'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'county' => $_POST['county'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'tax_type' => $_POST['tax_type'],
                'tax_id' => $_POST['tax_id'],
                'custom_key_one' => $_POST['custom_key_one'],
                'custom_value_one' => $_POST['custom_value_one'],
                'custom_key_two' => $_POST['custom_key_two'],
                'custom_value_two' => $_POST['custom_value_two'],
            ]);

            $this->update_settings('business', $value);
        }
    }

    public function captcha() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['type'] = in_array($_POST['type'], ['basic', 'recaptcha', 'hcaptcha', 'turnstile']) ? $_POST['type'] : 'basic';
            foreach(['login', 'register', 'lost_password', 'resend_activation', 'contact'] as $key) {
                $_POST[$key . '_is_enabled'] = (bool) $_POST[$key . '_is_enabled'];
            }

            /* Check for errors */
            if($_POST['type'] == 'basic') {
                if(!extension_loaded('gd') || !function_exists('gd_info')) {
                    Alerts::add_error(sprintf(l('global.error_message.function_required'), 'GD'));
                }
            }

            $value = json_encode([
                'type' => $_POST['type'],
                'recaptcha_public_key' => $_POST['recaptcha_public_key'],
                'recaptcha_private_key' => $_POST['recaptcha_private_key'],
                'hcaptcha_site_key' => $_POST['hcaptcha_site_key'],
                'hcaptcha_secret_key' => $_POST['hcaptcha_secret_key'],
                'turnstile_site_key' => $_POST['turnstile_site_key'],
                'turnstile_secret_key' => $_POST['turnstile_secret_key'],
                'login_is_enabled' => $_POST['login_is_enabled'],
                'register_is_enabled' => $_POST['register_is_enabled'],
                'lost_password_is_enabled' => $_POST['lost_password_is_enabled'],
                'resend_activation_is_enabled' => $_POST['resend_activation_is_enabled'],
                'contact_is_enabled' => $_POST['contact_is_enabled'],
            ]);

            $this->update_settings('captcha', $value);
        }
    }

    public function facebook() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'app_id' => $_POST['app_id'],
                'app_secret' => $_POST['app_secret'],
            ]);

            $this->update_settings('facebook', $value);
        }
    }

    public function google() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('google', $value);
        }
    }

    public function twitter() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'consumer_api_key' => $_POST['consumer_api_key'],
                'consumer_api_secret' => $_POST['consumer_api_secret'],
            ]);

            $this->update_settings('twitter', $value);
        }
    }

    public function discord() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('discord', $value);
        }
    }

    public function linkedin() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('linkedin', $value);
        }
    }

    public function microsoft() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('microsoft', $value);
        }
    }

    public function ads() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'ad_blocker_detector_is_enabled' => (bool) $_POST['ad_blocker_detector_is_enabled'],
                'ad_blocker_detector_lock_is_enabled' => (bool) $_POST['ad_blocker_detector_lock_is_enabled'],
                'ad_blocker_detector_delay' => (int) $_POST['ad_blocker_detector_delay'],
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
            ]);

            $this->update_settings('ads', $value);
        }
    }

    public function cookie_consent() {
        $this->process();

        /* CSV Export */
        if(isset($_GET['export']) && $_GET['export'] == 'csv') {
            //ALTUMCODE:DEMO if(DEMO) exit('This command is blocked on the demo.');

            header('Content-Disposition: attachment; filename="data.csv";');
            header('Content-Type: application/csv; charset=UTF-8');

            die(file_get_contents(UPLOADS_PATH . 'cookie_consent/data.csv'));
        }

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['logging_is_enabled'] = (bool) $_POST['logging_is_enabled'];
            $_POST['necessary_is_enabled'] = true;
            $_POST['analytics_is_enabled'] = (bool) $_POST['analytics_is_enabled'];
            $_POST['targeting_is_enabled'] = (bool) $_POST['targeting_is_enabled'];
            $_POST['layout'] = in_array($_POST['layout'], ['cloud', 'box', 'bar']) ? $_POST['layout'] : 'cloud';
            $_POST['position_y'] = in_array($_POST['position_y'], ['top', 'middle', 'bottom']) ? $_POST['position_y'] : 'bottom';
            $_POST['position_x'] = in_array($_POST['position_x'], ['left', 'center', 'right']) ? $_POST['position_x'] : 'center';

            if($_POST['logging_is_enabled']) {
                if(!is_writable(UPLOADS_PATH . 'cookie_consent/')) {
                    Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'cookie_consent/'));
                }
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'logging_is_enabled' => $_POST['logging_is_enabled'],
                'necessary_is_enabled' => $_POST['necessary_is_enabled'],
                'analytics_is_enabled' => $_POST['analytics_is_enabled'],
                'targeting_is_enabled' => $_POST['targeting_is_enabled'],
                'layout' => $_POST['layout'],
                'position_y' => $_POST['position_y'],
                'position_x' => $_POST['position_x'],
            ]);

            $this->update_settings('cookie_consent', $value);
        }
    }

    public function socials() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = [];
            foreach(require APP_PATH . 'includes/admin_socials.php' as $key => $social) {
                $value[$key] = $_POST[$key];
            }
            $value = json_encode($value);

            $this->update_settings('socials', $value);
        }
    }

    public function smtp() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['auth'] = (bool) isset($_POST['auth']);
            $_POST['username'] = input_clean($_POST['username'] ?? '');
            $_POST['password'] = $_POST['password'] ?? '';

            $value = json_encode([
                'from_name' => $_POST['from_name'],
                'from' => $_POST['from'],
                'host' => $_POST['host'],
                'encryption' => $_POST['encryption'],
                'port' => $_POST['port'],
                'auth' => $_POST['auth'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ]);

            $this->update_settings('smtp', $value);
        }
    }

    public function theme() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!is_writable(ASSETS_PATH . 'css/custom-bootstrap/')) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), ASSETS_PATH . 'css/custom-bootstrap/'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Process */
                $theme = [];

                /* Go through all the inputs and clean them */
                foreach(['light_ltr', 'light_rtl', 'dark_ltr', 'dark_rtl'] as $mode) {
                    foreach (['25', '50', '100', '200', '300', '400', '500', '600', '700', '800', '900'] as $key) {
                        if(isset($_POST[$mode . '_primary_' . $key])) {
                            $_POST[$mode . '_primary_' . $key] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST[$mode . '_primary_' . $key]) ? '#000000' : $_POST[$mode . '_primary_' . $key];
                            $theme[$mode . '_primary_' . $key] = $_POST[$mode . '_primary_' . $key];
                        }

                        if(isset($_POST[$mode . '_gray_' . $key])) {
                            $_POST[$mode . '_gray_' . $key] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST[$mode . '_gray_' . $key]) ? '#000000' : $_POST[$mode . '_gray_' . $key];
                            $theme[$mode . '_gray_' . $key] = $_POST[$mode . '_gray_' . $key];
                        }
                    }

                    /* Others */
                    $_POST[$mode . '_border_radius'] = isset($_POST[$mode . '_border_radius']) && $_POST[$mode . '_border_radius'] >= 0 && $_POST[$mode . '_border_radius'] <= 1 ? (float) $_POST[$mode . '_border_radius'] : 0.3;
                    $theme[$mode . '_border_radius'] = $_POST[$mode . '_border_radius'];

                    /* Font family */
                    $theme[$mode . '_font_family'] = $_POST[$mode . '_font_family'];

                }

                $css_files = [
                    'bootstrap' => 'light_ltr',
                    'bootstrap-rtl' => 'light_rtl',
                    'bootstrap-dark' => 'dark_ltr',
                    'bootstrap-dark-rtl' => 'dark_rtl',
                ];

                foreach($css_files as $key => $value) {
                    $theme[$value . '_is_enabled'] = (bool) $_POST[$value . '_is_enabled'];

                    if(!$theme[$value . '_is_enabled']) {
                        continue;
                    }

                    /* Initiate SCSS - PHP compiler */
                    $compiler = new \ScssPhp\ScssPhp\Compiler;
                    $compiler->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
                    $compiler->setImportPaths(ASSETS_PATH . 'scss/');

                    /* Get the current SCSS file content */
                    $main_scss_content = file_get_contents(ASSETS_PATH . 'scss/' . $key . '.scss');

                    /* Font family */
                    $font_family = $theme[$value . '_font_family'] ? '$font-family-base: ' . $theme[$value . '_font_family'] . ';' : null;

                    /* Replace the SCSS file content with the custom colors */
                    $main_scss_content = '
                    $primary-50: ' . $_POST[$value . '_primary_50'] . ';
                    $primary-100: ' . $_POST[$value . '_primary_100'] . ';
                    $primary-200: ' . $_POST[$value . '_primary_200'] . ';
                    $primary-300: ' . $_POST[$value . '_primary_300'] . ';
                    $primary-400: ' . $_POST[$value . '_primary_400'] . ';
                    $primary: ' . $_POST[$value . '_primary_500'] . ';
                    $primary-600: ' . $_POST[$value . '_primary_600'] . ';
                    $primary-700: ' . $_POST[$value . '_primary_700'] . ';
                    $primary-800: ' . $_POST[$value . '_primary_800'] . ';
                    $primary-900: ' . $_POST[$value . '_primary_900'] . ';
                    
                    $gray-25: ' . $_POST[$value . '_gray_25'] . ' !default;
                    $gray-50: ' . $_POST[$value . '_gray_50'] . ' !default;
                    $gray-100: ' . $_POST[$value . '_gray_100'] . ' !default;
                    $gray-200: ' . $_POST[$value . '_gray_200'] . ' !default;
                    $gray-300: ' . $_POST[$value . '_gray_300'] . ' !default;
                    $gray-400: ' . $_POST[$value . '_gray_400'] . ' !default;
                    $gray-500: ' . $_POST[$value . '_gray_500'] . ' !default;
                    $gray-600: ' . $_POST[$value . '_gray_600'] . ' !default;
                    $gray-700: ' . $_POST[$value . '_gray_700'] . ' !default;
                    $gray-800: ' . $_POST[$value . '_gray_800'] . ' !default;
                    $gray-900: ' . $_POST[$value . '_gray_900'] . ' !default;
                    
                    ' . $font_family . '
                    ' . strstr($main_scss_content, '/* :) */');

                    $main_scss_content = preg_replace('/border-radius: (.*)rem;/', 'border-radius: ' . $theme[$value . '_border_radius'] . 'rem;', $main_scss_content);

                    /* Compile to CSS */
                    $compiled_css = $compiler->compileString($main_scss_content)->getCss();

                    /* Save the custom CSS file */
                    file_put_contents(ASSETS_PATH . 'css/custom-bootstrap/' . $key . '.min.css', $compiled_css);
                }

                /* :) */
                $value = json_encode($theme);

                $this->update_settings('theme', $value);
            }
        }
    }

    public function custom() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'head_js' => $_POST['head_js'],
                'head_css' => $_POST['head_css'],
            ]);

            $this->update_settings('custom', $value);
        }
    }

    public function announcements() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['guests_id'] = md5($_POST['content'] . time());
            $_POST['guests_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_text_color']) ? '#000' : $_POST['guests_text_color'];
            $_POST['guests_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_background_color']) ? '#fff' : $_POST['guests_background_color'];
            $_POST['users_id'] = md5($_POST['content'] . time());
            $_POST['users_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_text_color']) ? '#000' : $_POST['users_text_color'];
            $_POST['users_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_background_color']) ? '#fff' : $_POST['users_background_color'];

            $value = json_encode([
                'guests_id' => $_POST['guests_id'],
                'guests_content' => $_POST['guests_content'],
                'guests_text_color' => $_POST['guests_text_color'],
                'guests_background_color' => $_POST['guests_background_color'],
                'users_id' => $_POST['users_id'],
                'users_content' => $_POST['users_content'],
                'users_text_color' => $_POST['users_text_color'],
                'users_background_color' => $_POST['users_background_color'],
            ]);

            $this->update_settings('announcements', $value);
        }
    }

    public function internal_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['users_is_enabled'] = (bool) isset($_POST['users_is_enabled']);
            $_POST['admins_is_enabled'] = (bool) isset($_POST['admins_is_enabled']);
            $_POST['new_user'] = (bool) isset($_POST['new_user']);
            $_POST['delete_user'] = (bool) isset($_POST['delete_user']);
            $_POST['new_newsletter_subscriber'] = (bool) isset($_POST['new_newsletter_subscriber']);
            $_POST['new_payment'] = (bool) isset($_POST['new_payment']);
            $_POST['new_affiliate_withdrawal'] = (bool) isset($_POST['new_affiliate_withdrawal']);

            $value = json_encode([
                'users_is_enabled' => $_POST['users_is_enabled'],
                'admins_is_enabled' => $_POST['admins_is_enabled'],
                'new_user' => $_POST['new_user'],
                'delete_user' => $_POST['delete_user'],
                'new_newsletter_subscriber' => $_POST['new_newsletter_subscriber'],
                'new_payment' => $_POST['new_payment'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
            ]);

            $this->update_settings('internal_notifications', $value);
        }
    }

    public function email_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['emails'] = str_replace(' ', '', $_POST['emails']);
            $_POST['new_user'] = (bool) isset($_POST['new_user']);
            $_POST['delete_user'] = (bool) isset($_POST['delete_user']);
            $_POST['new_payment'] = (bool) isset($_POST['new_payment']);
            $_POST['new_domain'] = (bool) isset($_POST['new_domain']);
            $_POST['contact'] = (bool) isset($_POST['contact']);
            $_POST['new_affiliate_withdrawal'] = (bool) isset($_POST['new_affiliate_withdrawal']);

            $value = json_encode([
                'emails' => $_POST['emails'],
                'new_user' => $_POST['new_user'],
                'delete_user' => $_POST['delete_user'],
                'new_payment' => $_POST['new_payment'],
                'new_domain' => $_POST['new_domain'],
                'contact' => $_POST['contact'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
            ]);

            $this->update_settings('email_notifications', $value);
        }
    }

    public function push_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('push-notifications')) {
                redirect('admin/settings/push_notifications');
            }

            /* Uploads processing */
            settings()->push_notifications->icon = \Altum\Uploads::process_upload(settings()->push_notifications->icon, 'push_notifications_icon', 'icon', 'icon' . '_remove', null);

            $value = json_encode([
                'is_enabled' => (bool) $_POST['is_enabled'],
                'guests_is_enabled' => (bool) $_POST['guests_is_enabled'],
                'ask_to_subscribe_is_enabled' => (bool) $_POST['ask_to_subscribe_is_enabled'],
                'ask_to_subscribe_delay' => (int) $_POST['ask_to_subscribe_delay'],
                'icon' => settings()->push_notifications->icon ?? '',
                'public_key' => settings()->push_notifications->public_key,
                'private_key' => settings()->push_notifications->private_key,
            ]);

            $this->update_settings('push_notifications', $value);
        }
    }

    public function webhooks() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['user_new'] = input_clean($_POST['user_new']);
            $_POST['user_delete'] = input_clean($_POST['user_delete']);
            $_POST['payment_new'] = input_clean($_POST['payment_new']);
            $_POST['code_redeemed'] = input_clean($_POST['code_redeemed']);
            $_POST['contact'] = input_clean($_POST['contact']);
            $_POST['domain_new'] = input_clean($_POST['domain_new']);
            $_POST['domain_update'] = input_clean($_POST['domain_update']);

            $value = json_encode([
                'user_new' => $_POST['user_new'],
                'user_delete' => $_POST['user_delete'],
                'payment_new' => $_POST['payment_new'],
                'code_redeemed' => $_POST['code_redeemed'],
                'contact' => $_POST['contact'],
                'domain_new' => $_POST['domain_new'],
                'domain_update' => $_POST['domain_update'],
            ]);

            $this->update_settings('webhooks', $value);
        }
    }

    public function offload() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('offload')) {
                redirect('admin/settings/offload');
            }

            /* :) */
            $_POST['assets_url'] = trim(input_clean($_POST['assets_url']));

            $value = json_encode([
                'cdn_uploads_url' => $_POST['cdn_uploads_url'],
                'cdn_assets_url' => $_POST['cdn_assets_url'],
                'assets_url' => $_POST['assets_url'],
                'provider' => $_POST['provider'],
                'endpoint_url' => $_POST['endpoint_url'],
                'uploads_url' => $_POST['uploads_url'],
                'access_key' => $_POST['access_key'],
                'secret_access_key' => $_POST['secret_access_key'],
                'storage_name' => $_POST['storage_name'],
                'region' => $_POST['region'],
            ]);

            $this->update_settings('offload', $value);
        }
    }

    public function pwa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('pwa')) {
                redirect('admin/settings/pwa');
            }

            if(!is_writable(UPLOADS_PATH . \Altum\Uploads::get_path('pwa'))) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . \Altum\Uploads::get_path('pwa')));
            }

            /* :) */
            $_POST['app_name'] = input_clean($_POST['app_name']);
            $_POST['short_app_name'] = input_clean($_POST['short_app_name']);
            $_POST['app_description'] = input_clean($_POST['app_description']);
            $_POST['theme_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['theme_color']) ? '#ffffff' : $_POST['theme_color'];
            $_POST['app_start_url'] = trim(filter_var($_POST['app_start_url'], FILTER_SANITIZE_URL));
            if(empty($_POST['app_start_url']) || !string_starts_with(SITE_URL, $_POST['app_start_url'])) {
                $_POST['app_start_url'] = SITE_URL;
            }

            /* App icons */
            settings()->pwa->app_icon = \Altum\Uploads::process_upload(settings()->pwa->app_icon, 'app_icon', 'app_icon', 'app_icon_remove', null);
            settings()->pwa->app_icon_maskable = \Altum\Uploads::process_upload(settings()->pwa->app_icon_maskable, 'app_icon', 'app_icon_maskable', 'app_icon_maskable_remove', null);

            $value = [
                'is_enabled' => (bool) $_POST['is_enabled'],
                'app_name' => $_POST['app_name'],
                'short_app_name' => $_POST['short_app_name'],
                'app_description' => $_POST['app_description'],
                'theme_color' => $_POST['theme_color'],
                'app_start_url' => $_POST['app_start_url'],
                'app_icon' => settings()->pwa->app_icon ?? '',
                'app_icon_maskable' => settings()->pwa->app_icon_maskable ?? '',
            ];

            /* Screenshots */
            $mobile_screenshots = [];
            $desktop_screenshots = [];
            foreach([1, 2, 3, 4, 5, 6] as $key) {
                /* Mobile */
                settings()->pwa->{'mobile_screenshot_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'mobile_screenshot_' .  $key}, 'app_screenshots', 'mobile_screenshot_' .  $key, 'mobile_screenshot_' .  $key . '_remove', null);
                $value['mobile_screenshot_' .  $key] = settings()->pwa->{'mobile_screenshot_' .  $key};

                if($value['mobile_screenshot_' .  $key]) {
                    $mobile_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['mobile_screenshot_' .  $key];
                }

                /* Desktop */
                settings()->pwa->{'desktop_screenshot_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'desktop_screenshot_' .  $key}, 'app_screenshots', 'desktop_screenshot_' .  $key, 'desktop_screenshot_' .  $key . '_remove', null);
                $value['desktop_screenshot_' .  $key] = settings()->pwa->{'desktop_screenshot_' .  $key};

                if($value['desktop_screenshot_' .  $key]) {
                    $desktop_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['desktop_screenshot_' .  $key];
                }
            }

            /* Shortcuts */
            $shortcuts = [];
            foreach([1, 2, 3] as $key) {
                $value['shortcut_name_' . $key] = input_clean($_POST['shortcut_name_' . $key]);
                $value['shortcut_description_' . $key] = input_clean($_POST['shortcut_description_' . $key]);

                if(empty($_POST['shortcut_url_' . $key]) || !string_starts_with(SITE_URL, $_POST['shortcut_url_' . $key])) {
                    $_POST['shortcut_url_' . $key] = SITE_URL;
                }
                $value['shortcut_url_' . $key] = trim(filter_var($_POST['shortcut_url_' . $key], FILTER_SANITIZE_URL));

                settings()->pwa->{'shortcut_icon_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'shortcut_icon_' .  $key}, 'app_screenshots', 'shortcut_icon_' .  $key, 'shortcut_icon_' .  $key . '_remove', null);
                $value['shortcut_icon_' .  $key] = settings()->pwa->{'shortcut_icon_' .  $key};

                if($value['shortcut_icon_' .  $key]) {
                    $desktop_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['shortcut_icon_' .  $key];
                }

                $shortcuts[] = [
                    'name' => $value['shortcut_name_' . $key],
                    'description' => $value['shortcut_description_' . $key],
                    'url' => $value['shortcut_url_' . $key],
                    'icon_url' => $value['shortcut_icon_' .  $key] ? \Altum\Uploads::get_full_url('app_screenshots') . $value['shortcut_icon_' .  $key] : null,
                ];
            }

            /* Generate the manifest file */
            $manifest = pwa_generate_manifest([
                'name' => $_POST['app_name'],
                'short_name' => $_POST['short_app_name'],
                'description' => $_POST['app_description'],
                'theme_color' => $_POST['theme_color'],
                'app_icon_url' => settings()->pwa->app_icon ? \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon : null,
                'app_icon_maskable_url' => settings()->pwa->app_icon_maskable ? \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon_maskable : null,
                'start_url' => $_POST['app_start_url'],
                'mobile_screenshots' => $mobile_screenshots,
                'desktop_screenshots' => $desktop_screenshots,
                'shortcuts' => $shortcuts,
            ]);
            pwa_save_manifest($manifest);

            $this->update_settings('pwa', json_encode($value));
        }
    }

    public function cron() {
        /* Get the latest cronjob details */
        settings()->cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', '`value`'));

        $this->process();
    }

    public function cache() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            \Altum\Cache::$adapter->clear();

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

            /* Refresh the page */
            redirect('admin/settings/cache');
        }
    }

    public function license() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_license'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $altumcode_api = 'https://api.altumcode.com/validate';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'type'              => 'update',
                'license'           => $_POST['new_license'],
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        $dump = explode('-- SEPARATOR --', $response->body->sql);

                        foreach ($dump as $query) {
                            database()->query($query);
                        }
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('license');
                }
            }

            redirect('admin/settings/license');
        }
    }

    public function support() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            //$altumcode_api = 'http://127.0.0.1/altumcode-api/get-support-status';
            $altumcode_api = 'https://api.altumcode.com/get-support-status';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'license'           => settings()->license->license,
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        database()->query($response->body->sql);
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('support');
                }
            }

            redirect('admin/settings/support');
        }
    }

    public function links() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['random_url_length'] = isset($_POST['random_url_length']) && $_POST['random_url_length'] < 4 ? 4 : (int) $_POST['random_url_length'];
            $_POST['qr_code_reader_is_enabled'] = (bool) $_POST['qr_code_reader_is_enabled'];
            $_POST['domains_is_enabled'] = (bool) $_POST['domains_is_enabled'];
            $_POST['additional_domains_is_enabled'] = (bool) $_POST['additional_domains_is_enabled'];
            $_POST['main_domain_is_enabled'] = (bool) $_POST['main_domain_is_enabled'];
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_keywords'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_keywords'])));
            $_POST['google_safe_browsing_is_enabled'] = (bool) $_POST['google_safe_browsing_is_enabled'];
            $_POST['favicon_size_limit'] = $_POST['favicon_size_limit'] > get_max_upload() || $_POST['favicon_size_limit'] < 0 ? get_max_upload() : (float) $_POST['favicon_size_limit'];

            $value = json_encode([
                'random_url_length' => $_POST['random_url_length'],
                'qr_code_reader_is_enabled' => $_POST['qr_code_reader_is_enabled'],
                'domains_is_enabled' => $_POST['domains_is_enabled'],
                'additional_domains_is_enabled' => $_POST['additional_domains_is_enabled'],
                'main_domain_is_enabled' => $_POST['main_domain_is_enabled'],
                'domains_custom_main_ip' => $_POST['domains_custom_main_ip'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_keywords' => $_POST['blacklisted_keywords'],
                'google_safe_browsing_is_enabled' => $_POST['google_safe_browsing_is_enabled'],
                'google_safe_browsing_api_key' => $_POST['google_safe_browsing_api_key'],
                'favicon_size_limit' => $_POST['favicon_size_limit'],
            ]);

            $this->update_settings('links', $value);
        }
    }

    public function send_test_email() {

        if(empty($_POST)) {
            redirect('admin/settings/smtp');
        }

        /* Check for any errors */
        $required_fields = ['email'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Alerts::add_field_error($field, l('global.error_message.empty_field'));
            }
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* If there are no errors, continue */
        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $result = send_mail($_POST['email'], settings()->main->title . ' - Test Email', 'This is just a test email to confirm that the smtp email settings are properly working!', null, [], true);

            if($result->ErrorInfo == '') {
                Alerts::add_success(l('admin_settings_send_test_email_modal.success_message'));
            } else {
                Alerts::add_error(sprintf(l('admin_settings_send_test_email_modal.error_message'), $result->ErrorInfo));
                Alerts::add_info(implode('<br />', $result->errors));
            }

        }

        redirect('admin/settings/smtp');
    }

}
