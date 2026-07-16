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
    <div class="alert alert-info alert-dismissible fade in" style="margin-bottom: 15px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-search"></span> 
            Найдена точка по ID устройства <?php echo $searchIdDev; ?>:
        </strong>
        <ul style="margin: 5px 0 0 20px;">
            <li><strong>ID точки:</strong> <?php echo $highlightData['id_point']; ?></li>
            <li><strong>Тип:</strong> <?php echo $highlightData['point_type']; ?></li>
            <li><strong>Устройство:</strong> <?php echo htmlspecialchars($highlightData['device_name'] ?: 'Не привязано'); ?></li>
            <li><strong>ID устройства:</strong> <?php echo $highlightData['id_dev']; ?></li>
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

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-edit"></span>
            Редактирование плана: <?php echo htmlspecialchars($floorplan['name']); ?>
            <?php if (isset($highlightData) && $highlightData): ?>
                <span class="label label-success" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-flag"></span> 
                    Точка найдена (id_dev=<?php echo $searchIdDev; ?>)
                </span>
            <?php endif; ?>
            <span class="pull-right">
                <span class="label label-info" id="pointCountLabel">Точек: <?php echo count($points); ?></span>
            </span>
        </h3>
    </div>
    <div class="panel-body">

        <!-- Форма обновления плана -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-12">
                <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>" 
                      class="form-inline" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_plan">
                    <div class="form-group">
                        <label>Название: </label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($current_floor['name']); ?>" class="form-control" style="width: 200px;">
                    </div>
                    <div class="form-group">
                        <label>Описание: </label>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($current_floor['description']); ?>" class="form-control" style="width: 250px;">
                    </div>
                    <div class="form-group" style="margin-left: 10px;">
                        <label>Новое изображение: </label>
                        <input type="file" name="image" class="form-control" style="display: inline-block; width: auto;" accept="image/*">
                        <small class="text-muted">(оставьте пустым, чтобы сохранить текущее)</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Обновить</button>
                    <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">Назад</a>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ПЕРЕКЛЮЧАТЕЛЬ ЭТАЖЕЙ -->
        <!-- ========================================== -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-12">
                <div class="floor-selector">
                    <div class="btn-group" role="group">
                        <?php foreach ($floors as $floor): ?>
                            <a href="<?php echo URL::site('floorplan/edit/' . $main_floor_id . '?floor=' . $floor['id_floorplan']); ?>" 
                               class="btn btn-<?php echo $floor['id_floorplan'] == $current_floor_id ? 'primary' : 'default'; ?> floor-btn"
                               title="<?php echo htmlspecialchars($floor['floor_name'] ?: $floor['floor_number'] . ' этаж'); ?>">
                                <?php echo $floor['floor_number']; ?>
                                <span class="badge floor-badge"><?php echo $floor['points_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                        <?php if ($is_admin): ?>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addFloorModal">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#copyFloorModal">
                                <span class="glyphicon glyphicon-copy"></span>
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteFloorModal">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <span class="label label-info" style="margin-left: 15px;">
                        <span class="glyphicon glyphicon-<?php echo isset($building) && $building ? 'building' : 'map-marker'; ?>"></span>
                        <?php echo isset($building) && $building ? htmlspecialchars($building['name']) : 'Здание'; ?>
                        &bull; 
                        <?php echo htmlspecialchars($current_floor['floor_name'] ?: $current_floor['floor_number'] . ' этаж'); ?>
                    </span>
                    <span class="pull-right text-muted">
                        Всего этажей: <strong><?php echo count($floors); ?></strong>
                        &bull; Точек: <strong><?php echo count($points); ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ПАНЕЛЬ УПРАВЛЕНИЯ МАСШТАБОМ + РЕЖИМ КЛИКА -->
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
                <button type="button" class="btn btn-success btn-sm" id="toggleClickMode" onclick="toggleClickMode()">
                    <span class="glyphicon glyphicon-hand-up"></span> Режим клика
                </button>
            </div>
            <span class="zoom-level">Масштаб: <strong id="zoomLevelDisplay">100</strong>%</span>
            <span class="text-muted" style="font-size: 11px; margin-left: 15px;">
                <span class="glyphicon glyphicon-info-sign"></span> 
                Ctrl+Колесо для масштабирования
            </span>
            <span id="clickModeStatus" style="display: none; margin-left: 15px; color: #ff9800; font-weight: bold;">
                <span class="glyphicon glyphicon-hand-up"></span> 
                Кликните на плане для добавления точки
                <span id="selectedDeviceDisplay" style="color: #337ab7; margin-left: 10px;"></span>
                <span id="clickCoords" style="color: #ff9800; margin-left: 10px;"></span>
            </span>
        </div>

        <!-- ========================================== -->
        <!-- КОНТЕЙНЕР ДЛЯ ПЛАНА                        -->
        <!-- ========================================== -->
        <div class="floorplan-scrollable" id="floorplanScrollable">
            <div id="floorplanCanvas" style="position: relative; width: <?php echo $current_floor['width']; ?>px; height: <?php echo $current_floor['height']; ?>px; margin: 0 auto; transform: scale(1); transform-origin: top left;">
                <img src="<?php echo URL::base() . $current_floor['image']; ?>" 
                     id="floorplanImage" 
                     style="width: 100%; height: 100%; display: block;"
                     alt="<?php echo htmlspecialchars($current_floor['name']); ?>">

                <?php 
                $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
                
                foreach ($points as $point): 
                    $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                    $statusClass = $status == 'online' ? 'status-online' : 'status-offline';
                    
                    $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
                    
                    $tooltip = $point['label'] ?: $point['device_name'];
                    if ($point['id_dev']) {
                        $tooltip .= ' (id_dev=' . $point['id_dev'] . ')';
                    }
                    if ($isHighlighted) {
                        $tooltip .= ' ★ ВЫДЕЛЕНА';
                    }
                ?>
                    <div class="floorplan-point <?php echo $statusClass; ?> draggable <?php echo $isHighlighted ? 'highlighted' : ''; ?>" 
                         data-point-id="<?php echo $point['id_point']; ?>"
                         data-device-id="<?php echo $point['id_dev']; ?>"
                         style="position: absolute; left: <?php echo $point['x_pos']; ?>%; top: <?php echo $point['y_pos']; ?>%; cursor: grab; transform: translate(-50%, -50%); <?php echo $isHighlighted ? 'z-index: 50;' : ''; ?>">
                        
                        <div class="point-icon" title="<?php echo htmlspecialchars($tooltip); ?>">
                            <?php if ($point['point_type'] == 'reader'): ?>
                                <span class="glyphicon glyphicon-qrcode text-info" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php elseif ($point['point_type'] == 'controller'): ?>
                                <span class="glyphicon glyphicon-cog text-warning" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php elseif ($point['point_type'] == 'door'): ?>
                                <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'ok-circle text-success' : 'ban-circle text-danger'; ?>" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-record text-muted" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php endif; ?>
                            
                            <?php if ($isHighlighted): ?>
                                <span class="highlight-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px; border-radius: 50%; border: 3px solid #ff9800; animation: pulse-ring 1.5s ease-in-out infinite; pointer-events: none;"></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($point['label']): ?>
                            <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 10px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : '#ddd'; ?>; <?php echo $isHighlighted ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                                <?php echo htmlspecialchars($point['label']); ?>
                                <?php if ($point['id_dev']): ?>
                                    <span style="color: #999; font-size: 8px;"> (id_dev=<?php echo $point['id_dev']; ?>)</span>
                                <?php endif; ?>
                                <?php if ($isHighlighted): ?>
                                    <span style="color: #ff9800; font-size: 10px;"> ★</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php if ($point['id_dev']): ?>
                                <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 9px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : '#ddd'; ?>; <?php echo $isHighlighted ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                                    <span style="color: #999;">id_dev=<?php echo $point['id_dev']; ?></span>
                                    <?php if ($isHighlighted): ?>
                                        <span style="color: #ff9800; font-size: 10px;"> ★</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="point-actions" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); display: none; z-index: 20;">
                           <button class="btn btn-xs btn-danger delete-point" 
        data-point-id="<?php echo $point['id_point']; ?>"
        onclick="testDelete(<?php echo $point['id_point']; ?>, this)">
    <span class="glyphicon glyphicon-trash"></span>
