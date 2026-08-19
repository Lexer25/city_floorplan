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
    $('#addControllerForm').on('submit', function(e) {
        var xVal = $('input[name="x"]', this).val().trim();
        var yVal = $('input[name="y"]', this).val().trim();
        
        var x = parseFloat(xVal);
        var y = parseFloat(yVal);
        
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
}

// ==========================================
// ПЕРЕТАСКИВАНИЕ ТОЧЕК
// ==========================================

(function() {
    var $container = $('#floorplanCanvas');
    var isDragging = false;
    var dragTarget = null;
    var startX = 0;
    var startY = 0;
    var startLeftPx = 0;
    var startTopPx = 0;
    var startLeftPct = 0;
    var startTopPct = 0;
    var parentWidth = 0;
    var parentHeight = 0;

    $(document).on('mousedown', '.floorplan-point.draggable .point-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $point = $(this).closest('.floorplan-point');
        if (!$point.hasClass('draggable')) return;
        
        parentWidth = $container.width();
        parentHeight = $container.height();
        
        var leftStr = $point.css('left');
        var topStr = $point.css('top');
        
        var isPercent = leftStr.indexOf('%') !== -1;
        var leftVal, topVal;
        
        if (isPercent) {
            leftVal = parseFloat(leftStr.replace('%', '').replace(',', '.'));
            topVal = parseFloat(topStr.replace('%', '').replace(',', '.'));
        } else {
            leftVal = parseFloat(leftStr.replace('px', ''));
            topVal = parseFloat(topStr.replace('px', ''));
        }
        
        if (isPercent) {
            if (isNaN(leftVal) || leftVal < 0 || leftVal > 100) {
                leftVal = 50;
                topVal = 50;
            }
            startLeftPct = leftVal;
            startTopPct = topVal;
            startLeftPx = (startLeftPct / 100) * parentWidth;
            startTopPx = (startTopPct / 100) * parentHeight;
        } else {
            startLeftPx = leftVal;
            startTopPx = topVal;
            startLeftPct = (startLeftPx / parentWidth) * 100;
            startTopPct = (startTopPx / parentHeight) * 100;
        }
        
        startX = e.pageX;
        startY = e.pageY;
        
        $point.css({
            'left': startLeftPx + 'px',
            'top': startTopPx + 'px'
        });
        
        dragTarget = $point;
        isDragging = true;
        
        $point.css('z-index', 20);
        $point.find('.point-actions').show();
        $point.addClass('dragging');
        
        $('#clickCoords').text('X: ' + Math.round(startLeftPct) + '% Y: ' + Math.round(startTopPct) + '%');
        
        return false;
    });

    $(document).on('mousemove', function(e) {
        if (!isDragging || !dragTarget) return;
        
        var dx = e.pageX - startX;
        var dy = e.pageY - startY;
        
        var left = startLeftPx + dx;
        var top = startTopPx + dy;
        
        var maxLeft = parentWidth - dragTarget.outerWidth();
        var maxTop = parentHeight - dragTarget.outerHeight();
        left = Math.max(0, Math.min(maxLeft, left));
        top = Math.max(0, Math.min(maxTop, top));
        
        var xPercent = (left / parentWidth) * 100;
        var yPercent = (top / parentHeight) * 100;
        xPercent = Math.max(0, Math.min(100, xPercent));
        yPercent = Math.max(0, Math.min(100, yPercent));
        
        $('#clickCoords').text('X: ' + Math.round(xPercent) + '% Y: ' + Math.round(yPercent) + '%');
        
        dragTarget.css({
            'left': left + 'px',
            'top': top + 'px'
        });
    });

    $(document).on('mouseup', function(e) {
        if (!isDragging || !dragTarget) {
            isDragging = false;
            return;
        }
        
        var $point = dragTarget;
        var pointId = $point.data('point-id');
        
        var left = parseFloat($point.css('left'));
        var top = parseFloat($point.css('top'));
        
        var xPercent = (left / parentWidth) * 100;
        var yPercent = (top / parentHeight) * 100;
        
        xPercent = Math.max(0, Math.min(100, xPercent));
        yPercent = Math.max(0, Math.min(100, yPercent));
        
        $point.css({
            'left': xPercent + '%',
            'top': yPercent + '%'
        });
        
        $point.removeClass('dragging');
        $('#clickCoords').text('');
        
        isDragging = false;
        dragTarget = null;
        
        savePointPosition(pointId, xPercent, yPercent);
    });

    $(document).on('mouseenter', '.floorplan-point.draggable', function() {
        if (!isDragging) {
            $(this).find('.point-actions').show();
            showPointInfo($(this));
        }
    });
    
    $(document).on('mouseleave', '.floorplan-point.draggable', function() {
        if (!isDragging) {
            $(this).find('.point-actions').hide();
            hidePointInfo();
        }
    });
})();

