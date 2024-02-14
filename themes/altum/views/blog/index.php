<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fas fa-fw fa-angle-right"></i></li>
            <?php if(!empty($_GET['search'])): ?>
                <li><a href="<?= url('blog') ?>"><?= l('blog.breadcrumb') ?></a></li>
            <?php else: ?>
                <li class="active" aria-current="page"><?= l('blog.breadcrumb') ?></li>
            <?php endif ?>
        </ol>
    </nav>

    <div class="d-flex align-items-center">
        <?php if(!empty($_GET['search'])): ?>
            <h1 class="h3 m-0"><?= sprintf(l('blog.header_search'), input_clean($_GET['search'])) ?></h1>
        <?php else: ?>
            <h1 class="h3 m-0"><?= l('blog.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('blog.subheader') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>

                <a href="<?= SITE_URL . 'blog/feed' ?>" target="_blank" data-toggle="tooltip" title="<?= l('blog.rss') ?>">
                    <i class="fas fa-fw fa-rss text-muted"></i>
                </a>
            </div>
        <?php endif ?>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-lg-8 mb-5 mb-lg-0">
            <?php if(count($data->blog_posts)): ?>
                <?php foreach($data->blog_posts as $blog_post): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>" class="text-decoration-none">
                                <h2 class="h4 mb-1"><?= $blog_post->title ?></h2>
                            </a>

                            <p class="small text-muted">
                                <span data-toggle="tooltip" title="<?= sprintf(l('global.last_datetime_tooltip'), \Altum\Date::get($blog_post->last_datetime, 2)) ?>"><?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($blog_post->datetime, 2)) ?></span>

                                <?php if($blog_post->blog_posts_category_id && isset($data->blog_posts_categories[$blog_post->blog_posts_category_id])): ?>
                                    • <a href="<?= SITE_URL . ($data->blog_posts_categories[$blog_post->blog_posts_category_id]->language ? \Altum\Language::$active_languages[$data->blog_posts_categories[$blog_post->blog_posts_category_id]->language] . '/' : null) . 'blog/category/' . $data->blog_posts_categories[$blog_post->blog_posts_category_id]->url ?>" class="text-muted"><?= $data->blog_posts_categories[$blog_post->blog_posts_category_id]->title ?></a>
                                <?php endif ?>

                                <?php if(settings()->content->blog_views_is_enabled): ?>
                                    <span> • <?= sprintf(l('blog.total_views'), nr($blog_post->total_views)) ?></span>
                                <?php endif ?>
                            </p>

                            <?php if($blog_post->image): ?>
                                <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>">
                                    <img src="<?= \Altum\Uploads::get_full_url('blog') . $blog_post->image ?>" class="blog-post-image img-fluid w-100 rounded mb-3" />
                                </a>
                            <?php endif ?>

                            <p class="m-0"><?= $blog_post->description ?></p>
                        </div>
                    </div>
                <?php endforeach ?>

                <div class="mt-3"><?= $data->pagination ?></div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center justify-content-center py-3">
                            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('blog.no_data') ?>" />
                            <h2 class="h4 text-muted"><?= l('blog.no_data') ?></h2>
                            <p class="text-muted"><?= l('blog.no_data_help') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <?php if(settings()->content->blog_popular_widget_is_enabled || settings()->content->blog_categories_widget_is_enabled || settings()->content->blog_search_widget_is_enabled): ?>
            <div class="col-12 col-lg-4">
                <?php if(settings()->content->blog_search_widget_is_enabled): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="<?= url('blog') ?>" method="get" role="form">
                                <input type="hidden" name="search_by" value="title" />

                                <div class="input-group">
                                    <input type="search" name="search" class="form-control" value="<?= !empty($_GET['search']) ? input_clean($_GET['search']) : null ?>" placeholder="<?= l('global.search') ?>" aria-label="<?= l('global.search') ?>" />

                                    <div class="input-group-append">
                                        <button class="btn btn-outline-gray-300 text-dark" type="submit" data-toggle="tooltip" title="<?= l('global.submit') ?>"><i class="fas fa-fw fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif ?>

                <?php if(settings()->content->blog_categories_widget_is_enabled && count($data->blog_posts_categories)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-3"><?= l('blog.categories') ?></h3>

                            <ul class="list-style-none m-0">
                                <?php foreach($data->blog_posts_categories as $blog_post_category): ?>
                                    <li class="mb-2">
                                        <a href="<?= SITE_URL . ($blog_post_category->language ? \Altum\Language::$active_languages[$blog_post_category->language] . '/' : null) . 'blog/category/' . $blog_post_category->url ?>"><?= $blog_post_category->title ?></a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>

                <?php if(settings()->content->blog_popular_widget_is_enabled && count($data->blog_posts_popular)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-3"><?= l('blog.popular') ?></h3>

                            <ul class="list-style-none m-0">
                                <?php $i = 800; ?>
                                <?php foreach($data->blog_posts_popular as $blog_post): ?>
                                    <li class="mb-2 d-flex align-items-center">
                                        <div class="mr-2 rounded <?= 'bg-gray-' . $i ?>" style="min-width: 1.5rem; min-height: 1.5rem;">
                                            &nbsp;
                                        </div>

                                        <?php $i = $i - 100; ?>

                                        <div>
                                            <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>"><?= $blog_post->title ?></a>
                                            <div class="small">
                                                <?php if($blog_post->blog_posts_category_id && isset($data->blog_posts_categories[$blog_post->blog_posts_category_id])): ?>
                                                    <a href="<?= SITE_URL . ($data->blog_posts_categories[$blog_post->blog_posts_category_id]->language ? \Altum\Language::$active_languages[$data->blog_posts_categories[$blog_post->blog_posts_category_id]->language] . '/' : null) . 'blog/category/' . $data->blog_posts_categories[$blog_post->blog_posts_category_id]->url ?>" class="text-muted"><?= $data->blog_posts_categories[$blog_post->blog_posts_category_id]->title ?></a>
                                                <?php endif ?>

                                                <?php if(settings()->content->blog_views_is_enabled): ?>
                                                    <span class="text-muted"> • <?= sprintf(l('blog.total_views'), nr($blog_post->total_views)) ?></span>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</div>