</button>
                        </div>
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
                                        <th>ID</th>
                                        <th>Тип</th>
                                        <th>Устройство</th>
                                        <th>ID устройства (id_dev)</th>
                                        <th>Метка</th>
                                        <th>Позиция</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($points)): ?>
                                        <?php 
                                        $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
                                        foreach ($points as $point): 
                                            $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                                            $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
                                        ?>
                                            <tr data-point-id="<?php echo $point['id_point']; ?>" <?php echo $isHighlighted ? 'class="success"' : ''; ?>>
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
                                                    <?php if ($isHighlighted): ?>
                                                        <span class="label label-warning">ВЫДЕЛЕНА</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>X: <?php echo round($point['x_pos'], 1); ?>% Y: <?php echo round($point['y_pos'], 1); ?>%</td>
                                                <td>
                                                    <span class="label label-<?php echo $status == 'online' ? 'success' : 'danger'; ?>">
                                                        <?php echo $status == 'online' ? 'Online' : 'Offline'; ?>
                                                    </span>
                                                </td>
                                                <td>
    <button class="btn btn-xs btn-danger" 
            onclick="deletePointDirect(<?php echo $point['id_point']; ?>, this)"
            title="Удалить точку">
        <span class="glyphicon glyphicon-trash"></span>
    </button>
    <?php if ($isHighlighted): ?>
        <span class="glyphicon glyphicon-flag" style="color: #ff9800; margin-left: 5px;"></span>
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
        <!-- ФОРМА ДОБАВЛЕНИЯ СЧИТЫВАТЕЛЯ               -->
        <!-- ========================================== -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <span class="glyphicon glyphicon-qrcode"></span> 
                            Добавить считыватель
                            <small class="text-muted">(устройства с id_reader)</small>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id . '?floor=' . $current_floor_id); ?>" 
                              class="form-inline" id="addReaderForm">
                            <input type="hidden" name="action" value="add_reader">
                            <div class="form-group">
                                <label>X (0-100%): </label>
                                <input type="number" name="x" step="0.1" class="form-control" style="width: 80px;" 
                                       min="0" max="100" required placeholder="0-100" id="readerX">
                            </div>
                            <div class="form-group">
                                <label>Y (0-100%): </label>
                                <input type="number" name="y" step="0.1" class="form-control" style="width: 80px;" 
                                       min="0" max="100" required placeholder="0-100" id="readerY">
                            </div>
                            <div class="form-group">
                                <label>Устройство: </label>
                                <select name="device_id" class="form-control" style="width: 200px;" required id="readerDevice">
                                    <option value="">Выберите считыватель</option>
                                    <?php foreach ($readers as $device): ?>
                                        <option value="<?php echo $device['id_dev']; ?>">
                                            <?php echo htmlspecialchars($device['name']); ?> (id_dev=<?php echo $device['id_dev']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($readers)): ?>
                                    <small class="text-warning">Нет доступных считывателей</small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Метка: </label>
                                <input type="text" name="label" class="form-control" style="width: 150px;" 
                                       placeholder="Название" maxlength="100" id="readerLabel">
                            </div>
                            <button type="submit" class="btn btn-info" id="submitReaderBtn" <?php echo empty($readers) ? 'disabled' : ''; ?>>
                                <span class="glyphicon glyphicon-plus"></span> Добавить
                            </button>
                        </form>
                        <small class="text-muted">X и Y - процентное положение от левого и верхнего края (0-100)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- ФОРМА ДОБАВЛЕНИЯ КОНТРОЛЛЕРА              -->
        <!-- ========================================== -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <span class="glyphicon glyphicon-cog"></span> 
                            Добавить контроллер
                            <small class="text-muted">(устройства без id_reader)</small>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id . '?floor=' . $current_floor_id); ?>" 
                              class="form-inline" id="addControllerForm">
                            <input type="hidden" name="action" value="add_controller">
                            <div class="form-group">
                                <label>X (0-100%): </label>
                                <input type="number" name="x" step="0.1" class="form-control" style="width: 80px;" 
                                       min="0" max="100" required placeholder="0-100" id="controllerX">
                            </div>
                            <div class="form-group">
                                <label>Y (0-100%): </label>
                                <input type="number" name="y" step="0.1" class="form-control" style="width: 80px;" 
                                       min="0" max="100" required placeholder="0-100" id="controllerY">
                            </div>
                            <div class="form-group">
                                <label>Устройство: </label>
                                <select name="device_id" class="form-control" style="width: 200px;" required id="controllerDevice">
                                    <option value="">Выберите контроллер</option>
                                    <?php foreach ($controllers as $device): ?>
                                        <option value="<?php echo $device['id_dev']; ?>">
                                            <?php echo htmlspecialchars($device['name']); ?> (id_dev=<?php echo $device['id_dev']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($controllers)): ?>
                                    <small class="text-warning">Нет доступных контроллеров</small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Метка: </label>
                                <input type="text" name="label" class="form-control" style="width: 150px;" 
                                       placeholder="Название" maxlength="100" id="controllerLabel">
                            </div>
                            <button type="submit" class="btn btn-warning" id="submitControllerBtn" <?php echo empty($controllers) ? 'disabled' : ''; ?>>
                                <span class="glyphicon glyphicon-plus"></span> Добавить
                            </button>
                        </form>
                        <small class="text-muted">X и Y - процентное положение от левого и верхнего края (0-100)</small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- БОКОВАЯ ПАНЕЛЬ С УСТРОЙСТВАМИ              -->