// ==========================================
// DRAG & DROP: ПЕРЕТАСКИВАНИЕ УСТРОЙСТВ НА ПЛАН
// ==========================================

$(document).ready(function() {
    var draggedDevice = null;
    var draggedDeviceId = null;
    var draggedDeviceType = null;
    var draggedDeviceName = null;
    
    $('.draggable-device').on('mousedown', function(e) {
        draggedDevice = $(this);
        draggedDeviceId = $(this).data('device-id');
        draggedDeviceType = $(this).data('device-type');
        draggedDeviceName = $(this).data('device-name');
        
        $(this).addClass('dragging-device');
        
        var clone = $(this).clone();
        clone.css({
            'position': 'fixed',
            'z-index': 10000,
            'width': $(this).outerWidth(),
            'opacity': 0.8,
            'pointer-events': 'none',
            'background': '#fff',
            'box-shadow': '0 4px 15px rgba(0,0,0,0.3)',
            'border-radius': '4px',
            'padding': '5px 10px'
        });
        clone.addClass('drag-clone');
        $('body').append(clone);
        
        $(document).on('mousemove.drag', function(e) {
            clone.css({
                'left': (e.pageX - 20) + 'px',
                'top': (e.pageY - 20) + 'px'
            });
            
            var $canvas = $('#floorplanCanvas');
            var canvasOffset = $canvas.offset();
            
            if (canvasOffset) {
                var isOverCanvas = e.pageX >= canvasOffset.left && 
                                   e.pageX <= canvasOffset.left + $canvas.width() &&
                                   e.pageY >= canvasOffset.top && 
                                   e.pageY <= canvasOffset.top + $canvas.height();
                
                if (isOverCanvas) {
                    clone.css('border', '2px solid #5cb85c');
                    $('#floorplanCanvas').css('border', '2px dashed #5cb85c');
                } else {
                    clone.css('border', 'none');
                    $('#floorplanCanvas').css('border', 'none');
                }
            }
        });
        
        $(document).on('mouseup.drag', function(e) {
            clone.remove();
            $(document).off('.drag');
            $('#floorplanCanvas').css('border', 'none');
            
            var $canvas = $('#floorplanCanvas');
            var canvasOffset = $canvas.offset();
            
            if (canvasOffset) {
                var isOverCanvas = e.pageX >= canvasOffset.left && 
                                   e.pageX <= canvasOffset.left + $canvas.width() &&
                                   e.pageY >= canvasOffset.top && 
                                   e.pageY <= canvasOffset.top + $canvas.height();
                
                if (isOverCanvas) {
                    var x = ((e.pageX - canvasOffset.left) / $canvas.width()) * 100;
                    var y = ((e.pageY - canvasOffset.top) / $canvas.height()) * 100;
                    
                    x = Math.max(0, Math.min(100, x));
                    y = Math.max(0, Math.min(100, y));
                    
                    clickX = x;
                    clickY = y;
                    $('#clickX').val(Math.round(x) + '%');
                    $('#clickY').val(Math.round(y) + '%');
                    $('#clickDeviceId').val(draggedDeviceId);
                    $('#clickLabel').val(draggedDeviceName || '');
                    $('#clickPointType').val(draggedDeviceType === 'reader' ? 'reader' : 'controller');
                    
                    if (panelVisible) {
                        toggleDevicePanel();
                    }
                    
                    $('#clickAddPointDialog').dialog('open');
                }
            }
            
            $('.dragging-device').removeClass('dragging-device');
            draggedDevice = null;
        });
        
        return false;
    });
    
    $(document).on('mouseenter', '.draggable-device', function() {
        $(this).css('cursor', 'grab');
    });
});

// ==========================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ==========================================

