<!-- ========================================== -->
<!-- ПРОВЕРКА БАЗЫ ДАННЫХ                       -->
<!-- ========================================== -->
<?php
if ($is_admin) {
    $installModel = Model::factory('Floorplan_Installm');
    $checkResult = $installModel->checkDatabase();
    
    if (!$checkResult['all_ok']):
?>
    <div class="alert alert-warning alert-dismissible fade in">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-warning-sign"></span> 
            Внимание!
        </strong>
        <p>База данных модуля "Планы объекта" не полностью установлена.</p>
        <p>
            <a href="<?php echo URL::site('floorplan/install'); ?>" class="btn btn-warning btn-sm">
                <span class="glyphicon glyphicon-database"></span> Перейти к установке
            </a>
        </p>
    </div>
<?php
    endif;
}
?>

<!-- ========================================== -->
<!-- ИНФОРМАЦИЯ О ПОДСВЕЧЕННОЙ ТОЧКЕ            -->
<!-- ========================================== -->
<?php if (isset($highlightData) && $highlightData): ?>
    <div class="alert alert-success alert-dismissible fade in" style="margin-bottom: 15px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-search"></span> 
            🎯 Искомое устройство (id_dev=<?php echo $searchIdDev; ?>):
        </strong>
        <ul style="margin: 5px 0 0 20px;">
            <li><strong>ID точки:</strong> <?php echo $highlightData['id_point']; ?></li>
            <li><strong>Тип:</strong> <?php echo $highlightData['point_type']; ?></li>
            <li><strong>Устройство:</strong> <?php echo htmlspecialchars($highlightData['device_name'] ?: 'Не привязано'); ?></li>
            <li><strong>ID устройства:</strong> <?php echo $highlightData['id_dev']; ?></li>
            <li><strong>ID контроллера:</strong> <?php echo isset($id_ctrl) ? htmlspecialchars($id_ctrl) : '—'; ?></li>
            <li><strong>Метка:</strong> <?php echo htmlspecialchars($highlightData['label'] ?: '—'); ?></li>
            <li><strong>Позиция:</strong> X: <?php echo round($highlightData['x_pos'], 1); ?>%, Y: <?php echo round($highlightData['y_pos'], 1); ?>%</li>
        </ul>
    </div>
<?php else: ?>
    <?php if (isset($searchIdDev) && $searchIdDev): ?>
        <div class="alert alert-warning alert-dismissible fade in" style="margin-bottom: 15px;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>
                <span class="glyphicon glyphicon-warning-sign"></span> 
                Точка не найдена:
            </strong>
            <p style="margin: 5px 0 0 0;">
                Устройство с ID=<strong><?php echo $searchIdDev; ?></strong> не найдено на этом плане.
            </p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Информация о связанных устройствах -->
