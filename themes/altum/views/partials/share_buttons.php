<?php defined('ALTUMCODE') || die() ?>

<a href="mailto:?body=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Email') ?>">
    <i class="fas fa-fw fa-envelope"></i>
</a>

<?php if($data->print_is_enabled ?? true): ?>
<button type="button" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= l('page.print') ?>" onclick="window.print()">
    <i class="fas fa-fw fa-sm fa-print"></i>
</button>
<?php endif ?>

<a href="https://www.facebook.com/sharer/sharer.php?u=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Facebook') ?>">
    <i class="fab fa-fw fa-facebook"></i>
</a>
<a href="https://x.com/share?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'X') ?>">
    <i class="fab fa-fw fa-twitter"></i>
</a>
<a href="https://pinterest.com/pin/create/link/?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Pinterest') ?>">
    <i class="fab fa-fw fa-pinterest"></i>
</a>
<a href="https://linkedin.com/shareArticle?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'LinkedIn') ?>">
    <i class="fab fa-fw fa-linkedin"></i>
</a>
<a href="https://www.reddit.com/submit?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Reddit') ?>">
    <i class="fab fa-fw fa-reddit"></i>
</a>
<a href="https://wa.me/?text=<?= $data->url ?>" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Whatsapp') ?>">
    <i class="fab fa-fw fa-whatsapp"></i>
</a>

<a href="https://t.me/share/url?url=<?= $data->url ?>" class="<?= $data->class ?>" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Telegram') ?>">
    <i class="fab fa-fw fa-telegram"></i>
</a>
