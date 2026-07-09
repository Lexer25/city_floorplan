<?php defined('SYSPATH') OR die('No direct script access.');

defined('FLOORPLAN_VERSION') OR define('FLOORPLAN_VERSION', '1.0.1');

// Добавляем в админ-меню (если используется)
if (Kohana::$config->load('adm')) {
    Kohana::$config->load('adm')
        ->set('floorplan', array(
            'title' => 'Планы объекта',
            'url' => 'floorplan',
            'icon' => 'fa-map',
            'order' => 95,
            'children'=> array(
                array(
                    'title' => 'Планы объекта',
                    'url' => 'floorplan',
                    'icon' => 'fa-search',
                ),
                array(
                    'title' => 'Установка БД планов',
                    'url' => 'floorplan/install',
                    'icon' => 'fa-lock',
                ),
            )
        ));
}

// ==========================================
// ОСНОВНЫЕ МАРШРУТЫ
// ==========================================

// AJAX маршруты
Route::set('floorplan_savePositions', 'floorplan/savePositions')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'savePositions',
    ));

Route::set('floorplan_addPointAjax', 'floorplan/addPointAjax')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'addPointAjax',
    ));

Route::set('floorplan_deletePointAjax', 'floorplan/deletePointAjax')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'deletePointAjax',
    ));

Route::set('floorplan_saveZoom', 'floorplan/saveZoom')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'saveZoom',
    ));

// ==========================================
// ОСНОВНЫЕ СТРАНИЦЫ
// ==========================================

Route::set('floorplan_view', 'floorplan/view/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'view',
    ));

Route::set('floorplan_edit', 'floorplan/edit/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'edit',
    ));

Route::set('floorplan_delete', 'floorplan/delete/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'delete',
    ));

Route::set('floorplan_add', 'floorplan/add')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'add',
    ));

// ==========================================
// УПРАВЛЕНИЕ ЗДАНИЯМИ
// ==========================================

Route::set('floorplan_buildings', 'floorplan/buildings')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'buildings',
    ));

Route::set('floorplan_addBuilding', 'floorplan/addBuilding')
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'addBuilding',
    ));

Route::set('floorplan_editBuilding', 'floorplan/editBuilding/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'editBuilding',
    ));

Route::set('floorplan_deleteBuilding', 'floorplan/deleteBuilding/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Floorplan',
        'action' => 'deleteBuilding',
    ));

// ==========================================
// УСТАНОВКА БАЗЫ ДАННЫХ
// ==========================================

// Маршрут для страницы установки
Route::set('floorplan_install', 'floorplan/install(/<action>)')
    ->defaults(array(
        'controller' => 'floorplan_Install',
        'action' => 'index',
    ));

// ==========================================
// СКАЧИВАНИЕ SQL-СКРИПТОВ
// ==========================================
Route::set('floorplan_downloadSql', 'floorplan/install/downloadSql(/<type>)')
    ->defaults(array(
        'controller' => 'floorplan_Install',
        'action' => 'downloadSql',
        'type' => 'install',
    ));