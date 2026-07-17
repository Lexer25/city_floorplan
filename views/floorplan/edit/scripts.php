<!-- ========================================== -->
<!-- ПОДКЛЮЧЕНИЕ МАСШТАБИРОВАНИЯ                -->
<!-- ========================================== -->
<script>
window.floorplanId = <?php echo $current_floor_id; ?>;
window.floorplanWidth = <?php echo $current_floor['width']; ?>;
window.floorplanHeight = <?php echo $current_floor['height']; ?>;

<?php if (isset($highlightData) && $highlightData): ?>
window.highlightPointId = <?php echo $highlightData['id_point']; ?>;
window.highlightX = <?php echo $highlightData['x_pos']; ?>;
window.highlightY = <?php echo $highlightData['y_pos']; ?>;
<?php endif; ?>
</script>

<?php include Kohana::find_file('views', 'floorplan/zoom_script'); ?>

<!-- ========================================== -->
<!-- ОСНОВНЫЕ СКРИПТЫ                           -->
<!-- ========================================== -->
<script>
$(document).ready(function() {
    FloorplanZoom.init(window.floorplanId);
    
    <?php if (isset($highlightData) && $highlightData): ?>
    setTimeout(function() {
        var $point = $('.floorplan-point.highlighted');
        if ($point.length) {
            var $container = $('#floorplanScrollable');
            var containerWidth = $container.width();
            var containerHeight = $container.height();
            var pointLeft = $point.position().left;
            var pointTop = $point.position().top;
            var scrollLeft = pointLeft - containerWidth / 2;
            var scrollTop = pointTop - containerHeight / 2;
            
            $container.animate({
                scrollLeft: scrollLeft,
                scrollTop: scrollTop
            }, 500);
        }
    }, 600);
    <?php endif; ?>
    
    // ==========================================
    // ИНИЦИАЛИЗАЦИЯ JQUERY UI DIALOG
    // ==========================================
    $('#clickAddPointDialog').dialog({
        autoOpen: false,
        modal: true,
        width: 450,
        height: 'auto',
        resizable: false,
        draggable: true,
        closeOnEscape: true,
        dialogClass: 'click-point-dialog',
        buttons: [
            {
                text: 'Отмена',
                class: 'btn btn-default',
                click: function() {
                    $(this).dialog('close');
                }
            },
            {
                text: 'Добавить точку',
                class: 'btn btn-success',
                id: 'clickSavePointBtn',
                click: function() {
                    saveClickPoint();
                }
            }
        ],
        open: function() {
            setTimeout(function() {
                $('#clickLabel').focus();
            }, 300);
        }
    });
    
    $('#clickLabel').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveClickPoint();
        }
    });
    
    $('#clickDeviceId').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveClickPoint();
        }
    });
    
    // ==========================================
    // ВАЛИДАЦИЯ ФОРМ
    // ==========================================
    
    // Валидация формы добавления считывателя
    $('#addReaderForm').on('submit', function(e) {
        var x = parseFloat($('input[name="x"]').val());
        var y = parseFloat($('input[name="y"]').val());
        
        if (isNaN(x) || x < 0 || x > 100) {
            e.preventDefault();
            showNotification('X должен быть от 0 до 100%', 'error');
            $('#readerX').focus().select();
            return false;
        }
        
        if (isNaN(y) || y < 0 || y > 100) {
            e.preventDefault();
            showNotification('Y должен быть от 0 до 100%', 'error');
            $('#readerY').focus().select();
            return false;
        }
        
        var deviceId = $('#readerDevice').val();
        if (!deviceId) {
            e.preventDefault();
            showNotification('Выберите устройство', 'warning');
            $('#readerDevice').focus();
            return false;
        }
        
        var $btn = $('#submitReaderBtn');
        $btn.html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Добавление...')
            .prop('disabled', true);
    });
    
    // Валидация формы добавления контроллера