<!-- ========================================== -->
<div id="devicePanelWrapper" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); z-index: 100; display: flex; align-items: center;">
    
    <div id="panelAnchor" onclick="toggleDevicePanel()" style="
        width: 32px;
        height: 80px;
        background: #337ab7;
        border-radius: 4px 0 0 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        flex-shrink: 0;
        z-index: 101;
        margin-right: 0;
    ">
        <span id="anchorIcon" class="glyphicon glyphicon-chevron-right" style="font-size: 14px;"></span>
    </div>
    
    <div id="devicePanel" style="
        width: 280px;
        background: #fff;
        border: 1px solid #ddd;
        border-right: none;
        border-radius: 4px 0 0 4px;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform: translateX(0);
        margin-right: -1px;
    ">
        <div style="background: #337ab7; color: #fff; padding: 8px 12px; border-radius: 4px 0 0 0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <strong style="font-size: 13px;">
                <span class="glyphicon glyphicon-list"></span> Устройства
            </strong>
            <span style="font-size: 11px; opacity: 0.7;">
                Всего: <?php echo count($readers) + count($controllers); ?>
            </span>
        </div>
        
        <!-- Вкладки -->
        <ul class="nav nav-tabs" style="padding: 0 5px; flex-shrink: 0;">
            <li class="active" style="width: 50%; text-align: center;">
                <a href="#tabReaders" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-qrcode"></span> Считыватели (<?php echo count($readers); ?>)
                </a>
            </li>
            <li style="width: 50%; text-align: center;">
                <a href="#tabControllers" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-cog"></span> Контроллеры (<?php echo count($controllers); ?>)
                </a>
            </li>
        </ul>
        
        <!-- Содержимое вкладок -->
        <div class="tab-content" style="flex: 1; overflow: hidden; padding: 5px;">
            <!-- Считыватели -->
            <div class="tab-pane active" id="tabReaders" style="height: 100%; overflow-y: auto; padding: 5px;">
                <?php if (!empty($readers)): ?>
                    <?php foreach ($readers as $device): ?>
                        <div class="device-item reader-item" 
                             data-device-id="<?php echo $device['id_dev']; ?>"
                             data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                             style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: pointer; border-left: 3px solid #5bc0de; font-size: 12px; transition: all 0.2s ease;"
                             onclick="selectDevice(this, 'reader')"
                             onmouseover="this.style.background='#e8f0fe'"
                             onmouseout="if (!this.classList.contains('selected')) this.style.background='#f9f9f9'">
                            <span class="glyphicon glyphicon-qrcode text-info" style="margin-right: 5px;"></span>
                            <?php echo htmlspecialchars($device['name']); ?>
                            <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                        <span class="glyphicon glyphicon-info-sign"></span><br>
                        Нет доступных считывателей
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Контроллеры -->
            <div class="tab-pane" id="tabControllers" style="height: 100%; overflow-y: auto; padding: 5px;">
                <?php if (!empty($controllers)): ?>
                    <?php foreach ($controllers as $device): ?>
                        <div class="device-item controller-item" 
                             data-device-id="<?php echo $device['id_dev']; ?>"
                             data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                             style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: pointer; border-left: 3px solid #f0ad4e; font-size: 12px; transition: all 0.2s ease;"
                             onclick="selectDevice(this, 'controller')"
                             onmouseover="this.style.background='#fff3e0'"
                             onmouseout="if (!this.classList.contains('selected')) this.style.background='#f9f9f9'">
                            <span class="glyphicon glyphicon-cog text-warning" style="margin-right: 5px;"></span>
                            <?php echo htmlspecialchars($device['name']); ?>
                            <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                        <span class="glyphicon glyphicon-info-sign"></span><br>
                        Нет доступных контроллеров
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div id="devicePanelFooter" style="padding: 5px 10px; background: #f5f5f5; border-top: 1px solid #ddd; font-size: 11px; color: #999; flex-shrink: 0;">
            <span id="selectedDeviceInfo">Выберите устройство для добавления</span>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- МОДАЛЬНЫЕ ОКНА (этажи)                     -->
