<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="url" data-url>
        <label for="url"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('global.url') ?></label>
        <input type="url" id="url" name="url" class="form-control" value="" maxlength="<?= $data->qr_code_settings['type']['url']['max_length'] ?>" required="required" placeholder="<?= l('global.url_placeholder') ?>" data-reload-qr-code />
    </div>

    <?php if(settings()->users->register_is_enabled || \Altum\Authentication::check()): ?>
    <div class="form-group" data-type="url" data-link-id>
        <div class="d-flex flex-column flex-xl-row justify-content-between">
            <label for="link_id"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.link_id') ?></label>
            <a href="<?= url('link-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('global.create') ?></a>
        </div>
        <select id="link_id" name="link_id" class="custom-select" required="required" data-reload-qr-code>
            <?php foreach($data->links as $row): ?>
                <option value="<?= $row->link_id ?>"><?= $row->full_url ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group" data-type="url">
        <div <?= \Altum\Authentication::check() ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= \Altum\Authentication::check() ? null : 'container-disabled' ?>">
                <div class="custom-control custom-checkbox">
                    <input id="url_dynamic" name="url_dynamic" type="checkbox" class="custom-control-input" data-reload-qr-code />
                    <label class="custom-control-label" for="url_dynamic"><?= l('qr_codes.input.url_dynamic') ?></label>
                    <small class="form-text text-muted"><?= l('qr_codes.input.url_dynamic_help') ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>
