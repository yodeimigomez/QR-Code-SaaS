<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group custom-control custom-switch">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->payment->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><i class="fas fa-fw fa-sm fa-credit-card text-muted mr-1"></i> <?= l('admin_settings.payment.is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('admin_settings.payment.is_enabled_help') ?></small>
        </div>

        <div class="form-group">
            <label for="type"><i class="fas fa-fw fa-sm fa-credit-card text-muted mr-1"></i> <?= l('admin_settings.payment.type') ?></label>
            <select id="type" name="type" class="custom-select">
                <option value="one_time" <?= settings()->payment->type == 'one_time' ? 'selected="selected"' : null ?>><?= l('admin_settings.payment.type_one_time') ?></option>
                <option value="recurring" <?= settings()->payment->type == 'recurring' ? 'selected="selected"' : null ?>><?= l('admin_settings.payment.type_recurring') ?></option>
                <option value="both" <?= settings()->payment->type == 'both' ? 'selected="selected"' : null ?>><?= l('admin_settings.payment.type_both') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="default_payment_frequency"><i class="fas fa-fw fa-sm fa-shopping-bag text-muted mr-1"></i> <?= l('admin_settings.payment.default_payment_frequency') ?></label>
            <select id="default_payment_frequency" name="default_payment_frequency" class="custom-select">
                <option value="monthly" <?= settings()->payment->default_payment_frequency == 'monthly' ? 'selected="selected"' : null ?>><?= l('plan.custom_plan.monthly') ?></option>
                <option value="annual" <?= settings()->payment->default_payment_frequency == 'annual' ? 'selected="selected"' : null ?>><?= l('plan.custom_plan.annual') ?></option>
                <option value="lifetime" <?= settings()->payment->default_payment_frequency == 'lifetime' ? 'selected="selected"' : null ?>><?= l('plan.custom_plan.lifetime') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="currency"><i class="fas fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= l('admin_settings.payment.currency') ?></label>
            <input id="currency" type="text" name="currency" class="form-control" value="<?= settings()->payment->currency ?>" />
            <small class="form-text text-muted"><?= l('admin_settings.payment.currency_help') ?></small>
        </div>

        <div class="form-group custom-control custom-switch">
            <input id="codes_is_enabled" name="codes_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->payment->codes_is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="codes_is_enabled"><i class="fas fa-fw fa-sm fa-tags text-muted mr-1"></i> <?= l('admin_settings.payment.codes_is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('admin_settings.payment.codes_is_enabled_help') ?></small>
        </div>

        <div class="form-group custom-control custom-switch">
            <input id="taxes_and_billing_is_enabled" name="taxes_and_billing_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->payment->taxes_and_billing_is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="taxes_and_billing_is_enabled"><i class="fas fa-fw fa-sm fa-receipt text-muted mr-1"></i> <?= l('admin_settings.payment.taxes_and_billing_is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('admin_settings.payment.taxes_and_billing_is_enabled_help') ?></small>
        </div>

        <div class="form-group custom-control custom-switch">
            <input id="invoice_is_enabled" name="invoice_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->payment->invoice_is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="invoice_is_enabled"><i class="fas fa-fw fa-sm fa-file-invoice text-muted mr-1"></i> <?= l('admin_settings.payment.invoice_is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('admin_settings.payment.invoice_is_enabled_help') ?></small>
        </div>

        <div class="form-group">
            <label for="user_plan_expiry_reminder"><i class="fas fa-fw fa-sm fa-envelope-open-text text-muted mr-1"></i> <?= l('admin_settings.payment.user_plan_expiry_reminder') ?></label>
            <div class="input-group">
                <input id="user_plan_expiry_reminder" type="number" min="0" name="user_plan_expiry_reminder" class="form-control" value="<?= settings()->payment->user_plan_expiry_reminder ?>" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= l('global.date.days') ?></span>
                </div>
            </div>
            <small class="form-text text-muted"><?= l('admin_settings.payment.user_plan_expiry_reminder_help') ?></small>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