<!-- ========================================== -->

<div class="modal fade" id="addFloorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Добавить этаж</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_floor">
                    <div class="form-group">
                        <label>Номер этажа *</label>
                        <input type="number" name="new_floor_number" class="form-control" 
                               value="<?php echo count($floors) + 1; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Название этажа</label>
                        <input type="text" name="new_floor_name" class="form-control" 
                               placeholder="Например: 1 этаж - Вестибюль">
                    </div>
                    <div class="form-group">
                        <label>Изображение плана этажа *</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Максимальный размер: 20 МБ</small>
                    </div>
                    <button type="submit" class="btn btn-success">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="copyFloorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Копировать этаж</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>">
                    <input type="hidden" name="action" value="copy_floor">
                    <div class="form-group">
                        <label>Новый номер этажа *</label>
                        <input type="number" name="new_floor_number" class="form-control" 
                               value="<?php echo count($floors) + 1; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Название этажа</label>
                        <input type="text" name="new_floor_name" class="form-control" 
                               placeholder="Например: 2 этаж - Офисы">
                    </div>
                    <div class="alert alert-info">
                        <span class="glyphicon glyphicon-info-sign"></span>
                        Будут скопированы все точки с текущего этажа
                    </div>
                    <button type="submit" class="btn btn-primary">Копировать</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteFloorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-danger">Удалить этаж</h4>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этаж <strong><?php echo htmlspecialchars($current_floor['floor_name'] ?: $current_floor['floor_number'] . ' этаж'); ?></strong>?</p>
                <p class="text-danger">Все точки на этом этаже будут удалены!</p>
                <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>">
                    <input type="hidden" name="action" value="delete_floor">
                    <input type="hidden" name="delete_floor_id" value="<?php echo $current_floor_id; ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JQUERY UI DIALOG ДЛЯ ДОБАВЛЕНИЯ ТОЧКИ     -->
