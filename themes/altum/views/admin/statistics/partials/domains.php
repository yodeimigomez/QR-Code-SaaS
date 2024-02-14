<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-globe fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.domains.header') ?></h2>

            <div>
                <span class="badge <?= $data->total['domains'] > 0 ? 'badge-success' : 'badge-secondary' ?>"><?= ($data->total['domains'] > 0 ? '+' : null) . nr($data->total['domains']) ?></span>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="domains"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Prepare chart */
    let domains_chart = document.getElementById('domains').getContext('2d');
    color_gradient = domains_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(domains_chart, {
        type: 'line',
        data: {
            labels: <?= $data->domains_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode(l('admin_statistics.domains.chart')) ?>,
                data: <?= $data->domains_chart['domains'] ?? '[]' ?>,
                backgroundColor: color_gradient,
                borderColor: color,
                fill: true
            }]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