// Валидация формы добавления контроллера
$('#addControllerForm').on('submit', function(e) {
    var xVal = $('input[name="x"]', this).val().trim();
    var yVal = $('input[name="y"]', this).val().trim();
    
    var x = parseFloat(xVal);
    var y = parseFloat(yVal);
    
    // Если поле пустое или не число
    if (xVal === '' || isNaN(x)) {
        e.preventDefault();
        showNotification('Введите значение X (число)', 'error');
        $('#controllerX').focus().select();
        return false;
    }
    
    if (x < 0 || x > 100) {
        e.preventDefault();
        showNotification('X должен быть от 0 до 100% (сейчас: ' + x + ')', 'error');
        $('#controllerX').focus().select();
        return false;
    }
    
    if (yVal === '' || isNaN(y)) {
        e.preventDefault();
        showNotification('Введите значение Y (число)', 'error');
        $('#controllerY').focus().select();
        return false;
    }
    
    if (y < 0 || y > 100) {
        e.preventDefault();
        showNotification('Y должен быть от 0 до 100% (сейчас: ' + y + ')', 'error');
        $('#controllerY').focus().select();
        return false;
    }
    
    var deviceId = $('#controllerDevice').val();
    if (!deviceId) {
        e.preventDefault();
        showNotification('Выберите устройство', 'warning');
        $('#controllerDevice').focus();
        return false;
    }
    
    var $btn = $('#submitControllerBtn');
    $btn.html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Добавление...')
        .prop('disabled', true);
});
});

// ==========================================
// РЕЖИМ КЛИКА ДЛЯ ДОБАВЛЕНИЯ ТОЧЕК
// ==========================================

var clickModeEnabled = false;
var clickX = 0;
var clickY = 0;
var selectedDeviceId = null;
var selectedDeviceName = null;
var selectedDeviceType = null;
var previewPoint = null;

function toggleClickMode() {
    clickModeEnabled = !clickModeEnabled;
    
    var $btn = $('#toggleClickMode');
    var $status = $('#clickModeStatus');
    var $canvas = $('#floorplanCanvas');
    var $image = $('#floorplanImage');
    
    if (clickModeEnabled) {
        $btn.removeClass('btn-success').addClass('btn-danger');
        $btn.html('<span class="glyphicon glyphicon-hand-up"></span> Выйти из режима');
        $status.show();
        $canvas.css('cursor', 'crosshair');
        $image.css('cursor', 'crosshair');
        $image.attr('title', 'Кликните для добавления точки');
        
        if (selectedDeviceName) {
            var icon = selectedDeviceType === 'reader' ? '📡' : '⚙️';
            $('#selectedDeviceDisplay').text(icon + ' ' + selectedDeviceName + ' (' + selectedDeviceType + ')');
        } else {
            $('#selectedDeviceDisplay').text(' (выберите устройство на панели)').css('color', '#d9534f');
        }
    } else {
        $btn.removeClass('btn-danger').addClass('btn-success');
        $btn.html('<span class="glyphicon glyphicon-hand-up"></span> Режим клика');
        $status.hide();
        $canvas.css('cursor', 'default');
        $image.css('cursor', 'default');
        $image.attr('title', '');
        $('#selectedDeviceDisplay').text('');
        $('#clickCoords').text('');
        
        if (previewPoint) {
            previewPoint.remove();
            previewPoint = null;
        }
    }
}

// ==========================================
// БОКОВАЯ ПАНЕЛЬ УСТРОЙСТВ
// ==========================================

var panelVisible = true;

function toggleDevicePanel() {
    panelVisible = !panelVisible;
    var $panel = $('#devicePanel');
    var $anchorIcon = $('#anchorIcon');
    
    if (panelVisible) {
        $panel.css('transform', 'translateX(0)');
        $anchorIcon.removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
        $anchorIcon.attr('title', 'Свернуть панель');
    } else {
        $panel.css('transform', 'translateX(100%)');
        $anchorIcon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
        $anchorIcon.attr('title', 'Развернуть панель');
    }
}

