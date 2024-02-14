<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-link fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.links.header') ?></h2>

            <div>
                <span class="badge <?= $data->total['links'] > 0 ? 'badge-success' : 'badge-secondary' ?>"><?= ($data->total['links'] > 0 ? '+' : null) . nr($data->total['links']) ?></span>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="links"></canvas>
        </div>
    </div>
</div>

<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Display chart */
    let links_chart = document.getElementById('links').getContext('2d');
    color_gradient = links_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(links_chart, {
        type: 'line',
        data: {
            labels: <?= $data->links_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_statistics.links.chart')) ?>,
                    data: <?= $data->links_chart['links'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
