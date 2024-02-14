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

class Qr extends Controller {

    public function index() {

        $qr_code_settings = require APP_PATH . 'includes/qr_code.php';

        $type = isset($this->params[0]) && array_key_exists($this->params[0], $qr_code_settings['type']) ? $this->params[0] : null;

        if($type) {
            if(!$this->user->plan_settings->enabled_qr_codes->{$type}) {
                Alerts::add_info(l('global.info_message.plan_feature_no_access'));
                redirect('qr');
            }

            /* Set a custom title */
            Title::set(sprintf(l('qr.title_dynamic'), l('qr_codes.type.' . $type)));

            if($type == 'url' && \Altum\Authentication::check()) {
                /* Existing links */
                $links = (new \Altum\Models\Link())->get_full_links_by_user_id($this->user->user_id);
            }

            /* Process dynamic view */
            $data = [
                'qr_code_settings' => $qr_code_settings,
                'links' => $links ?? [],
            ];
            $view = new \Altum\View('qr/partials/' . $type . '_form', (array) $this);
            $this->add_view_content('qr_form', $view->run($data));

            $view = new \Altum\View('qr/partials/' . $type . '_content', (array) $this);
            $this->add_view_content('qr_content', $view->run($data));
        }

        /* Main View */
        $data = [
            'type' => $type,
            'qr_code_settings' => $qr_code_settings,
            'links' => $links ?? [],
        ];

        $view = new \Altum\View('qr/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