<?php if (!empty($relatedData)): ?>
    <div class="alert alert-info alert-dismissible fade in" style="margin-bottom: 15px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-link"></span> 
            Связанные устройства (id_ctrl=<?php echo htmlspecialchars($id_ctrl); ?>):
        </strong>
        <ul style="margin: 5px 0 0 20px;">
            <?php foreach ($relatedData as $related): ?>
                <li>
                    <strong>ID точки:</strong> <?php echo $related['id_point']; ?> |
                    <strong>Тип:</strong> <?php echo $related['point_type']; ?> |
                    <strong>Устройство:</strong> <?php echo htmlspecialchars($related['device_name'] ?: 'Не привязано'); ?> |
                    <strong>ID устройства:</strong> <?php echo $related['id_dev']; ?>
                    <?php if ($related['label']): ?>
                        | <strong>Метка:</strong> <?php echo htmlspecialchars($related['label']); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-eye-open"></span>
            Просмотр плана: <?php echo htmlspecialchars($floorplan['name']); ?>
            <?php if (isset($highlightData) && $highlightData): ?>
                <span class="label label-success" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-flag"></span> 
                    Точка найдена (id_dev=<?php echo $searchIdDev; ?>)
                </span>
            <?php endif; ?>
            <?php if (!empty($relatedData)): ?>
                <span class="label label-info" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-link"></span> 
                    Связанных: <?php echo count($relatedData); ?>
                </span>
            <?php endif; ?>
        </h3>
    </div>
    <div class="panel-body">

        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-12">
                <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> Назад
                </a>
                <?php if ($is_admin): ?>
                    <a href="<?php echo URL::site('floorplan/edit/' . $floorplan['id_floorplan']); ?>" class="btn btn-primary">
                        <span class="glyphicon glyphicon-edit"></span> Редактировать
                    </a>
                <?php endif; ?>
                <span class="pull-right text-muted">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    <?php echo count($points); ?> точек на плане
                    <?php if (!empty($relatedData)): ?>
                        &bull; <span class="text-info">Связанных: <?php echo count($relatedData); ?></span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ПАНЕЛЬ УПРАВЛЕНИЯ МАСШТАБОМ                -->
        <!-- ========================================== -->
        <div class="floorplan-toolbar">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm" onclick="zoomIn()" title="Увеличить (Ctrl++)">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="zoomOut()" title="Уменьшить (Ctrl+-)">
                    <span class="glyphicon glyphicon-minus"></span>
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="resetZoom()" title="100% (Ctrl+0)">
                    <span class="glyphicon glyphicon-resize-full"></span> 100%
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="fitToScreen()" title="Подогнать под размер экрана">
                    <span class="glyphicon glyphicon-zoom-in"></span> По размеру
                </button>
            </div>
            <span class="zoom-level">Масштаб: <strong id="zoomLevelDisplay">100</strong>%</span>
            <span class="text-muted" style="font-size: 11px; margin-left: 15px;">
                <span class="glyphicon glyphicon-info-sign"></span> 
                Ctrl+Колесо для масштабирования
            </span>
        </div>

        <!-- ========================================== -->
        <!-- КОНТЕЙНЕР ДЛЯ ПЛАНА                        -->
        <!-- ========================================== -->
        <div class="floorplan-scrollable" id="floorplanScrollable">
            <div id="floorplanCanvas" style="position: relative; width: <?php echo $floorplan['width']; ?>px; height: <?php echo $floorplan['height']; ?>px; margin: 0 auto; transform: scale(1); transform-origin: top left;">
                <img src="<?php echo URL::base() . $floorplan['image']; ?>" 
                     id="floorplanImage"
                     style="width: 100%; height: 100%; display: block;"
                     alt="<?php echo htmlspecialchars($floorplan['name']); ?>">

                <?php 
                $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
                $relatedIds = isset($relatedIds) ? $relatedIds : array();
                
                foreach ($points as $point): 
                    $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                    $statusClass = $status == 'online' ? 'status-online' : 'status-offline';
                    
                    $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
                    $isRelated = in_array($point['id_point'], $relatedIds);
                    
                    $classes = $statusClass;
                    if ($isHighlighted) $classes .= ' highlighted';
                    if ($isRelated) $classes .= ' related';
                    
                    $tooltip = $point['label'] ?: $point['device_name'];
                    if ($point['id_dev']) {
                        $tooltip .= ' (id_dev=' . $point['id_dev'] . ')';
                    }
                    if ($isHighlighted) {
                        $tooltip .= ' ★ ИСКОМОЕ';
                    } elseif ($isRelated) {
                        $tooltip .= ' 🔗 СВЯЗАННОЕ';
                    }
                ?>
                    <div class="floorplan-point <?php echo $classes; ?>" 
                         data-point-id="<?php echo $point['id_point']; ?>"
                         data-device-id="<?php echo $point['id_dev']; ?>"
                         style="position: absolute; left: <?php echo $point['x_pos']; ?>%; top: <?php echo $point['y_pos']; ?>%; transform: translate(-50%, -50%); <?php echo $isHighlighted ? 'z-index: 50;' : ''; ?>">
                        
                        <div class="point-icon" title="<?php echo htmlspecialchars($tooltip); ?>">
                            <?php if ($point['point_type'] == 'reader'): ?>
                                <span class="glyphicon glyphicon-qrcode text-info" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                            <?php elseif ($point['point_type'] == 'controller'): ?>
                                <span class="glyphicon glyphicon-cog text-warning" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                            <?php elseif ($point['point_type'] == 'door'): ?>
                                <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'ok-circle text-success' : 'ban-circle text-danger'; ?>" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                            <?php elseif ($point['point_type'] == 'turnstile'): ?>
                                <span class="glyphicon glyphicon-resize-horizontal text-warning" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-record text-muted" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                            <?php endif; ?>
                            
                            <?php if ($isHighlighted): ?>
                                <span class="highlight-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px; border-radius: 50%; border: 3px solid #ff9800; animation: pulse-ring 1.5s ease-in-out infinite; pointer-events: none;"></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($point['label']): ?>
                            <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 10px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : ($isRelated ? '#ff9800' : '#ddd'); ?>; <?php echo ($isHighlighted || $isRelated) ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                                <?php echo htmlspecialchars($point['label']); ?>
                                <?php if ($point['id_dev']): ?>
                                    <span style="color: #999; font-size: 8px;"> (id_dev=<?php echo $point['id_dev']; ?>)</span>
                                <?php endif; ?>
                                <?php if ($isHighlighted): ?>
                                    <span style="color: #ff9800; font-size: 10px;"> ★</span>
                                <?php elseif ($isRelated): ?>
                                    <span style="color: #ff9800; font-size: 10px;"> 🔗</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php if ($point['id_dev']): ?>
                                <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 9px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : ($isRelated ? '#ff9800' : '#ddd'); ?>; <?php echo ($isHighlighted || $isRelated) ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                                    <span style="color: #999;">id_dev=<?php echo $point['id_dev']; ?></span>
                                    <?php if ($isHighlighted): ?>
                                        <span style="color: #ff9800; font-size: 10px;"> ★</span>
                                    <?php elseif ($isRelated): ?>
                                        <span style="color: #ff9800; font-size: 10px;"> 🔗</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ИНФОРМАЦИЯ О ТОЧКАХ (ТАБЛИЦА)              -->
        <!-- ========================================== -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Точки прохода на плане</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed" id="pointsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Тип</th>
                                        <th>Устройство</th>
                                        <th>ID устройства (id_dev)</th>
                                        <th>Метка</th>
                                        <th>Позиция</th>
                                        <th>Статус</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($points)): ?>
                                        <?php 
                                        $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
                                        $relatedIds = isset($relatedIds) ? $relatedIds : array();
                                        foreach ($points as $index => $point): 
                                            $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                                            $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
                                            $isRelated = in_array($point['id_point'], $relatedIds);
                                        ?>
                                            <tr data-point-id="<?php echo $point['id_point']; ?>" <?php echo $isHighlighted ? 'class="success"' : ($isRelated ? 'class="info"' : ''); ?>>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <?php if ($point['point_type'] == 'reader'): ?>
                                                        <span class="label label-info">Считыватель</span>
                                                    <?php elseif ($point['point_type'] == 'controller'): ?>
                                                        <span class="label label-warning">Контроллер</span>
                                                    <?php elseif ($point['point_type'] == 'door'): ?>
                                                        <span class="label label-default">Дверь</span>
                                                    <?php else: ?>
                                                        <span class="label label-default"><?php echo $point['point_type']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($point['device_name'] ?: 'Не привязано'); ?>
                                                    <?php if ($isHighlighted): ?>
                                                        <span class="label label-success">★ ИСКОМОЕ</span>
                                                    <?php elseif ($isRelated): ?>
                                                        <span class="label label-warning">🔗 СВЯЗАННОЕ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($point['id_dev']): ?>
                                                        <span class="label label-default"><?php echo $point['id_dev']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($point['label']); ?>
                                                </td>
                                                <td>X: <?php echo round($point['x_pos'], 1); ?>% Y: <?php echo round($point['y_pos'], 1); ?>%</td>
                                                <td>
                                                    <span class="label label-<?php echo $status == 'online' ? 'success' : 'danger'; ?>">
                                                        <?php echo $status == 'online' ? 'Online' : 'Offline'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($isHighlighted): ?>
                                                        <span class="glyphicon glyphicon-star" style="color: #ff9800;" title="Искомое устройство"></span>
                                                    <?php elseif ($isRelated): ?>
                                                        <span class="glyphicon glyphicon-link" style="color: #ff9800;" title="Связанное устройство"></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Нет точек на плане</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- СВЯЗАННЫЕ УСТРОЙСТВА (ТАБЛИЦА)            -->
        <!-- ========================================== -->
        <?php if (!empty($allHighlightPoints) && count($allHighlightPoints) > 1): ?>
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <span class="glyphicon glyphicon-link"></span> 
                            Связанные устройства (id_ctrl = <?php echo htmlspecialchars($id_ctrl); ?>)
                            <span class="pull-right">
                                <span class="label label-primary"><?php echo count($allHighlightPoints); ?> устройств</span>
                                <?php if ($highlightData): ?>
                                    <span class="label label-success">★ искомое</span>
                                <?php endif; ?>
                            </span>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID точки</th>
                                        <th>Тип</th>
                                        <th>Устройство</th>
                                        <th>ID устройства</th>
                                        <th>Позиция</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = 0;
                                    foreach ($allHighlightPoints as $point):
                                        $counter++;
                                        $isHighlighted = ($highlightData && $point['id_point'] == $highlightData['id_point']);
                                        $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                                    ?>
                                        <tr <?php echo $isHighlighted ? 'class="success"' : ''; ?>>
                                            <td><?php echo $counter; ?></td>
                                            <td><?php echo $point['id_point']; ?></td>
                                            <td>
                                                <?php if ($point['point_type'] == 'reader'): ?>
                                                    <span class="label label-info">Считыватель</span>
                                                <?php elseif ($point['point_type'] == 'controller'): ?>
                                                    <span class="label label-warning">Контроллер</span>
                                                <?php elseif ($point['point_type'] == 'door'): ?>
                                                    <span class="label label-default">Дверь</span>
                                                <?php else: ?>
                                                    <span class="label label-default"><?php echo $point['point_type']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($point['device_name'] ?: 'Не привязано'); ?>
                                                <?php if ($isHighlighted): ?>
                                                    <span class="label label-success">★ ИСКОМОЕ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($point['id_dev']): ?>
                                                    <span class="label label-default"><?php echo $point['id_dev']; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>X: <?php echo round($point['x_pos'], 1); ?>% Y: <?php echo round($point['y_pos'], 1); ?>%</td>
                                            <td>
                                                <span class="label label-<?php echo $status == 'online' ? 'success' : 'danger'; ?>">
                                                    <?php echo $status == 'online' ? 'Online' : 'Offline'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ========================================== -->