function selectDevice(el, type) {
    $('.device-item').removeClass('selected').css('border-left-color', function() {
        if ($(this).hasClass('reader-item')) return '#5bc0de';
        if ($(this).hasClass('controller-item')) return '#f0ad4e';
        return '#337ab7';
    }).css('background', function() {
        if ($(this).hasClass('reader-item')) return '#f9f9f9';
        if ($(this).hasClass('controller-item')) return '#f9f9f9';
        return '#f9f9f9';
    });
    
    $(el).addClass('selected');
    if (type === 'reader') {
        $(el).css('border-left-color', '#ff9800').css('background', '#e8f0fe');
        selectedDeviceType = 'reader';
    } else if (type === 'controller') {
        $(el).css('border-left-color', '#ff9800').css('background', '#fff3e0');
        selectedDeviceType = 'controller';
    }
    
    selectedDeviceId = $(el).data('device-id');
    selectedDeviceName = $(el).data('device-name');
    
    $('#selectedDeviceInfo').text('Выбрано: ' + selectedDeviceName + ' (' + selectedDeviceType + ')');
    
    if (selectedDeviceType === 'reader') {
        $('#readerDevice').val(selectedDeviceId);
        $('#readerLabel').val(selectedDeviceName || '');
    } else if (selectedDeviceType === 'controller') {
        $('#controllerDevice').val(selectedDeviceId);
        $('#controllerLabel').val(selectedDeviceName || '');
    }
    
    if (clickModeEnabled) {
        var icon = selectedDeviceType === 'reader' ? '📡' : '⚙️';
        $('#selectedDeviceDisplay').text(icon + ' ' + selectedDeviceName + ' (' + selectedDeviceType + ')').css('color', '#337ab7');
    }
}

// ==========================================
// ПРЕДВАРИТЕЛЬНЫЙ ПРОСМОТР ПРИ КЛИКЕ
// ==========================================

$('#floorplanImage').on('mousemove', function(e) {
    if (!clickModeEnabled) return;
    if (!selectedDeviceId) {
        $('#clickCoords').text('Выберите устройство!');
        return;
    }
    
    var $this = $(this);
    var offset = $this.offset();
    var x = e.pageX - offset.left;
    var y = e.pageY - offset.top;
    var width = $this.width();
    var height = $this.height();
    
    var xPercent = (x / width) * 100;
    var yPercent = (y / height) * 100;
    
    xPercent = Math.max(0, Math.min(100, xPercent));
    yPercent = Math.max(0, Math.min(100, yPercent));
    
    if (!previewPoint) {
        var icon = selectedDeviceType === 'reader' ? '📡' : '⚙️';
        var color = selectedDeviceType === 'reader' ? '#5bc0de' : '#f0ad4e';
        previewPoint = $('<div class="floorplan-point preview" style="position: absolute; transform: translate(-50%, -50%); pointer-events: none; opacity: 0.6; z-index: 100;">' +
            '<div class="point-icon">' +
            '<span style="font-size: 32px; color: ' + color + ';">' + icon + '</span>' +
            '</div>' +
            '<div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 10px; white-space: nowrap; background: rgba(255,152,0,0.9); color: #fff; padding: 2px 8px; border-radius: 3px;">' +
            selectedDeviceName + ' (' + selectedDeviceType + ')' +
            '</div>' +
            '</div>');
        $('#floorplanCanvas').append(previewPoint);
    }
    
    previewPoint.css({
        left: xPercent + '%',
        top: yPercent + '%'
    });
    
    $('#clickCoords').text('X: ' + Math.round(xPercent) + '% Y: ' + Math.round(yPercent) + '%');
});

$('#floorplanImage').on('mouseleave', function() {
    if (previewPoint) {
        previewPoint.remove();
        previewPoint = null;
        $('#clickCoords').text('');
    }
});

// ==========================================
// КЛИК ДЛЯ ДОБАВЛЕНИЯ ТОЧКИ
// ==========================================

