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

MODPATH/floorplan/views/floorplan/
├── view.php                          # Основной файл (маленький)
└── view/
    ├── header.php                    # Заголовок и информация
    ├── toolbar.php                   # Панель управления
    ├── canvas.php                    # План с точками
    ├── points_table.php              # Таблица точек
    ├── related_table.php             # Таблица связанных устройств
    ├── scripts.php                   # JavaScript
    └── styles.php                    # CSS
	
	
MODPATH/floorplan/views/floorplan/
├── edit.php                          # Основной файл (маленький)
└── edit/
    ├── header.php                    # Проверка БД и информация о подсвеченной точке
    ├── form_update.php               # Форма обновления плана
    ├── floor_selector.php            # Переключатель этажей
    ├── toolbar.php                   # Панель управления масштабом + режим клика
    ├── canvas.php                    # Контейнер с планом и точками
    ├── points_table.php              # Таблица точек
    ├── form_reader.php               # Форма добавления считывателя
    ├── form_controller.php           # Форма добавления контроллера
    ├── print_elements.php            # Элементы для печати
    ├── device_panel.php              # Боковая панель с устройствами
    ├── modals.php                    # Модальные окна (этажи)
    ├── dialog.php                    # JQUERY UI DIALOG
    ├── scripts.php                   # JavaScript
    └── styles.php                    # CSS

https://localhost/city/index.php/floorplan/view?id_dev=783