<!-- ПОДКЛЮЧЕНИЕ МАСШТАБИРОВАНИЯ                -->
<!-- ========================================== -->
<script>
window.floorplanId = <?php echo $floorplan['id_floorplan']; ?>;
window.floorplanWidth = <?php echo $floorplan['width']; ?>;
window.floorplanHeight = <?php echo $floorplan['height']; ?>;

<?php if (isset($highlightData) && $highlightData): ?>
window.highlightPointId = <?php echo $highlightData['id_point']; ?>;
window.highlightX = <?php echo $highlightData['x_pos']; ?>;
window.highlightY = <?php echo $highlightData['y_pos']; ?>;
<?php endif; ?>

// Передаем данные для линий
window.allHighlightPoints = <?php echo json_encode($allHighlightPoints); ?>;
window.highlightData = <?php echo json_encode($highlightData); ?>;
window.relatedData = <?php echo json_encode($relatedData); ?>;
</script>

<?php include Kohana::find_file('views', 'floorplan/zoom_script'); ?>

<!-- ========================================== -->
<!-- РИСОВАНИЕ ЛИНИЙ МЕЖДУ УСТРОЙСТВАМИ        -->
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
    // РИСОВАНИЕ СВЯЗЕЙ МЕЖДУ УСТРОЙСТВАМИ
    // ==========================================
    
    <?php if (!empty($allHighlightPoints) && count($allHighlightPoints) > 1): ?>
    var $img = $('#floorplanImage');
    
    function drawConnections() {
        console.log('=== РИСУЕМ ЛИНИИ ===');
        
        var $canvas = $('#floorplanCanvas');
        var canvasWidth = $canvas.width();
        var canvasHeight = $canvas.height();
        
        if (canvasWidth === 0 || canvasHeight === 0) {
            console.log('Canvas не загружен');
            return;
        }
        
        console.log('Canvas:', canvasWidth, 'x', canvasHeight);
        
        var $svg = $('#connectionsSvg');
        if (!$svg.length) {
            $svg = $('<svg id="connectionsSvg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 5;"></svg>');
            $canvas.append($svg);
            console.log('SVG создан');
        }
        
        $svg.empty();
        
        var points = [];
        <?php foreach ($allHighlightPoints as $point): ?>
            <?php 
            $isHighlighted = ($highlightData && $point['id_point'] == $highlightData['id_point']);
            ?>
            points.push({
                id: <?php echo $point['id_point']; ?>,
                x: <?php echo $point['x_pos']; ?>,
                y: <?php echo $point['y_pos']; ?>,
                isHighlighted: <?php echo $isHighlighted ? 'true' : 'false'; ?>
            });
        <?php endforeach; ?>
        
        console.log('Точек для линий:', points.length);
        
        if (points.length < 2) return;
        
        points.sort(function(a, b) { return a.x - b.x; });
        
        var color = '#ff9800';
        var highlightColor = '#ff5722';
        
        for (var i = 0; i < points.length - 1; i++) {
            var p1 = points[i];
            var p2 = points[i + 1];
            
            var isHighlighted = p1.isHighlighted || p2.isHighlighted;
            var strokeColor = isHighlighted ? highlightColor : color;
            var strokeWidth = isHighlighted ? 3 : 2;
            
            var x1 = (p1.x / 100) * canvasWidth;
            var y1 = (p1.y / 100) * canvasHeight;
            var x2 = (p2.x / 100) * canvasWidth;
            var y2 = (p2.y / 100) * canvasHeight;
            
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
            line.setAttribute('stroke', strokeColor);
            line.setAttribute('stroke-width', strokeWidth);
            line.setAttribute('stroke-dasharray', isHighlighted ? 'none' : '5,5');
            line.setAttribute('opacity', isHighlighted ? '1' : '0.7');
            
            if (isHighlighted) {
                line.setAttribute('style', 'animation: dash 1.5s ease-in-out infinite;');
            }
            
            $svg[0].appendChild(line);
            
            // Добавляем точки на концах линий
            function addDot(svg, x, y, isHighlighted) {
                var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
                circle.setAttribute('r', isHighlighted ? 6 : 4);
                circle.setAttribute('fill', isHighlighted ? highlightColor : color);
                circle.setAttribute('opacity', isHighlighted ? '1' : '0.6');
                svg.appendChild(circle);
            }
            
            addDot($svg[0], x1, y1, p1.isHighlighted);
            addDot($svg[0], x2, y2, p2.isHighlighted);
        }
        
        console.log('✅ Линии нарисованы!');
    }
    
    // Рисуем после загрузки изображения
    if ($img[0] && $img[0].complete) {
        setTimeout(drawConnections, 300);
    } else {
        $img.on('load', function() {
            setTimeout(drawConnections, 300);
        });
    }
    
    // Повторная отрисовка через 1 секунду
    setTimeout(drawConnections, 1000);
    
    // Перерисовка при изменении размера
    $(window).on('resize', function() {
        clearTimeout(window.resizeTimer);
        window.resizeTimer = setTimeout(drawConnections, 300);
    });
    
    // Перерисовка при зуме
    var originalApplyZoom = FloorplanZoom.applyZoom;
    FloorplanZoom.applyZoom = function() {
        originalApplyZoom.call(this);
        setTimeout(drawConnections, 200);
    };
    <?php endif; ?>
});
</script>

