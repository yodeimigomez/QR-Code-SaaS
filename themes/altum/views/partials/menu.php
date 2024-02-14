<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-gray-200 <?= \Altum\Router::$controller_key == 'index' ? null : 'mb-6' ?>">
    <div class="container">
        <a
            href="<?= url() ?>"
            class="navbar-brand"
            data-logo
            data-light-value="<?= settings()->main->logo_light != '' ? \Altum\Uploads::get_full_url('logo_light') . settings()->main->logo_light : settings()->main->title ?>"
            data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid navbar-logo' : '' ?>"
            data-light-tag="<?= settings()->main->logo_light != '' ? 'img' : 'span' ?>"
            data-dark-value="<?= settings()->main->logo_dark != '' ? \Altum\Uploads::get_full_url('logo_dark') . settings()->main->logo_dark : settings()->main->title ?>"
            data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid navbar-logo' : '' ?>"
            data-dark-tag="<?= settings()->main->logo_dark != '' ? 'img' : 'span' ?>"
        >
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                <img src="<?= \Altum\Uploads::get_full_url('logo_' . \Altum\ThemeStyle::get()) . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
            <?php else: ?>
                <?= settings()->main->title ?>
            <?php endif ?>
        </a>

        <button class="btn navbar-custom-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= l('global.accessibility.toggle_navigation') ?>">
            <i class="fas fa-fw fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav">

                <?php if(\Altum\Authentication::check() || (!\Altum\Authentication::check() && settings()->plan_guest->status)): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= \Altum\Authentication::check() ? url('qr-code-create') : url('qr') ?>"><?= l('qr.menu') ?></a></li>

                    <?php if(settings()->links->qr_code_reader_is_enabled): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('qr-reader') ?>"><?= l('qr_reader.menu') ?></a></li>
                    <?php endif ?>
                <?php endif ?>

                <?php foreach($data->pages as $data): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                <?php endforeach ?>

                <?php if(settings()->payment->is_enabled): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('plan') ?>"> <?= l('plan.menu') ?></a></li>
                <?php endif ?>

                <?php if(\Altum\Authentication::check()): ?>

                    <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= l('dashboard.menu') ?></a></li>

                    <?php if(settings()->internal_notifications->users_is_enabled): ?>
                        <li class="nav-item dropdown" id="internal_notifications">
                            <a id="internal_notifications_link" href="#" class="nav-link dropdown-toggle dropdown-toggle-simple" data-internal-notifications="user" data-tooltip data-tooltip-hide-on-click title="<?= l('internal_notifications.menu') ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="window">
                                <span class="fa-layers fa-fw">
                                    <i class="fas fa-fw fa-bell "></i>
                                    <?php if($this->user->has_pending_internal_notifications): ?>
                                        <span class="fa-layers-counter text-danger internal-notification-icon">&nbsp;</span>
                                    <?php endif ?>
                                </span>
                            </a>

                            <div id="internal_notifications_content" class="dropdown-menu dropdown-menu-right px-4 py-2" style="width: 550px;max-width: 550px;"></div>
                        </li>

                        <?php include_view(THEME_PATH . 'views/partials/internal_notifications_js.php', ['has_pending_internal_notifications' => $this->user->has_pending_internal_notifications]) ?>
                    <?php endif ?>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_gravatar($this->user->email) ?>" class="navbar-avatar mr-1" loading="lazy" />
                            <span class="align-middle"><?= $this->user->name ?></span>
                            <span class="align-middle"><i class="fas fa-fw fa-sm fa-caret-down"></i></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if(!\Altum\Teams::is_delegated()): ?>
                                <?php if(\Altum\Authentication::is_admin()): ?>
                                    <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fas fa-fw fa-sm fa-fingerprint mr-2"></i> <?= l('global.menu.admin') ?></a>
                                    <div class="dropdown-divider"></div>
                                <?php endif ?>

                                <a class="dropdown-item" href="<?= url('account') ?>"><i class="fas fa-fw fa-sm fa-wrench mr-2"></i> <?= l('account.menu') ?></a>

                                <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fas fa-fw fa-sm fa-box-open mr-2"></i> <?= l('account_plan.menu') ?></a>
                                <?php if(settings()->payment->is_enabled): ?>
                                    <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fas fa-fw fa-sm fa-credit-card mr-2"></i> <?= l('account_payments.menu') ?></a>

                                    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                                        <a class="dropdown-item" href="<?= url('referrals') ?>"><i class="fas fa-fw fa-sm fa-wallet mr-2"></i> <?= l('referrals.menu') ?></a>
                                    <?php endif ?>
                                <?php endif ?>

                                <?php if(settings()->main->api_is_enabled): ?>
                                    <a class="dropdown-item" href="<?= url('account-api') ?>"><i class="fas fa-fw fa-sm fa-code mr-2"></i> <?= l('account_api.menu') ?></a>
                                <?php endif ?>

                                <?php if(\Altum\Plugin::is_active('teams')): ?>
                                    <a class="dropdown-item" href="<?= url('teams-system') ?>"><i class="fas fa-fw fa-sm fa-user-shield mr-2"></i> <?= l('teams_system.menu') ?></a>
                                <?php endif ?>
                            <?php endif ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-fw fa-sm fa-sign-out-alt mr-2"></i> <?= l('global.menu.logout') ?></a>
                        </div>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link text-primary" href="<?= url('login') ?>">
                            <i class="fas fa-sign-in-alt fa-fw fa-sm mr-2"></i>
                            <?= l('login.menu') ?>
                        </a>
                    </li>

                    <?php if(settings()->users->register_is_enabled): ?>
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="<?= url('register') ?>">
                                <i class="fas fa-user-plus fa-fw fa-sm mr-2"></i>
                                <?= l('register.menu') ?>
                            </a>
                        </li>
                    <?php endif ?>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
