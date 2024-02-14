<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="text">
        <label for="text"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.text') ?></label>
        <textarea id="text" name="text" class="form-control" maxlength="<?= $data->qr_code_settings['type']['text']['max_length'] ?>" required="required" data-reload-qr-code></textarea>
    </div>
</div>