<!-- ========================================== -->
<div id="clickAddPointDialog" title="Добавить точку кликом" style="display: none;">
    <form id="clickAddPointForm">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Позиция X:</label>
                    <input type="text" class="form-control" id="clickX" readonly style="background: #f5f5f5; width: 100%;">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Позиция Y:</label>
                    <input type="text" class="form-control" id="clickY" readonly style="background: #f5f5f5; width: 100%;">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Устройство <span class="text-danger">*</span></label>
            <select class="form-control" id="clickDeviceId" required style="width: 100%;">
                <option value="">Выберите устройство</option>
                <?php foreach ($allDevices as $device): ?>
                    <option value="<?php echo $device['id_dev']; ?>" <?php echo $device['is_used'] ? 'disabled style="color:#999;background:#f5f5f5;"' : ''; ?>>
                        <?php echo htmlspecialchars($device['name']); ?> (id=<?php echo $device['id_dev']; ?>)
                        <?php if ($device['is_used']): ?>
                            ✓ (используется)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Тип точки</label>
            <select class="form-control" id="clickPointType" style="width: 100%;">
                <option value="reader">📡 Считыватель</option>
                <option value="controller">⚙️ Контроллер</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Метка (название)</label>
            <input type="text" class="form-control" id="clickLabel" 
                   placeholder="Например: Главный вход" maxlength="100" style="width: 100%;">
        </div>
    </form>
