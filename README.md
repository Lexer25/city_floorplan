# floorplan

Модуль для создания и редактирования графических планов объекта с точками прохода.

## Функциональность

- Загрузка и отображение планов объекта
- Размещение точек прохода на плане (двери, турникеты, считыватели)
- Перетаскивание точек мышью
- Визуализация статуса устройств (Online/Offline)
- Привязка точек к реальным устройствам

## Установка

1. Скопируйте модуль в `modules/floorplan`
2. Добавьте в `bootstrap.php`: `'floorplan' => MODPATH.'floorplan'`
3. Выполните SQL-скрипт для создания таблиц
4. Создайте директорию `media/floorplan/` и установите права на запись

## Использование

- `/floorplan` - список планов
- `/floorplan/view/<id>` - просмотр плана
- `/floorplan/edit/<id>` - редактирование плана
- `/floorplan/add` - добавление нового плана

modules/floorplan/
├── classes/
│   ├── Controller/
│   │   └── Floorplan.php
│   └── Model/
│       └── Floorplanm.php
├── views/
│   └── floorplan/
│       ├── index.php
│       ├── view.php          (с масштабированием)
│       ├── edit.php          (с масштабированием)
│       ├── add.php
│       ├── zoom_script.php   
│       ├── buildings.php
│       ├── add_building.php
│       └── edit_building.php
├── media/
│   └── floorplan/
│       └── css/
│           └── floorplan.css
├── init.php
└── README.md

https://localhost/city/index.php/floorplan/view/1?id_dev=783