<!-- ========================================== -->
<!-- СТИЛИ                                       -->
<!-- ========================================== -->
<style>
.floorplan-toolbar {
    background: #f8f9fa;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.floorplan-toolbar .btn-group {
    display: flex;
    gap: 2px;
}

.floorplan-toolbar .btn-group .btn {
    padding: 4px 10px;
    font-size: 12px;
}

.floorplan-toolbar .zoom-level {
    font-size: 12px;
    color: #666;
    min-width: 80px;
}

.floorplan-toolbar .zoom-level strong {
    color: #333;
}

.floorplan-toolbar .text-muted {
    font-size: 11px;
    color: #999;
}

.floorplan-scrollable {
    overflow: auto;
    position: relative;
    border: 1px solid #ddd;
    border-radius: 0 0 4px 4px;
    background: #fafafa;
    max-height: 600px;
    min-height: 400px;
}

#floorplanCanvas {
    position: relative;
    margin: 0 auto;
    transform-origin: top left;
    transition: transform 0.15s ease;
}

.floorplan-container {
    background: #fafafa;
    min-height: 400px;
}

.floorplan-point {
    z-index: 10;
    transition: all 0.2s ease;
}

.floorplan-point:hover {
    z-index: 20;
    transform: translate(-50%, -50%) scale(1.1) !important;
}