$('#floorplanImage').on('click', function(e) {
    if (!clickModeEnabled) return;
    
    if (!selectedDeviceId) {
        showNotification('Сначала выберите устройство на боковой панели', 'warning');
        return;
    }
    
    var $this = $(this);
    var offset = $this.offset();
    var x = e.pageX - offset.left;
    var y = e.pageY - offset.top;
    var width = $this.width();
    var height = $this.height();
    
    var xPercent = Math.round(((x / width) * 1000) / 10);
    var yPercent = Math.round(((y / height) * 1000) / 10);
    
    xPercent = Math.max(0, Math.min(100, xPercent));
    yPercent = Math.max(0, Math.min(100, yPercent));
    
    clickX = xPercent;
    clickY = yPercent;
    
    $('#clickX').val(xPercent + '%');
    $('#clickY').val(yPercent + '%');
    $('#clickDeviceId').val(selectedDeviceId);
    $('#clickLabel').val(selectedDeviceName || '');
    $('#clickPointType').val(selectedDeviceType === 'reader' ? 'reader' : 'controller');
    
    $('#clickAddPointDialog').dialog('open');
});

// ==========================================
// ПЕРЕТАСКИВАНИЕ ТОЧЕК
// ==========================================

$(document).ready(function() {
    var $points = $('.floorplan-point.draggable');
    var $container = $('#floorplanCanvas');
    var isDragging = false;

    if ($points.length > 0 && $container.length > 0) {
        $points.draggable({
            containment: $container,
            cursor: 'grab',
            handle: '.point-icon',
            start: function(e, ui) {
                isDragging = true;
                $(this).css('z-index', 20);
                $(this).find('.point-actions').show();
                $(this).addClass('dragging');
                // Показываем координаты только в статусной строке
                updateDragCoordinates(ui.position.left, ui.position.top, $(this));
            },
            drag: function(e, ui) {
                updateDragCoordinates(ui.position.left, ui.position.top, $(this));
            },
            stop: function(e, ui) {
                isDragging = false;
                var $point = $(this);
                var pointId = $point.data('point-id');
                var parentWidth = $container.width();
                var parentHeight = $container.height();
                
                var left = ui.position.left;
                var top = ui.position.top;
                
                var pointWidth = $point.outerWidth();
                var pointHeight = $point.outerHeight();
                
                var xPercent = ((left + pointWidth/2) / parentWidth) * 100;
                var yPercent = ((top + pointHeight/2) / parentHeight) * 100;
                
                xPercent = Math.max(0, Math.min(100, xPercent));
                yPercent = Math.max(0, Math.min(100, yPercent));
                
                $point.css('left', xPercent + '%');
                $point.css('top', yPercent + '%');
                
                $point.removeClass('dragging');
                hideDragCoordinates();
                
                savePointPosition(pointId, xPercent, yPercent);
            }
        });

        $points.hover(
            function() {
                if (!isDragging) {
                    $(this).find('.point-actions').show();
                    showPointInfo($(this));
                }
            },
            function() {
                if (!isDragging) {
                    $(this).find('.point-actions').hide();
                    hidePointInfo();
                }
            }
        );
    }
});

// ==========================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ==========================================

// Отключаем всплывающие подсказки координат при перетаскивании
function showDragCoordinates(left, top, $point) {
    // Подсказка координат при перетаскивании - отключена
}

function updateDragCoordinates(left, top, $point) {
    // Показываем координаты только в статусной строке (уже есть)
    // Ничего не делаем, чтобы не создавать лишних элементов
}

function hideDragCoordinates() {
    // Ничего не делаем
}

function showPointInfo($point) {
    var $tooltip = $('#pointInfo');
    if (!$tooltip.length) {
        $tooltip = $('<div id="pointInfo" style="position: fixed; background: rgba(0,0,0,0.8); color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 9999; pointer-events: none; display: none;"></div>');
        $('body').append($tooltip);
    }
    
    var pointId = $point.data('point-id');
    var deviceId = $point.data('device-id');
    var label = $point.find('.point-label').text() || 'Без метки';
    var xPos = parseFloat($point.css('left'));
    var yPos = parseFloat($point.css('top'));
    
    $tooltip.html(
        'ID: ' + pointId + 
        ' | Устр: ' + (deviceId || '—') + 
        ' | X: ' + Math.round(xPos) + '%' + 
        ' | Y: ' + Math.round(yPos) + '%' +
        ' | ' + label
    );
    
    var offset = $point.offset();
    $tooltip.css({
        left: (offset.left + 30) + 'px',
        top: (offset.top - 10) + 'px',
        display: 'block'
    });
}

