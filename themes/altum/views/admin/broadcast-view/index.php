<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/broadcasts') ?>"><?= l('admin_broadcasts.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_broadcast_view.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fas fa-fw fa-xs fa-mail-bulk text-primary-900 mr-2"></i> <?= $data->broadcast->name ?></h1>

    <div class="d-flex align-items-center">
        <div>
            <button
                    id="daterangepicker"
                    type="button"
                    class="btn btn-sm btn-light"
                    data-min-date="<?= \Altum\Date::get($data->broadcast->datetime, 4) ?>"
                    data-max-date="<?= \Altum\Date::get('', 4) ?>"
            >
                <i class="fas fa-fw fa-calendar mr-lg-1"></i>
                <span class="d-none d-lg-inline-block">
                <?php if($data->datetime['start_date'] == $data->datetime['end_date']): ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php else: ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->datetime['end_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php endif ?>
            </span>
                <i class="fas fa-fw fa-caret-down d-none d-lg-inline-block ml-lg-1"></i>
            </button>
        </div>

        <div class="ml-3">
            <?= include_view(THEME_PATH . 'views/admin/broadcasts/admin_broadcast_dropdown_button.php', ['id' => $data->broadcast->broadcast_id, 'resource_name' => $data->broadcast->name]) ?>
        </div>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>


<div class="mb-4 row justify-content-between">
    <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted">
                    <?php if($data->broadcast->status == 'draft'): ?>
                        <i class="fas fa-fw fa-sm fa-save text-light mr-1"></i>
                    <?php elseif($data->broadcast->status == 'processing'): ?>
                        <i class="fas fa-fw fa-sm fa-spinner fa-spin tet-warning mr-1"></i>
                    <?php elseif($data->broadcast->status == 'sent'): ?>
                        <i class="fas fa-fw fa-sm fa-check text-success mr-1"></i>
                    <?php endif ?>

                    <?= l('global.status') ?>
                </small>

                <div class="mt-3">
                    <span class="h4">
                        <?php if($data->broadcast->status == 'draft'): ?>
                            <?= l('admin_broadcasts.main.status.draft') ?>
                        <?php elseif($data->broadcast->status == 'processing'): ?>
                            <?= l('admin_broadcasts.main.status.processing') ?>
                        <?php elseif($data->broadcast->status == 'sent'): ?>
                            <?= l('admin_broadcasts.main.status.sent') ?>
                        <?php endif ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($data->broadcast->sent_emails, $data->broadcast->total_emails)) . '%' ?>">
            <div class="card-body">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-envelope mr-1"></i> <?= l('admin_broadcasts.main.sent_emails') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->broadcast->sent_emails) . '/' . nr($data->broadcast->total_emails) ?></span></div>
            </div>
        </div>
    </div>

    <?php if(settings()->main->broadcasts_statistics_is_enabled): ?>
        <div class="col-12 col-sm-6 col-xl mb-4 position-relative" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($data->broadcast->views, $data->broadcast->total_emails)) . '%' ?>">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="card-body">
                    <small class="text-muted"><i class="fas fa-fw fa-sm fa-eye mr-1"></i> <?= l('admin_broadcasts.main.views') ?></small>

                    <div class="mt-3"><span class="h4"><?= nr($data->broadcast->views) ?></span></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($data->broadcast->clicks, $data->broadcast->total_emails)) . '%' ?>">
                <div class="card-body">
                    <small class="text-muted"><i class="fas fa-fw fa-sm fa-mouse mr-1"></i> <?= l('admin_broadcasts.main.clicks') ?></small>

                    <div class="mt-3"><span class="h4"><?= nr($data->broadcast->clicks) ?></span></div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<div class="card mb-5">
    <div class="card-body">
        <div class="chart-container">
            <canvas id="statistics"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-5">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-group">
                    <label for="name" class="font-weight-bold"><i class="fas fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('global.name') ?></label>
                    <input id="name" type="text" class="form-control-plaintext" value="<?= $data->broadcast->name ?>" readonly />
                </div>

                <div class="form-group">
                    <label for="subject" class="font-weight-bold"><i class="fas fa-fw fa-sm fa-heading text-muted mr-1"></i> <?= l('admin_broadcasts.main.subject') ?></label>
                    <input id="subject" type="text" class="form-control-plaintext" value="<?= $data->broadcast->subject ?>" readonly />
                </div>

                <div class="form-group">
                    <label for="segment" class="font-weight-bold"><i class="fas fa-fw fa-sm fa-layer-group text-muted mr-1"></i> <?= l('admin_broadcasts.main.segment') ?></label>
                    <input id="segment" type="text" class="form-control-plaintext" value="<?= l('admin_broadcasts.main.segment.' . $data->broadcast->segment) ?>" readonly />
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fas fa-fw fa-sm fa-paper-plane text-muted mr-1"></i>
                        <?= sprintf(l('admin_broadcasts.main.last_sent_email_datetime'), ($data->broadcast->last_sent_email_datetime ? \Altum\Date::get($data->broadcast->last_sent_email_datetime, 2) . ' - <small>' . \Altum\Date::get($data->broadcast->last_sent_email_datetime, 3) . '</small>' : '-')) ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fas fa-fw fa-sm fa-clock text-muted mr-1"></i>
                        <?= sprintf(l('admin_broadcasts.main.last_sent_email_datetime'), ($data->broadcast->last_sent_email_datetime ? \Altum\Date::get($data->broadcast->last_sent_email_datetime, 2) . ' - <small>' . \Altum\Date::get($data->broadcast->last_sent_email_datetime, 3) . '</small>' : '-')) ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fas fa-fw fa-sm fa-history text-muted mr-1"></i>
                        <?= sprintf(l('admin_broadcasts.main.last_sent_email_datetime'), ($data->broadcast->last_sent_email_datetime ? \Altum\Date::get($data->broadcast->last_sent_email_datetime, 2) . ' - <small>' . \Altum\Date::get($data->broadcast->last_sent_email_datetime, 3) . '</small>' : '-')) ?>
                    </label>
                </div>

            </div>
        </div>
    </div>

    <?php if(settings()->main->broadcasts_statistics_is_enabled): ?>
        <div class="col-xl-6 mb-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-4"><?= l('admin_broadcasts.main.latest_views') ?></h2>

                    <?php if(count($data->users)): ?>

                        <div>
                            <?php foreach($data->users as $user): ?>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex">
                                        <a href="<?= url('admin/user-view/' . $user->user_id) ?>">
                                            <img src="<?= get_gravatar($user->email) ?>" class="user-avatar rounded-circle mr-3" alt="" />
                                        </a>

                                        <div class="d-flex flex-column">
                                            <div>
                                                <a href="<?= url('admin/user-view/' . $user->user_id) ?>"><?= $user->name ?></a>
                                            </div>

                                            <span class="text-muted"><?= $user->email ?></span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($user->datetime, 1) ?>"><?= \Altum\Date::get_timeago($user->datetime) ?></span>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>

                    <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center pt-7">
                            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-5 mb-3" alt="<?= l('global.no_data') ?>" />
                            <h3 class="h5 text-muted"><?= l('global.no_data') ?></h3>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>


