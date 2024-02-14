<?php defined('ALTUMCODE') || die() ?>

<footer class="d-flex flex-column flex-lg-row justify-content-between">
    <div class="mb-3 mb-lg-0">
        <div class="mb-2"><?= sprintf(l('global.footer.copyright'), date('Y'), settings()->main->title) ?></div>

        <div>Powered by <img src="<?= ASSETS_FULL_URL . 'images/altumcode.png' ?>" class="icon-favicon" alt="AltumCode logo" /> <a href="https://altumcode.com/" target="_blank">AltumCode</a>.</div>
    </div>

    <div class="d-flex flex-column flex-lg-row">
        <?php if(count(\Altum\Language::$active_languages) > 1): ?>
            <div class="dropdown mb-2 ml-lg-3">
                <button type="button" class="btn btn-link text-decoration-none p-0" id="language_switch" data-tooltip data-tooltip-hide-on-click title="<?= l('global.choose_language') ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-fw fa-sm fa-language mr-1"></i> <?= \Altum\Language::$name ?>
                </button>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language_switch">
                    <?php foreach(\Altum\Language::$languages_ordered as $language): ?>
                        <?php if($language['status'] == 'active'): ?>
                            <a class="dropdown-item" href="<?= SITE_URL . $language['code'] . '/' . \Altum\Router::$original_request . '?' . \Altum\Router::$original_request_query . '&set_language=' . $language['name'] ?>">
                                <?php if($language['name'] == \Altum\Language::$name): ?>
                                    <i class="fas fa-fw fa-sm fa-check mr-2 text-success"></i>
                                <?php else: ?>
                                    <?php if($language['language_flag']): ?>
                                        <span class="mr-2"><?= $language['language_flag'] ?></span>
                                    <?php else: ?>
                                        <i class="fas fa-fw fa-sm fa-circle-notch mr-2 text-muted"></i>
                                    <?php endif ?>
                                <?php endif ?>

                                <?= $language['name'] ?>
                            </a>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

        <?php if(settings()->main->theme_style_change_is_enabled): ?>
            <div class="ml-lg-3">
                <button type="button" id="switch_theme_style" class="btn btn-link text-decoration-none p-0" data-toggle="tooltip" title="<?= sprintf(l('global.theme_style'), (\Altum\ThemeStyle::get() == 'light' ? l('global.theme_style_dark') : l('global.theme_style_light'))) ?>" data-title-theme-style-light="<?= sprintf(l('global.theme_style'), l('global.theme_style_light')) ?>" data-title-theme-style-dark="<?= sprintf(l('global.theme_style'), l('global.theme_style_dark')) ?>">
                    <span data-theme-style="light" class="<?= \Altum\ThemeStyle::get() == 'light' ? null : 'd-none' ?>"><i class="fas fa-fw fa-sm fa-sun mr-1"></i> <?=  l('global.theme_style_light') ?></span>
                    <span data-theme-style="dark" class="<?= \Altum\ThemeStyle::get() == 'dark' ? null : 'd-none' ?>"><i class="fas fa-fw fa-sm fa-moon mr-1"></i> <?=  l('global.theme_style_dark') ?></span>
                </button>
            </div>

            <?php include_view(THEME_PATH . 'views/partials/theme_style_js.php') ?>
        <?php endif ?>
    </div>
</footer>