</div>

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
        var x = parseFloat($('input[name="x"]').val());
        var y = parseFloat($('input[name="y"]').val());
        
        if (isNaN(x) || x < 0 || x > 100) {
            e.preventDefault();
            showNotification('X должен быть от 0 до 100%', 'error');
            $('#controllerX').focus().select();
            return false;
        }
        
        if (isNaN(y) || y < 0 || y > 100) {
            e.preventDefault();
            showNotification('Y должен быть от 0 до 100%', 'error');
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
                showDragCoordinates(ui.position.left, ui.position.top, $(this));
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

function showDragCoordinates(left, top, $point) {
    var $tooltip = $('#dragCoordinates');
    if (!$tooltip.length) {
        $tooltip = $('<div id="dragCoordinates" style="position: fixed; background: rgba(0,0,0,0.8); color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 9999; pointer-events: none; display: none;"></div>');
        $('body').append($tooltip);
    }
    
    var parentWidth = $('#floorplanCanvas').width();
    var parentHeight = $('#floorplanCanvas').height();
    var xPercent = ((left + $point.outerWidth()/2) / parentWidth) * 100;
    var yPercent = ((top + $point.outerHeight()/2) / parentHeight) * 100;
    
    // Позиционируем подсказку относительно окна, а не canvas
    var offset = $point.offset();
    var tooltipX = offset.left + 30;
    var tooltipY = offset.top - 40;
    
    // Проверяем, чтобы подсказка не выходила за пределы окна
    var tooltipWidth = 200; // примерная ширина
    var tooltipHeight = 30; // примерная высота
    
    if (tooltipX + tooltipWidth > $(window).width()) {
        tooltipX = $(window).width() - tooltipWidth - 10;
    }
    if (tooltipY < 10) {
        tooltipY = 10;
    }
    if (tooltipY + tooltipHeight > $(window).height()) {
        tooltipY = $(window).height() - tooltipHeight - 10;
    }
    
    $tooltip.html('X: ' + Math.round(xPercent) + '% Y: ' + Math.round(yPercent) + '%')
        .css({
            left: tooltipX + 'px',
            top: tooltipY + 'px',
            display: 'block'
        });
}

function updateDragCoordinates(left, top, $point) {
    showDragCoordinates(left, top, $point);
}

function hideDragCoordinates() {
    $('#dragCoordinates').hide();
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
// ИСПРАВЛЕНИЕ: hover для кнопок удаления на плане
// ==========================================

$(document).ready(function() {
    var hoverTimer = null;
    var $currentPoint = null;
    
    // При наведении на точку - показываем кнопки
    $('.floorplan-point.draggable').on('mouseenter', function() {
        var $this = $(this);
        $currentPoint = $this;
        
        // Отменяем предыдущий таймер
        if (hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
        }
        
        // Показываем кнопки
        $this.find('.point-actions').stop(true, true).fadeIn(200);
    });
    
    // При уходе с точки - скрываем с задержкой
    $('.floorplan-point.draggable').on('mouseleave', function() {
        var $this = $(this);
        
        // Запускаем таймер на 300мс
        hoverTimer = setTimeout(function() {
            // Проверяем, находится ли курсор над кнопками или точкой
            var $hovered = $(document.elementFromPoint(
                event.clientX || 0,
                event.clientY || 0
            ));
            
            // Если курсор не над точкой и не над кнопками - скрываем
            if (!$hovered.closest('.floorplan-point.draggable, .point-actions').length) {
                $this.find('.point-actions').stop(true, true).fadeOut(200);
                hoverTimer = null;
            }
        }, 300);
    });
    
    // Если курсор над кнопками - не скрываем
    $(document).on('mouseenter', '.point-actions', function() {
        if (hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
        }
        $(this).stop(true, true).fadeIn(200);
    });
    
    // При уходе с кнопок - скрываем с задержкой
    $(document).on('mouseleave', '.point-actions', function() {
        var $this = $(this);
        var $parent = $this.closest('.floorplan-point.draggable');
        
        setTimeout(function() {
            var $hovered = $(document.elementFromPoint(
                event.clientX || 0,
                event.clientY || 0
            ));
            
            if (!$hovered.closest('.floorplan-point.draggable').length) {
                $parent.find('.point-actions').stop(true, true).fadeOut(200);
            }
        }, 200);
    });
});


</script>

<!-- ========================================== -->
<!-- СТИЛИ                                       -->
<!-- ========================================== -->
<style>
.floor-selector {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.floor-btn {
    min-width: 40px;
    border-radius: 0 !important;
    position: relative;
}

.floor-btn:first-child {
    border-radius: 4px 0 0 4px !important;
}

.floor-btn:last-child {
    border-radius: 0 4px 4px 0 !important;
}

.floor-btn .floor-badge {
    background: rgba(0,0,0,0.1);
    color: inherit;
    margin-left: 4px;
}

.floor-btn.active .floor-badge {
    background: rgba(255,255,255,0.3);
    color: #fff;
}

.floor-btn.active {
    background: #337ab7;
    color: #fff;
    border-color: #2e6da4;
}

.floor-btn.active::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #337ab7;
}

.floor-btn:hover .floor-badge {
    background: rgba(0,0,0,0.15);
}

.floor-btn.active:hover .floor-badge {
    background: rgba(255,255,255,0.4);
}

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

.floorplan-point {
    z-index: 10;
    transition: all 0.2s ease;
    user-select: none;
}

.floorplan-point:hover {
    z-index: 20;
}

.floorplan-point .point-icon {
    text-shadow: 0 0 5px rgba(255,255,255,0.8);
    cursor: grab;
}

.floorplan-point .point-icon:active {
    cursor: grabbing;
}

.floorplan-point.status-online .point-icon {
    opacity: 1;
}

.floorplan-point.status-offline .point-icon {
    opacity: 0.4;
}

.floorplan-point.draggable {
    cursor: grab;
}

.floorplan-point.draggable:active {
    cursor: grabbing;
}



.floorplan-point.dragging {
    z-index: 30 !important;
    transform: translate(-50%, -50%) scale(1.2) !important;
    filter: drop-shadow(0 0 20px rgba(51, 122, 183, 0.5));
    transition: none !important;
}

.floorplan-point.preview {
    animation: preview-pulse 1s ease-in-out infinite;
}

.floorplan-point.preview .point-label {
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

@keyframes preview-pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.6;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 1;
    }
}

.point-actions {
    display: block !important;
    z-index: 30;
    opacity: 0.6;
}
.point-actions:hover {
    opacity: 1;
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
    background-color: #f0ad4e;
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

#devicePanel {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.device-item {
    transition: all 0.2s ease;
}

.device-item:hover {
    transform: translateX(-3px);
}

.device-item.selected {
    border-left-color: #ff9800 !important;
    background: #e8f0fe !important;
}

.device-item.reader-item.selected {
    background: #e8f0fe !important;
}

.device-item.controller-item.selected {
    background: #fff3e0 !important;
}

#saveIndicator {
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    font-weight: bold;
}

