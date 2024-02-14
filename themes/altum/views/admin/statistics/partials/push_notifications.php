<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-bolt-lightning fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.push_notifications.header') ?></h2>

            <div>
                <span class="badge <?= $data->total['push_notifications'] > 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.push_notifications.chart_push_notifications') ?>">
                    <?= ($data->total['push_notifications'] > 0 ? '+' : null) . nr($data->total['push_notifications']) ?>
                </span>
                <span class="badge <?= $data->total['sent_push_notifications'] > 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.push_notifications.chart_sent_push_notifications') ?>">
                    <?= ($data->total['sent_push_notifications'] > 0 ? '+' : null) . nr($data->total['sent_push_notifications']) ?>
                </span>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="push_notifications"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
    <script>
        'use strict';

        let push_notifications_color = css.getPropertyValue('--gray-500');
        let sent_push_notifications_color = css.getPropertyValue('--primary');

        /* Display chart */
        let push_notifications_chart = document.getElementById('push_notifications').getContext('2d');

        let sent_push_notifications_color_gradient = push_notifications_chart.createLinearGradient(0, 0, 0, 250);
        sent_push_notifications_color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
        sent_push_notifications_color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)')

        let push_notifications_color_gradient = push_notifications_chart.createLinearGradient(0, 0, 0, 250);
        push_notifications_color_gradient.addColorStop(0, 'rgba(160, 174, 192, .1)');
        push_notifications_color_gradient.addColorStop(1, 'rgba(160, 174, 192, 0.025)')

        new Chart(push_notifications_chart, {
            type: 'line',
            data: {
                labels: <?= $data->push_notifications_chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('admin_statistics.push_notifications.chart_push_notifications')) ?>,
                        data: <?= $data->push_notifications_chart['push_notifications'] ?? '[]' ?>,
                        backgroundColor: push_notifications_color_gradient,
                        borderColor: push_notifications_color,
                        fill: true
                    },
                    {
                        label: <?= json_encode(l('admin_statistics.push_notifications.chart_sent_push_notifications')) ?>,
                        data: <?= $data->push_notifications_chart['sent_push_notifications'] ?? '[]' ?>,
                        backgroundColor: sent_push_notifications_color_gradient,
                        borderColor: sent_push_notifications_color,
                        fill: true
                    }
                ]
            },
            options: chart_options
        });
    </script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