function hidePointInfo() {
    $('#pointInfo').hide();
}

function savePointPosition(pointId, x, y) {
    var $indicator = $('#saveIndicator');
    if (!$indicator.length) {
        $indicator = $('<div id="saveIndicator" style="position: fixed; bottom: 20px; right: 20px; background: #5cb85c; color: #fff; padding: 10px 20px; border-radius: 4px; display: none; z-index: 9999;"></div>');
        $('body').append($indicator);
    }
    
    $indicator.text('Сохранение...').fadeIn(200);
    
    var data = {
        points: [{
            id: pointId,
            x: x,
            y: y
        }]
    };
    
    $.ajax({
        url: '<?php echo URL::site("floorplan/savePositions"); ?>',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $indicator.text('✓ Сохранено!').css('background', '#5cb85c').fadeOut(1000);
                console.log('Position saved for point ' + pointId);
            } else {
                $indicator.text('✗ Ошибка!').css('background', '#d9534f').fadeOut(2000);
            }
        },
        error: function() {
            $indicator.text('✗ Ошибка сохранения!').css('background', '#d9534f').fadeOut(2000);
        }
    });
}

function saveClickPoint() {
    var deviceId = $('#clickDeviceId').val();
    var pointType = $('#clickPointType').val();
    var label = $('#clickLabel').val();
    var floorplanId = <?php echo $current_floor_id; ?>;
    
    if (!deviceId) {
        showNotification('Пожалуйста, выберите устройство', 'warning');
        $('#clickDeviceId').focus();
        return;
    }
    
    if (clickX === 0 && clickY === 0) {
        showNotification('Ошибка: координаты не заданы', 'error');
        return;
    }
    
    var dialog = $('#clickAddPointDialog');
    var buttons = dialog.dialog('option', 'buttons');
    
    if (buttons && buttons[1]) {
        buttons[1].text = '<span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Добавление...';
        buttons[1].disabled = true;
        dialog.dialog('option', 'buttons', buttons);
    }
    
    $.ajax({
        url: '<?php echo URL::site("floorplan/addPointAjax"); ?>',
        type: 'POST',
        data: {
            floorplan_id: floorplanId,
            x: clickX,
            y: clickY,
            device_id: deviceId,
            point_type: pointType,
            label: label || ''
        },
        dataType: 'json',
        success: function(response) {
            console.log('Ответ сервера:', response);
            
            if (response.success) {
                dialog.dialog('close');
                showNotification('Точка успешно добавлена!', 'success');
                
                var countText = $('#pointCountLabel').text();
                var count = parseInt(countText.replace('Точек: ', ''));
                if (!isNaN(count)) {
                    $('#pointCountLabel').text('Точек: ' + (count + 1));
                }
                
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                showNotification('Ошибка при добавлении точки: ' + (response.error || 'Неизвестная ошибка'), 'error');
                if (buttons && buttons[1]) {
                    buttons[1].text = 'Добавить точку';
                    buttons[1].disabled = false;
                    dialog.dialog('option', 'buttons', buttons);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr, status, error);
            showNotification('Ошибка при отправке запроса: ' + error, 'error');
            
            if (buttons && buttons[1]) {
                buttons[1].text = 'Добавить точку';
                buttons[1].disabled = false;
                dialog.dialog('option', 'buttons', buttons);
            }
        }
    });
}

function showNotification(message, type) {
    var $notification = $('#notification');
    if (!$notification.length) {
        $notification = $('<div id="notification" style="position: fixed; top: 20px; right: 20px; padding: 15px 25px; border-radius: 4px; z-index: 10000; display: none; max-width: 400px;"></div>');
        $('body').append($notification);
    }
    
    var bgColor = '#5cb85c';
    if (type === 'warning') bgColor = '#f0ad4e';
    if (type === 'error') bgColor = '#d9534f';
    if (type === 'info') bgColor = '#5bc0de';
    
    $notification.css('background', bgColor)
        .css('color', '#fff')
        .html(message)
        .fadeIn(300);
    
    clearTimeout($notification.data('timer'));
    var timer = setTimeout(function() {
        $notification.fadeOut(300);
    }, 3000);
    $notification.data('timer', timer);
}

// ==========================================
// УДАЛЕНИЕ ТОЧЕК - ЕДИНАЯ ФУНКЦИЯ
// ==========================================

function deletePoint(pointId, btn) {
    console.log('=== УДАЛЕНИЕ ТОЧКИ ===');
    console.log('ID:', pointId);
    
    if (!pointId) {
        showNotification('Ошибка: ID точки не найден', 'error');
        return;
    }
    
    if (!confirm('Удалить точку ' + pointId + '?')) return;
    
    var $btn = $(btn);
    var originalHtml = $btn.html();
    $btn.html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span>')
        .prop('disabled', true);
    
    var $row = $('tr[data-point-id="' + pointId + '"]');
    var $point = $('.floorplan-point[data-point-id="' + pointId + '"]');
    
    $.ajax({
        url: '<?php echo URL::site("floorplan/deletePointAjax"); ?>',
        type: 'POST',
        data: { point_id: pointId },
        dataType: 'json',
        success: function(response) {
            console.log('Ответ сервера:', response);
            if (response.success) {
                // Удаляем строку из таблицы
                if ($row.length) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        updatePointCounter();
                    });
                } else {
                    updatePointCounter();
                }
                
                // Удаляем точку с плана
                if ($point.length) {
                    $point.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
                
                showNotification('Точка ' + pointId + ' удалена', 'success');
            } else {
                showNotification('Ошибка: ' + (response.error || 'Неизвестная ошибка'), 'error');
                $btn.html(originalHtml).prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка AJAX:', xhr, status, error);
            showNotification('Ошибка: ' + error, 'error');
            $btn.html(originalHtml).prop('disabled', false);
        }
    });
}

