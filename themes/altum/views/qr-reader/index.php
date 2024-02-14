<?php defined('ALTUMCODE') || die() ?>

<div class="container text-center">
    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="index-header mb-2"><?= l('qr_reader.header') ?></h1>
    <p class="index-subheader mb-5"><?= l('qr_reader.subheader') ?></p>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-lg-7 d-print-none mb-5 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">

                    <form action="" method="post" role="form" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                        <div class="form-group">
                            <label for="image"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('qr_reader.image') ?></label>
                            <input type="file" id="image" name="image" accept=".png, .jpg, .jpeg, .svg, .webp" class="form-control-file altum-file-input <?= \Altum\Alerts::has_field_errors('image') ? 'is-invalid' : null ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('image') ?>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5 mb-5 mb-lg-0">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="result"><?= l('qr_reader.result') ?></label>
                                <div>
                                    <button
                                            type="button"
                                            class="btn btn-link text-secondary"
                                            data-toggle="tooltip"
                                            title="<?= l('global.clipboard_copy') ?>"
                                            aria-label="<?= l('global.clipboard_copy') ?>"
                                            data-copy="<?= l('global.clipboard_copy') ?>"
                                            data-copied="<?= l('global.clipboard_copied') ?>"
                                            data-clipboard-target="#result"
                                            data-clipboard-text
                                    >
                                        <i class="fas fa-fw fa-sm fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <textarea id="result" class="form-control"></textarea>
                        </div>

                    </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="card">
            <div class="card-body">
                <?= l('qr_reader.extra_content') ?>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/html5-qrcode.min.js' ?>"></script>

<script>
    'use strict';
    const html5QrCode = new Html5Qrcode('image');

    const image = document.getElementById('image');
    image.addEventListener('change', event => {
        const file = image.files[0];

        if(!file) {
            return;
        }

        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                document.querySelector('#result').value = decodedText;
            })
            .catch(err => {
                document.querySelector('#result').value = <?= json_encode(l('global.no_data')) ?>;
            });
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

