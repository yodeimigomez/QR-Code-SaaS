<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><i class="fas fa-fw fa-xs fa-adjust mr-1"></i> <?= l('pixels.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('pixels.subheader') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-xl-auto d-flex">
            <div>
                <?php if($this->user->plan_settings->pixels_limit != -1 && $data->total_pixels >= $this->user->plan_settings->pixels_limit): ?>
                    <button type="button" class="btn btn-primary disabled" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>">
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('pixels.create') ?>
                    </button>
                <?php else: ?>
                    <a href="<?= url('pixel-create') ?>" class="btn btn-primary" data-toggle="tooltip" data-html="true" title="<?= get_plan_feature_limit_info($data->total_pixels, $this->user->plan_settings->pixels_limit) ?>">
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('pixels.create') ?>
                    </a>
                <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-light dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>">
                        <i class="fas fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('pixels?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fas fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('pixels?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                            <i class="fas fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-dark' : 'btn-light' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.filters.header') ?>">
                    <i class="fas fa-fw fa-sm fa-filter"></i>
                </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown custom-scrollbar">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('pixels') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="search_by" class="custom-select custom-select-sm">
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_type" class="small"><?= l('global.type') ?></label>
                                <select name="type" id="filters_type" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <?php foreach(require APP_PATH . 'includes/l/pixels.php' as $pixel_key => $pixel): ?>
                                        <option value="<?= $pixel_key ?>" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == $pixel_key ? 'selected="selected"' : null ?>><?= $pixel['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="order_by" class="custom-select custom-select-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="last_datetime" <?= $data->filters->order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_type" class="small"><?= l('global.filters.order_type') ?></label>
                                <select name="order_type" id="order_type" class="custom-select custom-select-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                                <select name="results_per_page" id="results_per_page" class="custom-select custom-select-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($data->pixels)): ?>
    <?php $available_pixels = require APP_PATH . 'includes/l/pixels.php'; ?>
        <div class="table-responsive table-custom-container custom-scrollbar">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('global.name') ?></th>
                    <th><?= l('global.type') ?></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->pixels as $row): ?>

                    <tr>
                        <td class="text-nowrap">
                            <a href="<?= url('pixel-update/' . $row->pixel_id) ?>"><?= $row->name ?></a>
                        </td>

                        <td class="text-nowrap">
                            <span class="badge badge-light">
                                <i class="<?= $available_pixels[$row->type]['icon'] ?> fa-fw fa-sm mr-1" style="color: <?= $available_pixels[$row->type]['color'] ?>"></i>
                                <?= $available_pixels[$row->type]['name'] ?>
                            </span>
                        </td>

                        <td class="text-nowrap text-muted">
                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>') ?>">
                                <i class="fas fa-fw fa-calendar text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' : '-')) ?>">
                                <i class="fas fa-fw fa-history text-muted"></i>
                            </span>
                        </td>

                        <td>
                            <div class="d-flex justify-content-end">
                                <?= include_view(THEME_PATH . 'views/pixels/pixel_dropdown_button.php', ['id' => $row->pixel_id, 'resource_name' => $row->name]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('pixels.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('pixels.no_data') ?></h2>
                    <p class="text-muted"><?= l('pixels.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>

</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'pixel',
    'resource_id' => 'pixel_id',
    'has_dynamic_resource_name' => true,
    'path' => 'pixels/delete'
]), 'modals'); ?>