<?php if(settings()->main->broadcasts_statistics_is_enabled): ?>
    <div class="card h-100">
        <div class="card-body">
            <h2 class="h5 mb-4"><?= l('admin_broadcasts.main.clicks') ?></h2>

            <?php if(count($data->clicks)): ?>

                <div>
                    <?php foreach($data->clicks as $click): ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <img src="<?= get_favicon_url_from_domain($click->target) ?>" class="img-fluid icon-favicon mr-1" loading="lazy" />

                                <a href="<?= $click->target ?>" target="_blank" rel="noreferrer">
                                    <?= remove_url_protocol_from_url($click->target) ?>
                                </a>
                            </div>

                            <div>
                                <span class="text-muted"><?= nr($click->clicks) ?></span>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('global.no_data') ?>" />
                    <h3 class="h5 text-muted"><?= l('global.no_data') ?></h3>
                </div>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/libraries/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/admin_chartjs_defaults.js' ?>"></script>

<script>
    'use strict'

    /* Daterangepicker */
    $('#daterangepicker').daterangepicker({
        startDate: <?= json_encode($data->datetime['start_date']) ?>,
        endDate: <?= json_encode($data->datetime['end_date']) ?>,
        minDate: $('#daterangepicker').data('min-date'),
        maxDate: $('#daterangepicker').data('max-date'),
        ranges: {
            <?= json_encode(l('global.date.today')) ?>: [moment(), moment()],
            <?= json_encode(l('global.date.yesterday')) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            <?= json_encode(l('global.date.last_7_days')) ?>: [moment().subtract(6, 'days'), moment()],
            <?= json_encode(l('global.date.last_30_days')) ?>: [moment().subtract(29, 'days'), moment()],
            <?= json_encode(l('global.date.this_month')) ?>: [moment().startOf('month'), moment().endOf('month')],
            <?= json_encode(l('global.date.last_month')) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            <?= json_encode(l('global.date.all_time')) ?>: [moment('2015-01-01'), moment()]
        },
        alwaysShowCalendars: true,
        linkedCalendars: false,
        singleCalendar: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {

        /* Redirect */
        redirect(`<?= url('admin/broadcast/' . $data->broadcast->broadcast_id) ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });

    /* Chart */
    let css = window.getComputedStyle(document.body)
    let views_color = css.getPropertyValue('--primary');
    let clicks_color = css.getPropertyValue('--gray-500');
    let views_color_gradient = null;
    let clicks_color_gradient = null;

    /* Display chart */
    let statistics_chart = document.getElementById('statistics').getContext('2d');

    views_color_gradient = statistics_chart.createLinearGradient(0, 0, 0, 250);
    views_color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    views_color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    clicks_color_gradient = statistics_chart.createLinearGradient(0, 0, 0, 250);
    clicks_color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    clicks_color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(statistics_chart, {
        type: 'line',
        data: {
            labels: <?= $data->statistics_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_broadcasts.main.views')) ?>,
                    data: <?= $data->statistics_chart['views'] ?? '[]' ?>,
                    backgroundColor: views_color_gradient,
                    borderColor: views_color,
                    fill: true
                },

                {
                    label: <?= json_encode(l('admin_broadcasts.main.clicks')) ?>,
                    data: <?= $data->statistics_chart['clicks'] ?? '[]' ?>,
                    backgroundColor: clicks_color_gradient,
                    borderColor: clicks_color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

