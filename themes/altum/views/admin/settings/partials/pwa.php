<?php defined('ALTUMCODE') || die() ?>

<div>
    <div <?= !\Altum\Plugin::is_active('pwa') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('pwa')->name ?? 'pwa') . '"' : null ?>>
        <div class="<?= !\Altum\Plugin::is_active('pwa') ? 'container-disabled' : null ?>">
            <div class="form-group custom-control custom-switch">
                <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('pwa') && settings()->pwa->is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.pwa.is_enabled') ?></label>
            </div>

            <div class="form-group">
                <label for="app_name"><?= l('admin_settings.pwa.app_name') ?></label>
                <input id="app_name" type="text" name="app_name" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->app_name : null ?>" />
            </div>

            <div class="form-group" data-character-counter="input">
                <label for="short_app_name" class="d-flex justify-content-between align-items-center">
                    <span><?= l('admin_settings.pwa.short_app_name') ?></span>
                    <small class="text-muted" data-character-counter-wrapper></small>
                </label>
                <input id="short_app_name" type="text" name="short_app_name" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->short_app_name : null ?>" maxlength="12" />
            </div>

            <div class="form-group">
                <label for="app_description"><?= l('admin_settings.pwa.app_description') ?></label>
                <input id="app_description" type="text" name="app_description" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->app_description : null ?>" />
            </div>

            <div class="form-group">
                <label for="app_start_url"><?= l('admin_settings.pwa.app_start_url') ?></label>
                <input id="app_start_url" type="text" name="app_start_url" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->app_start_url : null ?>" placeholder="<?= SITE_URL ?>" />
                <small class="form-text text-muted"><?= l('admin_settings.pwa.app_start_url_help') ?></small>
            </div>

            <div class="form-group">
                <label for="theme_color"><?= l('admin_settings.pwa.theme_color') ?></label>
                <input id="theme_color" type="hidden" name="theme_color" class="form-control" value="<?= settings()->pwa->theme_color ?>" data-color-picker />
            </div>

            <div class="form-group">
                <label for="app_icon"><?= l('admin_settings.pwa.app_icon') ?></label>
                <?php if(!empty(settings()->pwa->app_icon)): ?>
                    <div class="m-1">
                        <img src="<?= \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                    </div>
                    <div class="custom-control custom-checkbox my-2">
                        <input id="app_icon_remove" name="app_icon_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#app_icon').classList.add('d-none') : document.querySelector('#app_icon').classList.remove('d-none')">
                        <label class="custom-control-label" for="app_icon_remove">
                            <span class="text-muted"><?= l('global.delete_file') ?></span>
                        </label>
                    </div>
                <?php endif ?>
                <input id="app_icon" type="file" name="app_icon" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('app_icon') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= l('admin_settings.pwa.app_icon_help') ?></small>
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('app_icon')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>

            <div class="form-group">
                <label for="app_icon_maskable"><?= l('admin_settings.pwa.app_icon_maskable') ?></label>
                <?php if(!empty(settings()->pwa->app_icon_maskable)): ?>
                    <div class="m-1">
                        <img src="<?= \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon_maskable ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                    </div>
                    <div class="custom-control custom-checkbox my-2">
                        <input id="app_icon_maskable_remove" name="app_icon_maskable_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#app_icon_maskable').classList.add('d-none') : document.querySelector('#app_icon_maskable').classList.remove('d-none')">
                        <label class="custom-control-label" for="app_icon_maskable_remove">
                            <span class="text-muted"><?= l('global.delete_file') ?></span>
                        </label>
                    </div>
                <?php endif ?>
                <input id="app_icon_maskable" type="file" name="app_icon_maskable" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('app_icon') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= l('admin_settings.pwa.app_icon_maskable_help') ?></small>
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('app_icon')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#mobile_screenshots_container" aria-expanded="false" aria-controls="mobile_screenshots_container">
                <i class="fas fa-fw fa-mobile fa-sm mr-1"></i> <?= l('admin_settings.pwa.mobile_screenshots') ?>
            </button>

            <div class="collapse" id="mobile_screenshots_container">
                <div class="alert alert-info"><?= l('admin_settings.pwa.mobile_screenshots_help') ?></div>
                <div class="alert alert-info"><?= l('admin_settings.pwa.mobile_screenshots_help2') ?></div>

                <?php foreach([1, 2, 3, 4 ,5, 6] as $key): ?>
                    <div class="form-group">
                        <label for="<?= 'mobile_screenshot_' . $key ?>"><?= sprintf(l('admin_settings.pwa.screenshot_x'), $key) ?></label>
                        <?php if(!empty(settings()->pwa->{'mobile_screenshot_' . $key})): ?>
                            <div class="m-1">
                                <img src="<?= \Altum\Uploads::get_full_url('app_screenshots') . settings()->pwa->{'mobile_screenshot_' . $key} ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                            </div>
                            <div class="custom-control custom-checkbox my-2">
                                <input id="<?= 'mobile_screenshot_' . $key . '_remove' ?>" name="<?= 'mobile_screenshot_' . $key . '_remove' ?>" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('<?= '#mobile_screenshot_' . $key ?>').classList.add('d-none') : document.querySelector('<?= '#mobile_screenshot_' . $key ?>').classList.remove('d-none')">
                                <label class="custom-control-label" for="<?= 'mobile_screenshot_' . $key . '_remove' ?>">
                                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                                </label>
                            </div>
                        <?php endif ?>
                        <input id="<?= 'mobile_screenshot_' . $key ?>" type="file" name="<?= 'mobile_screenshot_' . $key ?>" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots') ?>" class="form-control-file altum-file-input" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                    </div>
                <?php endforeach ?>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#desktop_screenshots_container" aria-expanded="false" aria-controls="desktop_screenshots_container">
                <i class="fas fa-fw fa-desktop fa-sm mr-1"></i> <?= l('admin_settings.pwa.desktop_screenshots') ?>
            </button>

            <div class="collapse" id="desktop_screenshots_container">
                <div class="alert alert-info"><?= l('admin_settings.pwa.desktop_screenshots_help') ?></div>
                <div class="alert alert-info"><?= l('admin_settings.pwa.desktop_screenshots_help2') ?></div>

                <?php foreach([1,2,3,4,5,6,7,8] as $key): ?>
                    <div class="form-group">
                        <label for="<?= 'desktop_screenshot_' . $key ?>"><?= sprintf(l('admin_settings.pwa.screenshot_x'), $key) ?></label>
                        <?php if(!empty(settings()->pwa->{'desktop_screenshot_' . $key})): ?>
                            <div class="m-1">
                                <img src="<?= \Altum\Uploads::get_full_url('app_screenshots') . settings()->pwa->{'desktop_screenshot_' . $key} ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                            </div>
                            <div class="custom-control custom-checkbox my-2">
                                <input id="<?= 'desktop_screenshot_' . $key . '_remove' ?>" name="<?= 'desktop_screenshot_' . $key . '_remove' ?>" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('<?= '#desktop_screenshot_' . $key ?>').classList.add('d-none') : document.querySelector('<?= '#desktop_screenshot_' . $key ?>').classList.remove('d-none')">
                                <label class="custom-control-label" for="<?= 'desktop_screenshot_' . $key . '_remove' ?>">
                                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                                </label>
                            </div>
                        <?php endif ?>
                        <input id="<?= 'desktop_screenshot_' . $key ?>" type="file" name="<?= 'desktop_screenshot_' . $key ?>" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots') ?>" class="form-control-file altum-file-input" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                    </div>
                <?php endforeach ?>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#shortcuts_container" aria-expanded="false" aria-controls="shortcuts_container">
                <i class="fas fa-fw fa-wand-sparkles fa-sm mr-1"></i> <?= l('admin_settings.pwa.shortcuts') ?>
            </button>

            <div class="collapse" id="shortcuts_container">
                <?php foreach([1,2,3] as $key): ?>
                    <div class="form-group">
                        <label for="<?= 'shortcut_name_' . $key ?>"><?= sprintf(l('admin_settings.pwa.shortcut_name_x'), $key) ?></label>
                        <input id="<?= 'shortcut_name_' . $key ?>" type="text" name="<?= 'shortcut_name_' . $key ?>" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->{'shortcut_name_' . $key} : null ?>" />
                    </div>

                    <div class="form-group">
                        <label for="<?= 'shortcut_description_' . $key ?>"><?= sprintf(l('admin_settings.pwa.shortcut_description_x'), $key) ?></label>
                        <input id="<?= 'shortcut_description_' . $key ?>" type="text" name="<?= 'shortcut_description_' . $key ?>" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->{'shortcut_description_' . $key} : null ?>" />
                    </div>

                    <div class="form-group">
                        <label for="<?= 'shortcut_url_' . $key ?>"><?= sprintf(l('admin_settings.pwa.shortcut_url_x'), $key) ?></label>
                        <input id="<?= 'shortcut_url_' . $key ?>" type="url" name="<?= 'shortcut_url_' . $key ?>" class="form-control" value="<?= \Altum\Plugin::is_active('pwa') ? settings()->pwa->{'shortcut_url_' . $key} : null ?>" />
                    </div>

                    <div class="form-group">
                        <label for="<?= 'shortcut_icon_' . $key ?>"><?= sprintf(l('admin_settings.pwa.shortcut_icon_x'), $key) ?></label>
                        <?php if(!empty(settings()->pwa->{'shortcut_icon_' . $key})): ?>
                            <div class="m-1">
                                <img src="<?= \Altum\Uploads::get_full_url('app_screenshots') . settings()->pwa->{'shortcut_icon_' . $key} ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                            </div>
                            <div class="custom-control custom-checkbox my-2">
                                <input id="<?= 'shortcut_icon_' . $key . '_remove' ?>" name="<?= 'shortcut_icon_' . $key . '_remove' ?>" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('<?= '#shortcut_icon_' . $key ?>').classList.add('d-none') : document.querySelector('<?= '#shortcut_icon_' . $key ?>').classList.remove('d-none')">
                                <label class="custom-control-label" for="<?= 'shortcut_icon_' . $key . '_remove' ?>">
                                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                                </label>
                            </div>
                        <?php endif ?>
                        <input id="<?= 'shortcut_icon_' . $key ?>" type="file" name="<?= 'shortcut_icon_' . $key ?>" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots') ?>" class="form-control-file altum-file-input" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('app_screenshots')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('pwa')): ?>
    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
<?php endif ?>

<?php include_view(THEME_PATH . 'views/partials/color_picker_js.php') ?>