#pointInfo, #dragCoordinates {
    font-family: monospace;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    font-size: 11px !important;
    pointer-events: none;
    white-space: nowrap;
}

#clickCoords {
    color: #ff9800;
    font-weight: bold;
}

#notification {
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    font-weight: 500;
}

.glyphicon-spin {
    animation: spin 1s infinite linear;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(359deg); }
}

.click-point-dialog .ui-dialog-titlebar {
    background: #337ab7;
    color: #fff;
    border: none;
}

.click-point-dialog .ui-dialog-titlebar-close {
    color: #fff;
}

.click-point-dialog .ui-dialog-buttonpane {
    padding: 10px 15px;
    border-top: 1px solid #ddd;
}

.click-point-dialog .ui-dialog-buttonpane button {
    margin: 0 5px;
}

.click-point-dialog .ui-dialog-content {
    padding: 20px;
}

.click-point-dialog .form-group {
    margin-bottom: 15px;
}

.click-point-dialog .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.click-point-dialog .form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}

.click-point-dialog .form-control[readonly] {
    background-color: #f5f5f5;
}

.click-point-dialog .row {
    margin-right: -15px;
    margin-left: -15px;
}

.click-point-dialog .col-md-6 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
    width: 50%;
}

.click-point-dialog .btn {
    display: inline-block;
    margin-bottom: 0;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.click-point-dialog .btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.click-point-dialog .btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}

.click-point-dialog .btn-success:hover {
    background-color: #449d44;
    border-color: #398439;
}

.click-point-dialog .btn-success:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.click-point-dialog .text-danger {
    color: #a94442;
}

.click-point-dialog .ui-dialog {
    z-index: 1000;
}

.click-point-dialog .ui-widget-overlay {
    background: #000;
    opacity: 0.5;
    z-index: 999;
}

.nav-tabs {
    border-bottom: 1px solid #ddd;
}

.nav-tabs > li {
    margin-bottom: -1px;
}

.nav-tabs > li > a {
    border-radius: 4px 4px 0 0;
    color: #555;
}

.nav-tabs > li.active > a {
    background: #fff;
    border-bottom-color: transparent;
    font-weight: bold;
}

.tab-content {
    padding: 5px 0;
}
</style>