function showPointInfo($point) {
    var $tooltip = $('#pointInfo');
    if (!$tooltip.length) {
        $tooltip = $('<div id="pointInfo" style="position: fixed; background: rgba(0,0,0,0.8); color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 9999; pointer-events: none; display: none;"></div>');
        $('body').append($tooltip);
    }
    
    var pointId = $point.data('point-id');
    var deviceId = $point.data('device-id');
    var label = $point.find('.point-label').text() || 'Без метки';
    
    // Получаем координаты из атрибута style
    var style = $point.attr('style');
    var leftMatch = style.match(/left:\s*([\d.,]+)%/);
    var topMatch = style.match(/top:\s*([\d.,]+)%/);
    
    var xPos = 0;
    var yPos = 0;
    
    if (leftMatch) {
        // Заменяем запятую на точку и парсим
        xPos = parseFloat(leftMatch[1].replace(',', '.'));
    }
    if (topMatch) {
        yPos = parseFloat(topMatch[1].replace(',', '.'));
    }
    
    // Если не найдено в style, пробуем через css()
    if (xPos === 0 && yPos === 0) {
        var leftVal = $point.css('left');
        var topVal = $point.css('top');
        xPos = parseFloat(leftVal.replace('%', '').replace(',', '.'));
        yPos = parseFloat(topVal.replace('%', '').replace(',', '.'));
    }
    
    // Если координаты все еще невалидны или больше 100%, пытаемся получить из data-атрибутов
    if (isNaN(xPos) || xPos === 0 || xPos > 100) {
        var dataX = $point.data('x-pos');
        if (dataX !== undefined) {
            xPos = parseFloat(dataX.toString().replace(',', '.'));
        }
    }
    if (isNaN(yPos) || yPos === 0 || yPos > 100) {
        var dataY = $point.data('y-pos');
        if (dataY !== undefined) {
            yPos = parseFloat(dataY.toString().replace(',', '.'));
        }
    }
    
    // Если все еще > 100%, значит это px, конвертируем в %
    if (xPos > 100) {
        var parentWidth = $('#floorplanCanvas').width();
        if (parentWidth > 0) {
            xPos = (xPos / parentWidth) * 100;
        }
    }
    if (yPos > 100) {
        var parentHeight = $('#floorplanCanvas').height();
        if (parentHeight > 0) {
            yPos = (yPos / parentHeight) * 100;
        }
    }
    
    // Ограничиваем значения 0-100% и округляем до 1 знака
    xPos = Math.max(0, Math.min(100, Math.round(xPos * 10) / 10));
    yPos = Math.max(0, Math.min(100, Math.round(yPos * 10) / 10));
    
    $tooltip.html(
        'ID: ' + pointId + 
        ' | Устр: ' + (deviceId || '—') + 
        ' | X: ' + xPos + '%' + 
        ' | Y: ' + yPos + '%' +
        ' | ' + label
    );
    
    var offset = $point.offset();
    var tooltipLeft = offset.left + 30;
    var tooltipTop = offset.top - 10;
    
    // Не даем подсказке выйти за пределы окна
    var tooltipWidth = $tooltip.outerWidth();
    var tooltipHeight = $tooltip.outerHeight();
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    
    if (tooltipLeft + tooltipWidth > windowWidth) {
        tooltipLeft = offset.left - tooltipWidth - 10;
    }
    if (tooltipTop + tooltipHeight > windowHeight) {
        tooltipTop = windowHeight - tooltipHeight - 10;
    }
    if (tooltipTop < 0) {
        tooltipTop = 10;
    }
    
    $tooltip.css({
        left: tooltipLeft + 'px',
        top: tooltipTop + 'px',
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
	//var draggedData = window.draggedDeviceData || null;
    var pointType = window.draggedDeviceData || null;
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
    setTimeout(function() {
        $notification.fadeOut(300);
    }, 3000);
}

// ==========================================
// УДАЛЕНИЕ ТОЧЕК - ЕДИНАЯ ФУНКЦИЯ
// ==========================================

function deletePoint(pointId, btn) {
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
            if (response.success) {
                if ($row.length) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        updatePointCounter();
                    });
                } else {
                    updatePointCounter();
                }
                
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
        error: function() {
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
    $('.delete-point').each(function() {
        var pointId = $(this).data('point-id');
        if (pointId) {
            $(this).attr('onclick', 'deletePoint(' + pointId + ', this)');
        }
    });
    
    $('#pointsTable .btn-danger').each(function() {
        var $row = $(this).closest('tr');
        var pointId = $row.data('point-id');
        if (pointId) {
            $(this).attr('onclick', 'deletePoint(' + pointId + ', this)');
        }
    });
    
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
    $('.print-header, .print-legend, .print-footer').show();
    
    setTimeout(function() {
        window.print();
        
        setTimeout(function() {
            $('.print-header, .print-legend, .print-footer').hide();
        }, 500);
    }, 300);
}

// ==========================================
// СТИЛИ ДЛЯ DRAG & DROP
// ==========================================

$('<style>')
    .text(
        '.dragging-device {\n' +
        '    opacity: 0.5 !important;\n' +
        '    transform: scale(0.95);\n' +
        '}\n' +
        '.drag-clone {\n' +
        '    font-size: 12px;\n' +
        '    font-family: inherit;\n' +
        '}\n' +
        '.drag-clone .glyphicon {\n' +
        '    margin-right: 5px;\n' +
        '}\n' +
        '#floorplanCanvas.drag-over {\n' +
        '    border: 2px dashed #5cb85c !important;\n' +
        '    background: rgba(92, 184, 92, 0.05) !important;\n' +
        '}\n' +
        '.draggable-device {\n' +
        '    transition: transform 0.2s ease, opacity 0.2s ease;\n' +
        '}\n' +
        '.draggable-device:active {\n' +
        '    cursor: grabbing !important;\n' +
        '}'
    )
    .appendTo('head');
	
	
	
// ==========================================
// ПЕРЕТАСКИВАНИЕ ПАНЕЛИ УСТРОЙСТВ С СОХРАНЕНИЕМ ПОЗИЦИИ
// ==========================================

$(document).ready(function() {
    var $panelWrapper = $('#devicePanelWrapper');
    var $panel = $('#devicePanel');
    var $dragHandle = $('#panelDragHandle');
    var isDragging = false;
    var dragOffsetX = 0;
    var dragOffsetY = 0;
    
    // Восстанавливаем сохраненную позицию
    restorePanelPosition();
    
    // Функция восстановления позиции
    function restorePanelPosition() {
        try {
            var savedPosition = localStorage.getItem('floorplan_device_panel_position');
            if (savedPosition) {
                var position = JSON.parse(savedPosition);
                // Проверяем, что позиция в пределах окна
                var maxTop = $(window).height() - 100;
                var maxLeft = $(window).width() - 100;
                
                position.top = Math.max(20, Math.min(maxTop, position.top));
                position.left = Math.max(20, Math.min(maxLeft, position.left));
                
                $panelWrapper.css({
                    'top': position.top + 'px',
                    'left': position.left + 'px',
                    'transform': 'none',
                    'right': 'auto',
                    'bottom': 'auto'
                });
            } else {
                // Позиция по умолчанию - справа сверху
                $panelWrapper.css({
                    'top': '80px',
                    'left': 'auto',
                    'right': '20px',
                    'transform': 'none'
                });
            }
        } catch(e) {
            // Игнорируем ошибки
        }
    }
    
    // Функция сохранения позиции
    function savePanelPosition() {
        var offset = $panelWrapper.offset();
        var position = {
            top: offset.top,
            left: offset.left
        };
        try {
            localStorage.setItem('floorplan_device_panel_position', JSON.stringify(position));
        } catch(e) {
            // Игнорируем ошибки localStorage
        }
    }
    
    // Начинаем перетаскивание
    $dragHandle.on('mousedown', function(e) {
        if (e.button !== 0) return; // Только левая кнопка
        
        var offset = $panelWrapper.offset();
        dragOffsetX = e.pageX - offset.left;
        dragOffsetY = e.pageY - offset.top;
        isDragging = true;
        
        $panelWrapper.addClass('dragging');
        $panel.css({
            'transition': 'none',
            'box-shadow': '0 8px 40px rgba(0,0,0,0.3)'
        });
        
        e.preventDefault();
        return false;
    });
    
    // Перемещение
    $(document).on('mousemove', function(e) {
        if (!isDragging) return;
        
        var newLeft = e.pageX - dragOffsetX;
        var newTop = e.pageY - dragOffsetY;
        
        // Ограничиваем перемещение в пределах окна
        var maxLeft = $(window).width() - $panelWrapper.outerWidth();
        var maxTop = $(window).height() - 50;
        
        newLeft = Math.max(0, Math.min(maxLeft, newLeft));
        newTop = Math.max(0, Math.min(maxTop, newTop));
        
        $panelWrapper.css({
            'left': newLeft + 'px',
            'top': newTop + 'px',
            'right': 'auto',
            'bottom': 'auto',
            'transform': 'none'
        });
        
        e.preventDefault();
    });
    
    // Завершение перетаскивания
    $(document).on('mouseup', function(e) {
        if (isDragging) {
            isDragging = false;
            $panelWrapper.removeClass('dragging');
            $panel.css({
                'transition': 'transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)',
                'box-shadow': '0 4px 25px rgba(0,0,0,0.15)'
            });
            savePanelPosition();
        }
    });
    
    // При изменении размера окна корректируем позицию
    $(window).on('resize', function() {
        var offset = $panelWrapper.offset();
        var maxLeft = $(window).width() - $panelWrapper.outerWidth();
        var maxTop = $(window).height() - 50;
        
        if (offset.left > maxLeft) {
            $panelWrapper.css('left', Math.max(0, maxLeft) + 'px');
            savePanelPosition();
        }
        if (offset.top > maxTop) {
            $panelWrapper.css('top', Math.max(0, maxTop) + 'px');
            savePanelPosition();
        }
    });
    
    // Кнопка сворачивания/разворачивания
    window.toggleDevicePanel = function() {
        var $panel = $('#devicePanel');
        var $anchorIcon = $('#anchorIcon');
        var isVisible = $panel.css('opacity') !== '0' && $panel.css('transform') !== 'translateX(100%)';
        
        if (isVisible) {
            $panel.css({
                'transform': 'translateX(100%)',
                'opacity': '0'
            });
            $anchorIcon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
            $('#panelAnchor').css('border-radius', '0 4px 4px 0');
        } else {
            $panel.css({
                'transform': 'translateX(0)',
                'opacity': '1'
            });
            $anchorIcon.removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
            $('#panelAnchor').css('border-radius', '4px 0 0 4px');
        }
    };
});
// ==========================================
// ФИЛЬТРАЦИЯ УСТРОЙСТВ НА ПАНЕЛИ
// ==========================================

$(document).ready(function() {
    var $filterInput = $('#deviceFilterInput');
    var $clearBtn = $('#clearDeviceFilter');
    var $filterTypeBtns = $('.filter-type-btn');
    var currentTypeFilter = 'all';
    var searchQuery = '';
    
    // Функция фильтрации
    function filterDevices() {
        var query = searchQuery.toLowerCase().trim();
        var type = currentTypeFilter;
        var $readers = $('#readersList .device-item');
        var $controllers = $('#controllersList .device-item');
        var readersVisible = 0;
        var controllersVisible = 0;
        
        // Фильтруем считыватели
        $readers.each(function() {
            var $item = $(this);
            var searchData = $item.data('device-search') || '';
            var matchesQuery = query === '' || searchData.indexOf(query) !== -1;
            var matchesType = type === 'all' || type === 'reader';
            
            if (matchesQuery && matchesType) {
                $item.show();
                readersVisible++;
            } else {
                $item.hide();
            }
        });
        
        // Фильтруем контроллеры
        $controllers.each(function() {
            var $item = $(this);
            var searchData = $item.data('device-search') || '';
            var matchesQuery = query === '' || searchData.indexOf(query) !== -1;
            var matchesType = type === 'all' || type === 'controller';
            
            if (matchesQuery && matchesType) {
                $item.show();
                controllersVisible++;
            } else {
                $item.hide();
            }
        });
        
        // Показываем/скрываем сообщения "ничего не найдено"
        var $readersEmpty = $('#readersEmptyFilter');
        var $controllersEmpty = $('#controllersEmptyFilter');
        var $noReadersMsg = $('#noReadersMsg');
        var $noControllersMsg = $('#noControllersMsg');
        
        // Проверяем наличие элементов в списках
        var hasReaders = $readers.length > 0;
        var hasControllers = $controllers.length > 0;
        
        if (hasReaders) {
            $noReadersMsg.hide();
            if (readersVisible === 0) {
                $readersEmpty.show();
            } else {
                $readersEmpty.hide();
            }
        } else {
            $noReadersMsg.show();
            $readersEmpty.hide();
        }
        
        if (hasControllers) {
            $noControllersMsg.hide();
            if (controllersVisible === 0) {
                $controllersEmpty.show();
            } else {
                $controllersEmpty.hide();
            }
        } else {
            $noControllersMsg.show();
            $controllersEmpty.hide();
        }
        
        // Обновляем счетчики в заголовке
        var totalVisible = readersVisible + controllersVisible;
        $('#totalDevicesCount').text(totalVisible);
        $('#filterAllCount').text(totalVisible);
        $('#filterReaderCount').text(readersVisible);
        $('#filterControllerCount').text(controllersVisible);
        $('#readerTabCount').text(readersVisible);
        $('#controllerTabCount').text(controllersVisible);
        
        // Показываем/скрываем кнопку сброса
        if (query !== '' || type !== 'all') {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }
    }
    
    // Обработчик ввода текста (с debounce)
    var filterTimeout;
    $filterInput.on('input', function() {
        clearTimeout(filterTimeout);
        searchQuery = $(this).val();
        filterTimeout = setTimeout(filterDevices, 200);
    });
    
    // Обработчик кнопок быстрого фильтра по типу
    $filterTypeBtns.on('click', function() {
        var type = $(this).data('filter');
        
        // Обновляем активную кнопку
        $filterTypeBtns.removeClass('active').css({
            'background': '#fff',
            'color': '#333'
        });
        $(this).addClass('active').css({
            'background': '#337ab7',
            'color': '#fff'
        });
        
        currentTypeFilter = type;
        filterDevices();
    });
    
    // Обработчик кнопки сброса фильтра
    $clearBtn.on('click', function() {
        $filterInput.val('');
        searchQuery = '';
        currentTypeFilter = 'all';
        
        // Сбрасываем активную кнопку
        $filterTypeBtns.removeClass('active').css({
            'background': '#fff',
            'color': '#333'
        });
        $filterTypeBtns.filter('[data-filter="all"]').addClass('active').css({
            'background': '#337ab7',
            'color': '#fff'
        });
        
        filterDevices();
        $filterInput.focus();
    });
    
    // Обработчик клавиши Escape для сброса фильтра
    $filterInput.on('keydown', function(e) {
        if (e.key === 'Escape') {
            $clearBtn.click();
        }
    });
    
    // Сохраняем состояние фильтра при переключении вкладок
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        // Обновляем фильтр при переключении вкладки
        filterDevices();
    });
    
    // Инициализация фильтрации при загрузке страницы
    setTimeout(filterDevices, 300);
    
    // Обновляем фильтр при изменении размера панели
    var resizeTimeout;
    $(window).on('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(filterDevices, 200);
    });
});

