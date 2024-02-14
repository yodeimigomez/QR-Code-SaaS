<?php defined('ALTUMCODE') || die() ?>

<div class="index-background py-5">
    <div class="container">
        <?= \Altum\Alerts::output_alerts() ?>

        <div class="row justify-content-center">
            <div class="col-11 col-md-10 col-lg-7">
                <h1 class="index-header text-center mb-2"><?= l('index.header') ?></h1>
            </div>

            <div class="col-10 col-sm-8 col-lg-6">
                <p class="index-subheader text-center mb-5"><?= l('index.subheader') ?></p>
            </div>
        </div>

        <div class="d-flex flex-column flex-lg-row justify-content-center">
            <a href="<?= url('qr/text') ?>" class="btn btn-primary index-button mb-3 mb-lg-0 mr-lg-3">
                <i class="fas fa-fw fa-sm fa-plus-circle mr-1"></i> <?= l('index.qr') ?>
            </a>
            <?php if(settings()->users->register_is_enabled): ?>
                <a href="<?= url('register') ?>" target="_blank" class="btn btn-dark index-button mb-3 mb-lg-0">
                    <i class="fas fa-fw fa-sm fa-user-plus mr-1"></i> <?= l('index.register') ?>
                </a>
            <?php endif ?>
        </div>

        <div class="row justify-content-center mt-8" data-aos="fade-up">
            <div class="col-12">
                <img src="<?= ASSETS_FULL_URL . 'images/index/hero.png' ?>" class="img-fluid shadow-lg" loading="lazy" />
            </div>
        </div>
    </div>
</div>

<div class="my-5">&nbsp;</div>

<div class="container">
    <div class="row">
        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="100">
                <img src="<?= ASSETS_FULL_URL . 'images/index/qr_templates.png' ?>" class="index-card-image mb-2 " loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.qr_templates.header') ?></span>
                    </div>
                    <span class="text-muted"><?= sprintf(l('index.qr_templates.subheader'), count($data->qr_code_settings['type'])) ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="200">
                <img src="<?= ASSETS_FULL_URL . 'images/index/privacy.png' ?>" class="index-card-image mb-2" loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.privacy.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.privacy.subheader') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="300">
                <img src="<?= ASSETS_FULL_URL . 'images/index/customization.png' ?>" class="index-card-image mb-2" loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.customization.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.customization.subheader') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="400">
                <img src="<?= ASSETS_FULL_URL . 'images/index/pixels.png' ?>" class="index-card-image mb-2" loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.pixels.header') ?></span>
                    </div>
                    <span class="text-muted"><?= sprintf(l('index.pixels.subheader'), implode(', ',  array_map(function($item) {return $item['name'];}, require APP_PATH . 'includes/l/pixels.php'))) ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="500">
                <img src="<?= ASSETS_FULL_URL . 'images/index/projects.png' ?>" class="index-card-image mb-2" loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.projects.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.projects.subheader') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-5">
            <div class="card d-flex flex-column justify-content-between h-100" data-aos="fade-up" data-aos-delay="600">
                <img src="<?= ASSETS_FULL_URL . 'images/index/domains.png' ?>" class="index-card-image mb-2" loading="lazy" />

                <div class="card-body">
                    <div class="mb-2">
                        <span class="h5"><?= l('index.domains.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.domains.subheader') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="my-5">&nbsp;</div>

<div class="container">
    <div class="row">
        <div class="col-lg-7 mb-5" data-aos="fade">
            <img src="<?= ASSETS_FULL_URL . 'images/index/static.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>

        <div class="col-lg-5 mb-5 d-flex align-items-center">
            <div>
                <span class="p-3 bg-primary-100 rounded">
                    <i class="fas fa-fw fa-lg fa-qrcode text-primary"></i>
                </span>

                <h2 class="mt-4"><?= l('index.static.header') ?></h2>
                <p class="text-muted mt-3"><?= l('index.static.subheader') ?></p>

                <ul class="list-style-none mt-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.static.feature1') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.static.feature2') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.static.feature3') ?></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="my-5">&nbsp;</div>

<div class="container">
    <div class="row">
        <div class="col-lg-5 mb-5 d-flex align-items-center order-1 order-lg-0">
            <div>
                <span class="p-3 bg-primary-100 rounded">
                    <i class="fas fa-fw fa-lg fa-link text-primary"></i>
                </span>

                <h2 class="mt-4"><?= l('index.dynamic.header') ?></h2>
                <p class="text-muted mt-3"><?= l('index.dynamic.subheader') ?></p>

                <ul class="list-style-none mt-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.dynamic.feature1') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.dynamic.feature2') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.dynamic.feature3') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.dynamic.feature4') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                        <div><?= l('index.dynamic.feature5') ?></div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-7 mb-5 order-0 order-lg-1" data-aos="fade">
            <img src="<?= ASSETS_FULL_URL . 'images/index/dynamic.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>
    </div>
</div>

<div class="my-5">&nbsp;</div>

