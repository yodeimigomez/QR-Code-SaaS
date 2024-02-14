<?php defined('ALTUMCODE') || die() ?>

<?php foreach(['reset', 'broadcasts'] as $cron): ?>
    <div class="form-group">
        <label for="cron_<?= $cron ?>"><?= l('admin_settings.cron.' . $cron) ?></label>
        <input id="cron_<?= $cron ?>" name="cron_<?= $cron ?>" type="text" class="form-control" value="<?= '* * * * * wget --quiet -O /dev/null ' . SITE_URL . 'cron/' . $cron . '?key=' . settings()->cron->key ?>" readonly="readonly" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.cron.last_execution'), isset(settings()->cron->{$cron . '_datetime'}) ? \Altum\Date::get_timeago(settings()->cron->{$cron . '_datetime'}) : '-') ?></small>
    </div>
<?php endforeach ?>

<div <?= !\Altum\Plugin::is_active('push-notifications') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('push-notifications')->name ?? 'push-notifications') . '"' : null ?>>
    <div class="<?= !\Altum\Plugin::is_active('push-notifications') ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="cron_push_notifications"><?= l('admin_settings.cron.push_notifications') ?></label>
            <input id="cron_push_notifications" name="cron_push_notifications" type="text" class="form-control" value="<?= '* * * * * wget --quiet -O /dev/null ' . SITE_URL . 'cron/push_notifications?key=' . settings()->cron->key ?>" readonly="readonly" />
            <small class="form-text text-muted"><?= sprintf(l('admin_settings.cron.last_execution'), isset(settings()->cron->push_notifications_datetime) ? \Altum\Date::get_timeago(settings()->cron->push_notifications_datetime) : '-') ?></small>
        </div>
    </div>
</div>
