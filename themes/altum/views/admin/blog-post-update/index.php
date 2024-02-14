<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/blog-posts') ?>"><?= l('admin_pages.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_blog_post_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fas fa-fw fa-xs fa-paste text-primary-900 mr-2"></i> <?= l('admin_blog_post_update.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/blog-posts/admin_blog_post_dropdown_button.php', ['id' => $data->blog_post->blog_post_id, 'resource_name' => $data->blog_post->title, 'url' => $data->blog_post->url, 'language' => $data->blog_post->language]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="url"><i class="fas fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('global.url') ?></label>
                <div class="input-group">
                    <div id="url_prepend" class="input-group-prepend">
                        <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) . 'blog/' ?></span>
                    </div>

                    <input id="url" type="text" name="url" class="form-control <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" placeholder="<?= l('global.url_placeholder') ?>" value="<?= $data->blog_post->url ?>" onchange="update_this_value(this, get_slug)" onkeyup="update_this_value(this, get_slug)" maxlength="256" required="required" />
                    <?= \Altum\Alerts::output_field_error('url') ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title"><i class="fas fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('admin_blog.main.title') ?></label>
                <input id="title" type="text" name="title" class="form-control <?= \Altum\Alerts::has_field_errors('title') ? 'is-invalid' : null ?>" value="<?= $data->blog_post->title ?>" maxlength="256" required="required" />
                <?= \Altum\Alerts::output_field_error('title') ?>
            </div>

            <div class="form-group">
                <label for="description"><i class="fas fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('global.description') ?></label>
                <input id="description" type="text" name="description" class="form-control" value="<?= $data->blog_post->description ?>" maxlength="256" />
            </div>

            <div class="form-group">
                <label for="image"><i class="fas fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('admin_blog.main.image') ?></label>
                <?php if(!empty($data->blog_post->image)): ?>
                    <div class="m-1">
                        <img src="<?= \Altum\Uploads::get_full_url('blog') . $data->blog_post->image ?>" class="img-fluid" style="max-height: 5rem;height: 5rem;" />
                    </div>
                    <div class="custom-control custom-checkbox my-2">
                        <input id="image_remove" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#image').classList.add('d-none') : document.querySelector('#image').classList.remove('d-none')">
                        <label class="custom-control-label" for="image_remove">
                            <span class="text-muted"><?= l('global.delete_file') ?></span>
                        </label>
                    </div>
                <?php endif ?>
                <input id="image" type="file" name="image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('blog') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('blog')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>

            <div class="form-group">
                <label for="editor"><i class="fas fa-fw fa-sm fa-newspaper text-muted mr-1"></i> <?= l('admin_blog.main.editor') ?></label>
                <div class="row btn-group-toggle" data-toggle="buttons">
                    <div class="col-12 col-lg-4">
                        <label class="btn btn-light btn-block <?= $data->blog_post->editor == 'wysiwyg' ? 'active"' : null?>">
                            <input type="radio" name="editor" value="wysiwyg" class="custom-control-input" <?= $data->blog_post->editor == 'wysiwyg' ? 'checked="checked"' : null?> required="required" />
                            <i class="fas fa-eye fa-fw fa-sm mr-1"></i> <?= l('admin_blog.main.editor_wysiwyg') ?>
                        </label>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="btn btn-light btn-block <?= $data->blog_post->editor == 'blocks' ? 'active"' : null?>">
                            <input type="radio" name="editor" value="blocks" class="custom-control-input" <?= $data->blog_post->editor == 'blocks' ? 'checked="checked"' : null?> required="required" />
                            <i class="fas fa-th-large fa-fw fa-sm mr-1"></i> <?= l('admin_blog.main.editor_blocks') ?>
                        </label>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="btn btn-light btn-block <?= $data->blog_post->editor == 'raw' ? 'active"' : null?>">
                            <input type="radio" name="editor" value="raw" class="custom-control-input" <?= $data->blog_post->editor == 'raw' ? 'checked="checked"' : null?> required="required" />
                            <i class="fas fa-pen-nib fa-fw fa-sm mr-1"></i> <?= l('admin_blog.main.editor_raw') ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="content"><i class="fas fa-fw fa-sm fa-paragraph text-muted mr-1"></i> <?= l('admin_blog.main.content') ?></label>
                <div id="quill_container">
                    <div id="quill"></div>
                </div>
                <div class="bg-gray-100 rounded p-3" id="editorjs"></div>
                <textarea name="content" id="content" class="form-control d-none" style="height: 15rem;"><?= $data->blog_post->content ?></textarea>
            </div>

            <div class="form-group">
                <label for="blog_posts_category_id"><i class="fas fa-fw fa-sm fa-map text-muted mr-1"></i> <?= l('admin_blog.main.blog_posts_category_id') ?></label>
                <select id="blog_posts_category_id" name="blog_posts_category_id" class="custom-select">
                    <?php foreach($data->blog_posts_categories as $row): ?>
                        <option value="<?= $row->blog_posts_category_id ?>" <?= $data->blog_post->blog_posts_category_id == $row->blog_posts_category_id ? 'selected="selected"' : null ?>><?= $row->title ?></option>
                    <?php endforeach ?>
                    <option value="" <?= !$data->blog_post->blog_posts_category_id ? 'selected="selected"' : null ?>><?= l('admin_blog.main.blog_posts_category_id_null') ?></option>
                </select>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="is_published" name="is_published" type="checkbox" class="custom-control-input" <?= $data->blog_post->is_published ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="is_published"><i class="fas fa-fw fa-sm fa-dot-circle text-muted mr-1"></i> <?= l('admin_blog.main.is_published') ?></label>
            </div>

            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#advanced_container" aria-expanded="false" aria-controls="advanced_container">
                <i class="fas fa-fw fa-user-tie fa-sm mr-1"></i> <?= l('admin_blog.main.advanced') ?>
            </button>

            <div class="collapse" id="advanced_container">
                <div class="form-group">
                    <label for="keywords"><i class="fas fa-fw fa-sm fa-file-word text-muted mr-1"></i> <?= l('admin_blog.main.keywords') ?></label>
                    <input id="keywords" type="text" name="keywords" class="form-control" value="<?= $data->blog_post->keywords ?>" maxlength="256" />
                </div>

                <div class="form-group">
                    <label for="language"><i class="fas fa-fw fa-sm fa-language text-muted mr-1"></i> <?= l('global.language') ?></label>
                    <select id="language" name="language" class="custom-select">
                        <option value="" <?= !$data->blog_post->language ? 'selected="selected"' : null ?>><?= l('global.all') ?></option>
                        <?php foreach(\Altum\Language::$languages as $language): ?>
                            <option value="<?= $language['name'] ?>" <?= $data->blog_post->language == $language['name'] ? 'selected="selected"' : null ?>><?= $language['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/libraries/quill.snow.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">

<style>
    .codex-editor__redactor {
        padding-bottom: 0 !important;
    }
</style>
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/quill.min.js' ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script><!-- Header -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest"></script><!-- Image -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script><!-- List -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script><!-- Link -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest"></script><!-- Code -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@latest"></script><!-- Raw HTML -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script><!-- Embed -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script><!-- Delimiter -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script><!-- Text Marker -->

<!-- Load Editor.js's Core -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>

<script>
    'use strict';

    /* EditorJS initiatilization */
    let editorjs = new EditorJS({
        readOnly: false,
        holder: 'editorjs',

        /* Data */
        data: <?= json_decode($data->blog_post->content) ? $data->blog_post->content : '{}' ?>,

        /* Tolls */
        tools: {
            header: {
                class: Header,
                inlineToolbar: true,
            },

            list: {
                class: List,
                inlineToolbar: true,
            },

            delimiter: Delimiter,

            marker: Marker,

            code: CodeTool,

            image: SimpleImage,

            embed: Embed,

            raw: RawTool,
        },
    });

    (async () => {
        try {
            await editorjs.isReady;
        } catch (reason) {
            console.log(`Editor.js initialization failed because of ${reason}`)
        }
    })();

    /* Initiate QuillJs */
    let quill = new Quill('#quill', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ "font": [] }, { "size": ["small", false, "large", "huge"] }],
                ["bold", "italic", "underline", "strike"],
                [{ "color": [] }, { "background": [] }],
                [{ "script": "sub" }, { "script": "super" }],
                [{ "header": 1 }, { "header": 2 }, "blockquote", "code-block"],
                [{ "list": "ordered" }, { "list": "bullet" }, { "indent": "-1" }, { "indent": "+1" }],
                [{ "direction": "rtl" }, { "align": [] }],
                ["link", "image", "video", "formula"],
                ["clean"]
            ]
        },
    });
    quill.root.innerHTML = document.querySelector('#content').value;

    /* Handle form submission with the editor */
    document.querySelector('form').addEventListener('submit', async event => {
        let editor = document.querySelector('input[name="editor"]:checked').value;

        if(editor == 'wysiwyg') {
            document.querySelector('#content').value = quill.root.innerHTML;
        }

        if(editor == 'blocks') {
            let data = await editorjs.save();
            document.querySelector('#content').value = JSON.stringify(data);
        }
    });

    /* Editor change handlers */
    let current_editor = document.querySelector('input[name="editor"]:checked').value;

    let editor_handler = async (event = null) => {
        if(event && !confirm(<?= json_encode(l('admin_resources.main.editor_confirm')) ?>)) {
            document.querySelector('input[name="editor"]:checked').value = current_editor;
            return;
        }

        let editor = document.querySelector('input[name="editor"]:checked').value;

        switch(editor) {
            case 'wysiwyg':
                document.querySelector('#quill_container').classList.remove('d-none');
                quill.enable(true);
                document.querySelector('#editorjs').classList.add('d-none');
                document.querySelector('#content').classList.add('d-none');
                break;

            case 'blocks':
                document.querySelector('#quill_container').classList.add('d-none');
                quill.enable(false);
                document.querySelector('#editorjs').classList.remove('d-none');
                document.querySelector('#content').classList.add('d-none');
                break;

            case 'raw':
                document.querySelector('#quill_container').classList.add('d-none');
                quill.enable(false);
                document.querySelector('#editorjs').classList.add('d-none');
                document.querySelector('#content').classList.remove('d-none');
                break;
        }

        current_editor = document.querySelector('input[name="editor"]:checked').value;
    };

    document.querySelectorAll('input[name="editor"]').forEach(element => element.addEventListener('change', editor_handler));
    editor_handler();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'blog_post',
    'resource_id' => 'blog_post_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/blog-posts/delete/'
]), 'modals'); ?>
