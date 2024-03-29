<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center flex-column flex-lg-row">
                <img src="<?= ASSETS_FULL_URL . 'images/404.svg' ?>" class="col-10 col-md-7 col-lg-5 mb-5 mb-lg-0 mr-lg-3" loading="lazy" />

                <div>
                    <h1 class="h3"><?= l('notfound.header') ?></h1>
                    <p class="text-muted"><?= l('notfound.subheader') ?></p>

                    <a href="<?= url() ?>" class="btn btn-block btn-primary mt-4">
                        <i class="fas fa-fw fa-sm fa-home mr-1"></i> <?= l('notfound.button') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