// ==========================================
// ДОПОЛНИТЕЛЬНЫЕ СТИЛИ ДЛЯ ФИЛЬТРА
// ==========================================

$('<style>')
    .text(
        '.filter-type-btn {\n' +
        '    transition: all 0.2s ease;\n' +
        '}\n' +
        '.filter-type-btn:hover {\n' +
        '    opacity: 0.8;\n' +
        '}\n' +
        '.filter-type-btn.active {\n' +
        '    border-color: #337ab7;\n' +
        '}\n' +
        '.filter-type-btn.active:hover {\n' +
        '    opacity: 0.9;\n' +
        '}\n' +
        '#deviceFilterInput:focus {\n' +
        '    border-color: #337ab7 !important;\n' +
        '    box-shadow: 0 0 5px rgba(51, 122, 183, 0.3);\n' +
        '}\n' +
        '#clearDeviceFilter {\n' +
        '    transition: all 0.2s ease;\n' +
        '}\n' +
        '#clearDeviceFilter:hover {\n' +
        '    opacity: 0.8;\n' +
        '    transform: scale(1.05);\n' +
        '}\n' +
        '.device-item.hidden-by-filter {\n' +
        '    display: none !important;\n' +
        '}\n' +
        '#readersEmptyFilter, #controllersEmptyFilter {\n' +
        '    padding: 20px 10px;\n' +
        '    text-align: center;\n' +
        '    color: #999;\n' +
        '    font-size: 12px;\n' +
        '}\n' +
        '#readersEmptyFilter .glyphicon, #controllersEmptyFilter .glyphicon {\n' +
        '    font-size: 20px;\n' +
        '    display: block;\n' +
        '    margin-bottom: 5px;\n' +
        '    color: #ccc;\n' +
        '}'
    )
    .appendTo('head');
</script>