function updatePointCounter() {
    var countText = $('#pointCountLabel').text();
    var count = parseInt(countText.replace('Точек: ', ''));
    if (!isNaN(count) && count > 0) {
        $('#pointCountLabel').text('Точек: ' + (count - 1));
    }
    
    var visibleRows = $('#pointsTable tbody tr:visible');
    if (visibleRows.length === 0) {
        $('#pointsTable tbody').html(
            '<tr><td colspan="8" class="text-center text-muted">Нет точек на плане</td></tr>'
        );
    }
}

// ==========================================
// НАСТРОЙКА ВСЕХ КНОПОК УДАЛЕНИЯ
// ==========================================

$(document).ready(function() {
    // Перенастраиваем все кнопки на плане
    $('.delete-point').each(function() {
        var pointId = $(this).data('point-id');
        if (pointId) {
            $(this).attr('onclick', 'deletePoint(' + pointId + ', this)');
        }
    });
    
    // Перенастраиваем все кнопки в таблице
    $('#pointsTable .btn-danger').each(function() {
        var $row = $(this).closest('tr');
        var pointId = $row.data('point-id');
        if (pointId) {
            $(this).attr('onclick', 'deletePoint(' + pointId + ', this)');
        }
    });
    
    console.log('✅ Все кнопки удаления настроены!');
    
    // На случай, если кнопки добавлены динамически
    $(document).on('click', '.delete-point', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pointId = $(this).data('point-id');
        if (pointId) {
            deletePoint(pointId, this);
        }
    });
});

// ==========================================
// ФУНКЦИЯ ПЕЧАТИ
// ==========================================

function printFloorplan() {
    // Показываем скрытые элементы для печати
    $('.print-header, .print-legend, .print-footer').show();
    
    // Небольшая задержка для применения стилей
    setTimeout(function() {
        window.print();
        
        // Скрываем обратно после печати
        setTimeout(function() {
            $('.print-header, .print-legend, .print-footer').hide();
        }, 500);
    }, 300);
}
</script>