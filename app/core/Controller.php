<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\Page;
use Altum\Traits\Paramsable;

class Controller {
    use Paramsable;

    public $views = [];

    public function __construct(Array $params = []) {

        $this->add_params($params);

    }

    public function add_view_content($name, $data) {

        $this->views[$name] = $data;

    }

    public function run() {

        /* Do we need to show something? */
        if(!\Altum\Router::$controller_settings['has_view']) {
            return;
        }

        if(\Altum\Router::$path == 'l') {
            $wrapper = new \Altum\View('l/wrapper', (array) $this);
        }

        if(\Altum\Router::$path == '') {

            /* Get the top menu custom pages */
            $top_pages = settings()->content->pages_is_enabled ? (new Page())->get_pages('top') : [];

            /* Get the footer pages */
            $bottom_pages = settings()->content->pages_is_enabled ? (new Page())->get_pages('bottom') : [];

            /* Custom wrapper condition for plan pages */
            if(in_array(\Altum\Router::$controller_key, ['plan', 'contact', 'affiliate'])) {
                \Altum\Router::$controller_settings['wrapper'] = \Altum\Authentication::check() ? 'app_wrapper' : 'wrapper';
            }

            /* Normal wrapper ( not logged in ) */
            if(\Altum\Router::$controller_settings['wrapper'] == 'wrapper') {

                /* Establish the menu view */
                $menu = new \Altum\View('partials/menu', (array) $this);
                $this->add_view_content('menu', $menu->run(['pages' => $top_pages]));

                /* Establish the footer view */
                $footer = new \Altum\View('partials/footer', (array) $this);
                $this->add_view_content('footer', $footer->run(['pages' => $bottom_pages]));
            } else

            /* App wrapper logged in users */
            if(\Altum\Router::$controller_settings['wrapper'] == 'app_wrapper') {

                $sidebar = new \Altum\View('partials/app_sidebar', (array) $this);
                $this->add_view_content('app_sidebar', $sidebar->run(['pages' => $top_pages]));

                $menu = new \Altum\View('partials/app_menu', (array) $this);
                $this->add_view_content('app_menu', $menu->run(['pages' => $top_pages]));

                /* Establish the footer view */
                $footer = new \Altum\View('partials/footer', (array) $this);
                $this->add_view_content('footer', $footer->run(['pages' => $bottom_pages]));

            }

            $wrapper = new \Altum\View(\Altum\Router::$controller_settings['wrapper'], (array) $this);
        }

        if(\Altum\Router::$path == 'admin') {
            /* Establish the side menu view */
            $sidebar = new \Altum\View('admin/partials/admin_sidebar', (array) $this);
            $this->add_view_content('admin_sidebar', $sidebar->run());

            /* Establish the top menu view */
            $menu = new \Altum\View('admin/partials/admin_menu', (array) $this);
            $this->add_view_content('admin_menu', $menu->run());

            /* Establish the footer view */
            $footer = new \Altum\View('admin/partials/footer', (array) $this);
            $this->add_view_content('footer', $footer->run());

            $wrapper = new \Altum\View('admin/wrapper', (array) $this);
        }

        if(isset($wrapper)) {
            echo $wrapper->run();
        }
    }


}