.floorplan-point .point-icon {
    text-shadow: 0 0 5px rgba(255,255,255,0.8);
}

.floorplan-point.status-online .point-icon {
    opacity: 1;
}

.floorplan-point.status-offline .point-icon {
    opacity: 0.4;
}

.floorplan-point.related .point-icon {
    animation: related-pulse 2s ease-in-out infinite;
}

.floorplan-point.related .point-icon .glyphicon {
    filter: drop-shadow(0 0 15px rgba(255, 152, 0, 0.5)) !important;
}

@keyframes related-pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        filter: drop-shadow(0 0 10px rgba(255, 152, 0, 0.3));
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        filter: drop-shadow(0 0 25px rgba(255, 152, 0, 0.7));
    }
}

.text-success {
    color: #5cb85c;
}

.text-danger {
    color: #d9534f;
}

.label-default {
    background-color: #777;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

.label-info {
    background-color: #5bc0de;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

.label-warning {
    background-color: #ff9800;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

@keyframes pulse-ring {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

.floorplan-point.highlighted {
    z-index: 50 !important;
    animation: highlight-bounce 1.5s ease-in-out infinite;
}

.floorplan-point.highlighted .point-icon {
    filter: drop-shadow(0 0 20px rgba(255, 152, 0, 0.8));
}

@keyframes highlight-bounce {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        transform: translate(-50%, -50%) scale(1.15);
    }
}

tr.success {
    background-color: #fff3e0 !important;
    border-left: 3px solid #ff9800;
}

tr.success td {
    background-color: #fff3e0 !important;
}

tr.info {
    background-color: #e3f2fd !important;
    border-left: 3px solid #2196f3;
}

tr.info td {
    background-color: #e3f2fd !important;
}

#connectionsSvg {
    pointer-events: none;
}

@keyframes dash {
    to {
        stroke-dashoffset: -20;
    }
}
</style>