<div class="container">
    <div class="text-center mb-5">
        <h2><?= l('index.qr_codes.header') ?></h2>
        <p class="text-muted"><?= l('index.qr_codes.subheader') ?></p>
    </div>

    <div class="row">
        <?php foreach($data->qr_code_settings['type'] as $key => $value): ?>
            <div class="col-12 col-lg-6 mb-4">
                <div class="card position-relative icon-zoom-animation">
                    <div class="card-body bg-gray-50 text-center d-flex flex-column">
                        <div class="mb-4"><i class="<?= $value['icon'] ?> fa-fw fa-2x text-primary"></i></div>
                        <h3 class="h4"><?= l('qr_codes.type.' . $key) ?></h3>
                        <p class="text-muted"><?= l('qr_codes.type.' . $key . '_description') ?></p>

                        <a href="<?= url('qr/' . $key) ?>" class="btn btn-block btn-sm btn-light mt-4 stretched-link">
                            <?= sprintf(l('index.qr_codes.choose'), l('qr_codes.type.' . $key)) ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php if(settings()->main->display_index_testimonials): ?>
    <div class="my-5">&nbsp;</div>

    <div class="mt-9 py-7 bg-primary-100">
        <div class="container">
            <div class="text-center">
                <h2><?= l('index.testimonials.header') ?> <i class="fas fa-fw fa-xs fa-check-circle text-primary"></i></h2>
            </div>

            <div class="row mt-8">
                <?php foreach(['one', 'two', 'three', ] as $key => $value): ?>
                    <div class="col-12 col-lg-4 mb-6 mb-lg-0" data-aos="fade-up" data-aos-delay="<?= $key * 100 ?>">

                        <div class="card border-0 zoom-animation">
                            <div class="card-body">
                                <img src="<?= ASSETS_FULL_URL . 'images/index/testimonial-' . $value . '.jpeg' ?>" class="img-fluid index-testimonial-avatar" loading="lazy" />

                                <p class="mt-5">
                                    <span class="text-gray-800 font-weight-bold text-muted h5">“</span>
                                    <span><?= l('index.testimonials.' . $value . '.text') ?></span>
                                    <span class="text-gray-800 font-weight-bold text-muted h5">”</span>
                                </p>

                                <div class="blockquote-footer mt-4">
                                    <span class="font-weight-bold"><?= l('index.testimonials.' . $value . '.name') ?></span>, <span class="text-muted"><?= l('index.testimonials.' . $value . '.attribute') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(settings()->main->display_index_plans): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><?= l('index.pricing.header') ?></h2>
            <p class="text-muted"><?= l('index.pricing.subheader') ?></p>
        </div>

        <?= $this->views['plans'] ?>
    </div>
<?php endif ?>

<?php if(settings()->main->display_index_faq): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><?= sprintf(l('index.faq.header'), '<span class="text-primary">', '</span>') ?></h2>
        </div>

        <div class="accordion index-faq" id="faq_accordion">
            <?php foreach(['one', 'two', 'three', 'four'] as $key): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="" id="<?= 'faq_accordion_' . $key ?>">
                            <h3 class="mb-0">
                                <button class="btn btn-lg font-weight-bold btn-block d-flex justify-content-between text-gray-800 px-0 icon-zoom-animation" type="button" data-toggle="collapse" data-target="<?= '#faq_accordion_answer_' . $key ?>" aria-expanded="true" aria-controls="<?= 'faq_accordion_answer_' . $key ?>">
                                    <span><?= l('index.faq.' . $key . '.question') ?></span>

                                    <span data-icon>
                                        <i class="fas fa-fw fa-circle-chevron-down"></i>
                                    </span>
                                </button>
                            </h3>
                        </div>

                        <div id="<?= 'faq_accordion_answer_' . $key ?>" class="collapse text-muted mt-2" aria-labelledby="<?= 'faq_accordion_' . $key ?>" data-parent="#faq_accordion">
                            <?= l('index.faq.' . $key . '.answer') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        'use strict';

        $('#faq_accordion').on('show.bs.collapse', event => {
            let svg = event.target.parentElement.querySelector('[data-icon] svg')
            svg.style.transform = 'rotate(180deg)';
            svg.style.color = 'var(--primary)';
        })

        $('#faq_accordion').on('hide.bs.collapse', event => {
            let svg = event.target.parentElement.querySelector('[data-icon] svg')
            svg.style.color = 'var(--primary-800)';
            svg.style.removeProperty('transform');
        })
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>

<?php if(settings()->users->register_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="card border-0 index-cta py-5 py-lg-6">
            <div class="card-body">
                <div class="row align-items-center justify-content-center">
                    <div class="col-12 col-lg-5">
                        <div class="text-center text-lg-left mb-4 mb-lg-0">
                            <h1><?= l('index.cta.header') ?></h1>
                            <p class="h5"><?= l('index.cta.subheader') ?></p>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5 mt-4 mt-lg-0">
                        <div class="text-center text-lg-right">
                            <?php if(\Altum\Authentication::check()): ?>
                                <a href="<?= url('dashboard') ?>" class="btn btn-primary zoom-animation">
                                    <?= l('dashboard.menu') ?> <i class="fas fa-fw fa-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= url('register') ?>" class="btn btn-primary zoom-animation">
                                    <?= l('index.cta.register') ?> <i class="fas fa-fw fa-arrow-right"></i>
                                </a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>


<?php ob_start() ?>
<link rel="stylesheet" href="<?= ASSETS_FULL_URL . 'css/libraries/aos.min.css' ?>">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/aos.min.js' ?>"></script>

<script>
    AOS.init({
        delay: 100,
        duration: 600
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
