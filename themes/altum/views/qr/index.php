<?php defined('ALTUMCODE') || die() ?>

<div class="container text-center d-print-none">
    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="index-header mb-2"><?= $data->type ? sprintf(l('qr.header_dynamic'), l('qr_codes.type.' . $data->type)) : l('qr.header') ?></h1>
    <p class="index-subheader mb-5"><?= $data->type ? sprintf(l('qr.subheader_dynamic'), l('qr_codes.type.' . $data->type)) : l('qr.subheader') ?></p>

    <div class="d-flex flex-wrap justify-content-center">
        <?php foreach($data->qr_code_settings['type'] as $key => $value): ?>
            <div class="mr-3 mb-3" data-toggle="tooltip" <?= $this->user->plan_settings->enabled_qr_codes->{$key} ? 'title="' . l('qr_codes.type.' . $key . '_description') . '"' : 'title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <a
                        href="<?= url('qr/' . $key) ?>"
                        class="btn <?= $data->type == $key ? 'btn-primary' : 'btn-outline-secondary' ?> <?= $this->user->plan_settings->enabled_qr_codes->{$key} ? null : 'disabled' ?>"
                >
                    <i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1"></i> <?= l('qr_codes.type.' . $key) ?>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php if($data->type): ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-lg-7 d-print-none mb-5 mb-lg-0">
                <div class="card">
                    <div class="card-body">
                        <form action="<?= url('qr-code-create') ?>" method="post" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                            <input type="hidden" name="api_key" value="<?= $this->user->api_key ?? null ?>" />
                            <input type="hidden" name="type" value="<?= $data->type ?>" />
                            <input type="hidden" name="reload" value="" data-reload-qr-code />
                            <?php if(\Altum\Authentication::check()): ?>
                                <input type="hidden" name="qr_code" value="" />
                                <input type="hidden" name="embedded_data" value="<?= $data->values['embedded_data'] ?? null ?>" />
                                <input type="hidden" name="name" value="<?= $this->user->name ?>" />
                            <?php endif ?>

                            <div class="notification-container"></div>

                            <?= $this->views['qr_form'] ?>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#style_container" aria-expanded="false" aria-controls="style_container">
                                <i class="fas fa-fw fa-qrcode fa-sm mr-1"></i> <?= l('qr_codes.input.style') ?>
                            </button>

                            <div class="collapse" id="style_container">
                                <div class="form-group">
                                    <label for="style"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.style') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <?php foreach(['square' => 'fas fa-square-full', 'dot' => 'fas fa-circle', 'round' => 'fas fa-square', 'heart' => 'fas fa-heart', 'diamond' => 'fas fa-gem'] as $key => $icon): ?>
                                            <div class="col-6">
                                                <label class="btn btn-light btn-block <?= ($data->values['settings']['style'] ?? 'square') == $key ? 'active"' : null?>">
                                                    <input type="radio" name="style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['style'] ?? 'square') == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <i class="<?= $icon ?> fa-fw fa-sm mr-1"></i> <?= l('qr_codes.input.style.' . $key) ?>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inner_eye_style"><i class="fas fa-fw fa-circle fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.inner_eye_style') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <?php foreach(['square' => 'fas fa-square-full', 'dot' => 'fas fa-circle', 'rounded' => 'fas fa-square', 'diamond' => 'fas fa-gem', 'flower' => 'fas fa-seedling', 'leaf' => 'fas fa-leaf'] as $key => $icon): ?>
                                            <div class="col-6">
                                                <label class="btn btn-light btn-block <?= ($data->values['settings']['inner_eye_style'] ?? 'square') == $key ? 'active"' : null?>">
                                                    <input type="radio" name="inner_eye_style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['inner_eye_style'] ?? 'square') == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <i class="<?= $icon ?> fa-fw fa-sm mr-1"></i> <?= l('qr_codes.input.style.' . $key) ?>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="outer_eye_style"><i class="fas fa-fw fa-dot-circle fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.outer_eye_style') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <?php foreach(['square' => 'fas fa-square-full', 'circle' => 'fas fa-circle', 'rounded' => 'fas fa-square', 'flower' => 'fas fa-seedling', 'leaf' => 'fas fa-leaf'] as $key => $icon): ?>
                                            <div class="col-6">
                                                <label class="btn btn-light btn-block <?= ($data->values['settings']['outer_eye_style'] ?? 'square') == $key ? 'active"' : null?>">
                                                    <input type="radio" name="outer_eye_style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['outer_eye_style'] ?? 'square') == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <i class="<?= $icon ?> fa-fw fa-sm mr-1"></i> <?= l('qr_codes.input.style.' . $key) ?>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#colors_container" aria-expanded="false" aria-controls="colors_container">
                                <i class="fas fa-fw fa-palette fa-sm mr-1"></i> <?= l('qr_codes.input.colors') ?>
                            </button>

                            <div class="collapse" id="colors_container">
                                <div class="form-group">
                                    <label for="foreground_type"><i class="fas fa-fw fa-paint-roller fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_type') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <div class="col-6">
                                            <label class="btn btn-light btn-block active">
                                                <input type="radio" name="foreground_type" value="color" class="custom-control-input" checked="checked" required="required" data-reload-qr-code />
                                                <?= l('qr_codes.input.foreground_type_color') ?>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label class="btn btn-light btn-block">
                                                <input type="radio" name="foreground_type" value="gradient" class="custom-control-input" required="required" data-reload-qr-code />
                                                <?= l('qr_codes.input.foreground_type_gradient') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" data-foreground-type="color">
                                    <label for="foreground_color"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_color') ?></label>
                                    <input type="hidden" id="foreground_color" name="foreground_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_style"><i class="fas fa-fw fa-brush fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_gradient_style') ?></label>
                                    <select id="foreground_gradient_style" name="foreground_gradient_style" class="custom-select" data-reload-qr-code>
                                        <?php foreach(['vertical', 'horizontal', 'diagonal', 'inverse_diagonal', 'radial'] as $style): ?>
                                            <option value="<?= $style ?>"><?= l('qr_codes.input.foreground_gradient_style_' . $style) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_one"><?= l('qr_codes.input.foreground_gradient_one') ?></label>
                                    <input type="hidden" id="foreground_gradient_one" name="foreground_gradient_one" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_two"><?= l('qr_codes.input.foreground_gradient_two') ?></label>
                                    <input type="hidden" id="foreground_gradient_two" name="foreground_gradient_two" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group">
                                    <label for="background_color"><?= l('qr_codes.input.background_color') ?></label>
                                    <input type="hidden" id="background_color" name="background_color" class="form-control" value="<?= '#ffffff' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-range-counter data-range-counter-suffix="%">
                                    <label for="background_color_transparency"><?= l('qr_codes.input.background_color_transparency') ?></label>
                                    <input id="background_color_transparency" type="range" min="0" max="100" step="10" name="background_color_transparency" value="<?= 0 ?>" class="form-control" data-reload-qr-code />
                                </div>

                                <div class="form-group">
                                    <label for="custom_eyes_color"><i class="fas fa-fw fa-eye fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.custom_eyes_color') ?></label>
                                    <select id="custom_eyes_color" name="custom_eyes_color" class="custom-select" data-reload-qr-code>
                                        <option value="1"><?= l('global.yes') ?></option>
                                        <option value="0"><?= l('global.no') ?></option>
                                    </select>
                                </div>

                                <div class="form-group" data-custom-eyes-color="1">
                                    <label for="eyes_inner_color"><?= l('qr_codes.input.eyes_inner_color') ?></label>
                                    <input type="hidden" id="eyes_inner_color" name="eyes_inner_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-custom-eyes-color="1">
                                    <label for="eyes_outer_color"><?= l('qr_codes.input.eyes_outer_color') ?></label>
                                    <input type="hidden" id="eyes_outer_color" name="eyes_outer_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#branding_container" aria-expanded="false" aria-controls="branding_container">
                                <i class="fas fa-fw fa-copyright fa-sm mr-1"></i> <?= l('qr_codes.input.branding') ?>
                            </button>

                            <div class="collapse" id="branding_container">
                                <div class="form-group">
                                    <label for="qr_code_logo"><i class="fas fa-fw fa-eye fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_logo') ?></label>
                                    <?php if(!empty($data->values['qr_code_logo'])): ?>
                                        <input type="hidden" name="qr_code_logo" value="<?= \Altum\Uploads::get_full_url('qr_codes/logo') . $data->values['qr_code_logo'] ?>" />
                                        <div class="m-1">
                                            <img src="<?= \Altum\Uploads::get_full_url('qr_codes/logo') . $data->values['qr_code_logo'] ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
                                        </div>
                                        <div class="custom-control custom-checkbox my-2">
                                            <input id="qr_code_logo_remove" name="qr_code_logo_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#qr_code_logo').classList.add('d-none') : document.querySelector('#qr_code_logo').classList.remove('d-none')" data-reload-qr-code>
                                            <label class="custom-control-label" for="qr_code_logo_remove">
                                                <span class="text-muted"><?= l('global.delete_file') ?></span>
                                            </label>
                                        </div>
                                    <?php endif ?>
                                    <input id="qr_code_logo" type="file" name="qr_code_logo" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_codes/logo') ?>" class="form-control-file altum-file-input" data-reload-qr-code />
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_codes/logo')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), $data->qr_code_settings['qr_code_logo_size_limit']) ?></small>
                                </div>

                                <div class="form-group" data-range-counter>
                                    <label for="qr_code_logo_size"><i class="fas fa-fw fa-expand-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_logo_size') ?></label>
                                    <input id="qr_code_logo_size" type="range" min="5" max="35" name="qr_code_logo_size" value="<?= $data->values['qr_code_logo_size'] ?? 25 ?>" class="form-control" data-reload-qr-code />
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#options_container" aria-expanded="false" aria-controls="options_container">
                                <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('qr_codes.input.options') ?>
                            </button>

                            <div class="collapse" id="options_container">
                                <div class="form-group">
                                    <label for="size"><i class="fas fa-fw fa-expand-arrows-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.size') ?></label>
                                    <div class="input-group">
                                        <input id="size" type="number" min="50" max="2000" name="size" class="form-control" value="<?= 500 ?>" data-reload-qr-code />
                                        <div class="input-group-append">
                                            <span class="input-group-text">px</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="margin"><i class="fas fa-fw fa-expand fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.margin') ?></label>
                                    <input id="margin" type="number" min="0" max="25" name="margin" class="form-control" value="<?= 0 ?>" data-reload-qr-code />
                                </div>

                                <div class="form-group">
                                    <label for="ecc"><i class="fas fa-fw fa-check fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.ecc') ?></label>
                                    <select id="ecc" name="ecc" class="custom-select" data-reload-qr-code>
                                        <?php foreach(['L', 'M', 'Q', 'H'] as $level): ?>
                                            <option value="<?= $level ?>"><?= l('qr_codes.input.ecc_' . mb_strtolower($level)) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                            <?php if(\Altum\Authentication::check()): ?>
                                <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.create') ?></button>
                            <?php else: ?>
                                <?php if(settings()->users->register_is_enabled): ?>
                                    <a href="<?= url('register') ?>" class="btn btn-block btn-outline-primary mt-4"><i class="fas fa-fw fa-xs fa-plus mr-1"></i> <?= l('qr.register') ?></a>
                                <?php endif ?>
                            <?php endif ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="sticky">
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <img id="qr_code" src="<?= ASSETS_FULL_URL . 'images/qr_code.svg' ?>" class="img-fluid qr-code" loading="lazy" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-print-none">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" onclick="window.print()" class="btn btn-block btn-outline-secondary d-print-none">
                                <i class="fas fa-fw fa-sm fa-file-pdf mr-1"></i> <?= l('qr_codes.print') ?>
                            </button>
                        </div>

                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-primary d-print-none dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-fw fa-download fa-sm mr-1"></i> <?= l('global.download') ?>
                            </button>

                            <div class="dropdown-menu">
                                <a href="<?= ASSETS_FULL_URL . 'images/qr_code.svg' ?>" id="download_svg" class="dropdown-item" download="<?= get_slug(settings()->main->title) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                <button type="button" class="dropdown-item" onclick="convert_svg_to_others(null, 'png', '<?= get_slug(settings()->main->title) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_to_others(null, 'jpg', '<?= get_slug(settings()->main->title) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_to_others(null, 'webp', '<?= get_slug(settings()->main->title) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                            </div>
                        </div>
                    </div>

                    <button id="embedded_data_container_button" class="btn btn-block btn-light my-4 d-none" type="button" data-toggle="collapse" data-target="#embedded_data_container" aria-expanded="false" aria-controls="embedded_data_container">
                        <i class="fas fa-fw fa-bars fa-sm mr-1"></i> <?= l('qr_codes.embedded_data') ?>
                    </button>

                    <div class="collapse" id="embedded_data_container">
                        <div class="card my-4">
                            <div class="card-body" id="embedded_data_display"></div>
                        </div>
                    </div>

                    <div class="mb-4 text-center d-print-none">
                        <small>
                            <i class="fas fa-fw fa-info-circle text-muted mr-1"></i> <span class="text-muted"><?= l('qr_codes.info') ?></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <?= $this->views['qr_content'] ?>
        </div>
    </div>

    <?php require THEME_PATH . 'views/qr-codes/js_qr_code.php' ?>
    <?php include_view(THEME_PATH . 'views/partials/color_picker_js.php') ?>
<?php endif ?>

