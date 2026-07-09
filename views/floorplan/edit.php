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
                            <?php if ($point['point_type'] == 'door'): ?>
                                <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'ok-circle text-success' : 'ban-circle text-danger'; ?>" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php elseif ($point['point_type'] == 'turnstile'): ?>
                                <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'unchecked text-success' : 'remove-circle text-danger'; ?>" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'record text-success' : 'record text-danger'; ?>" style="font-size: 28px; <?php echo $isHighlighted ? 'font-size: 36px;' : ''; ?>"></span>
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
                            <button class="btn btn-xs btn-danger delete-point" data-point-id="<?php echo $point['id_point']; ?>">
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
                                                <td><?php echo $point['point_type']; ?></td>
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
                                                    <button class="btn btn-xs btn-danger delete-point" data-point-id="<?php echo $point['id_point']; ?>">
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
        <!-- ФОРМА ДОБАВЛЕНИЯ ТОЧКИ (обычная)           -->
        <!-- ========================================== -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <span class="glyphicon glyphicon-plus"></span> 
                            Добавить точку прохода
                            <small class="text-muted">(или используйте режим клика на плане)</small>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id . '?floor=' . $current_floor_id); ?>" class="form-inline">
                            <input type="hidden" name="action" value="add_point">
                            <div class="form-group">
                                <label>X: </label>
                                <input type="number" name="x" step="0.1" class="form-control" style="width: 80px;" required>
                            </div>
                            <div class="form-group">
                                <label>Y: </label>
                                <input type="number" name="y" step="0.1" class="form-control" style="width: 80px;" required>
                            </div>
                            <div class="form-group">
                                <label>Устройство (id_dev): </label>
                                <select name="device_id" class="form-control" style="width: 200px;" required>
                                    <option value="">Выберите устройство</option>
                                    <?php foreach ($availableDevices as $device): ?>
                                        <option value="<?php echo $device['id_dev']; ?>">
                                            <?php echo htmlspecialchars($device['name']); ?> (id_dev=<?php echo $device['id_dev']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Тип: </label>
                                <select name="point_type" class="form-control" style="width: 120px;">
                                    <option value="door">Дверь</option>
                                    <option value="turnstile">Турникет</option>
                                    <option value="reader">Считыватель</option>
                                    <option value="camera">Камера</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Метка: </label>
                                <input type="text" name="label" class="form-control" style="width: 150px;">
                            </div>
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </form>
                        <small class="text-muted">Подсказка: X и Y - это процентное положение от левого и верхнего края (0-100)</small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- БОКОВАЯ ПАНЕЛЬ С УСТРОЙСТВАМИ              -->
<!-- ========================================== -->
<div id="devicePanel" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); width: 220px; z-index: 100; background: #fff; border: 1px solid #ddd; border-right: none; border-radius: 4px 0 0 4px; box-shadow: -2px 0 10px rgba(0,0,0,0.1); max-height: 70vh; display: flex; flex-direction: column; transition: all 0.3s ease;">
    <div style="background: #337ab7; color: #fff; padding: 8px 12px; border-radius: 4px 0 0 0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
        <strong style="font-size: 13px;">
            <span class="glyphicon glyphicon-list"></span> Устройства
        </strong>
        <button class="btn btn-xs" onclick="toggleDevicePanel()" style="color: #fff; background: rgba(255,255,255,0.2); border: none; border-radius: 3px;">
            <span class="glyphicon glyphicon-chevron-right" id="panelToggleIcon"></span>
        </button>
    </div>
    <div id="deviceList" style="overflow-y: auto; padding: 5px; flex: 1;">
        <?php if (!empty($availableDevices)): ?>
            <?php foreach ($availableDevices as $device): ?>
                <div class="device-item" 
                     data-device-id="<?php echo $device['id_dev']; ?>"
                     data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                     style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: pointer; border-left: 3px solid #337ab7; font-size: 12px; transition: all 0.2s ease;"
                     onclick="selectDevice(this)"
                     onmouseover="this.style.background='#e8f0fe'"
                     onmouseout="if (!this.classList.contains('selected')) this.style.background='#f9f9f9'">
                    <?php echo htmlspecialchars($device['name']); ?>
                    <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                <span class="glyphicon glyphicon-info-sign"></span><br>
                Нет доступных устройств
            </div>
        <?php endif; ?>
    </div>
    <div id="devicePanelFooter" style="padding: 5px 10px; background: #f5f5f5; border-top: 1px solid #ddd; font-size: 11px; color: #999; flex-shrink: 0;">
        <span id="selectedDeviceInfo">Выберите устройство для добавления</span>
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
                <?php foreach ($availableDevices as $device): ?>
                    <option value="<?php echo $device['id_dev']; ?>">
                        <?php echo htmlspecialchars($device['name']); ?> (id=<?php echo $device['id_dev']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Тип точки</label>
            <select class="form-control" id="clickPointType" style="width: 100%;">
                <option value="door">🚪 Дверь</option>
                <option value="turnstile">🚧 Турникет</option>
                <option value="reader">📡 Считыватель</option>
                <option value="camera">📷 Камера</option>
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
                        <input type="file" name="image" class="form-control" required>
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
});

// ==========================================
// РЕЖИМ КЛИКА ДЛЯ ДОБАВЛЕНИЯ ТОЧЕК
// ==========================================

var clickModeEnabled = false;
var clickX = 0;
var clickY = 0;
var selectedDeviceId = null;
var selectedDeviceName = null;
var panelVisible = true;

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
            $('#selectedDeviceDisplay').text('Выбрано: ' + selectedDeviceName);
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
    }
}

function toggleDevicePanel() {
    panelVisible = !panelVisible;
    var $panel = $('#devicePanel');
    var $icon = $('#panelToggleIcon');
    
    if (panelVisible) {
        $panel.css('transform', 'translateY(-50%) translateX(0)');
        $icon.removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
    } else {
        $panel.css('transform', 'translateY(-50%) translateX(calc(100% - 32px))');
        $icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
    }
}

function selectDevice(el) {
    $('.device-item').css('border-left-color', '#337ab7').css('background', '#f9f9f9').removeClass('selected');
    
    $(el).css('border-left-color', '#ff9800').css('background', '#fff3e0').addClass('selected');
    
    selectedDeviceId = $(el).data('device-id');
    selectedDeviceName = $(el).data('device-name');
    
    $('#selectedDeviceInfo').text('Выбрано: ' + selectedDeviceName);
    
    if (clickModeEnabled) {
        $('#selectedDeviceDisplay').text('Выбрано: ' + selectedDeviceName).css('color', '#337ab7');
    }
}

$(document).ready(function() {
    $('#floorplanImage').on('click', function(e) {
        if (!clickModeEnabled) return;
        
        if (!selectedDeviceId) {
            alert('Сначала выберите устройство на боковой панели');
            return;
        }
        
        var $this = $(this);
        var offset = $this.offset();
        var x = e.pageX - offset.left;
        var y = e.pageY - offset.top;
        var width = $this.width();
        var height = $this.height();
        
        var xPercent = Math.round((x / width) * 1000) / 10;
        var yPercent = Math.round((y / height) * 1000) / 10;
        
        xPercent = Math.max(0, Math.min(100, xPercent));
        yPercent = Math.max(0, Math.min(100, yPercent));
        
        clickX = xPercent;
        clickY = yPercent;
        
        $('#clickX').val(xPercent + '%');
        $('#clickY').val(yPercent + '%');
        $('#clickDeviceId').val(selectedDeviceId);
        $('#clickLabel').val(selectedDeviceName || '');
        $('#clickPointType').val('door');
        
        $('#clickAddPointDialog').dialog('open');
    });
});

function saveClickPoint() {
    var deviceId = $('#clickDeviceId').val();
    var pointType = $('#clickPointType').val();
    var label = $('#clickLabel').val();
    var floorplanId = <?php echo $current_floor_id; ?>;
    
    // ОТЛАДКА: выводим данные в консоль
    console.log('=== saveClickPoint ===');
    console.log('deviceId:', deviceId);
    console.log('pointType:', pointType);
    console.log('label:', label);
    console.log('floorplanId:', floorplanId);
    console.log('clickX:', clickX);
    console.log('clickY:', clickY);
    
    if (!deviceId) {
        alert('Пожалуйста, выберите устройство');
        $('#clickDeviceId').focus();
        return;
    }
    
    if (clickX === 0 && clickY === 0) {
        alert('Ошибка: координаты не заданы');
        return;
    }
    
    var dialog = $('#clickAddPointDialog');
    var buttons = dialog.dialog('option', 'buttons');
    
    // Меняем текст кнопки
    if (buttons && buttons[1]) {
        buttons[1].text = '<span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Добавление...';
        buttons[1].disabled = true;
        dialog.dialog('option', 'buttons', buttons);
    }
    
    var postData = {
        floorplan_id: floorplanId,
        x: clickX,
        y: clickY,
        device_id: deviceId,
        point_type: pointType,
        label: label || ''
    };
    
    console.log('Отправка данных:', postData);
    
    $.ajax({
        url: '<?php echo URL::site("floorplan/addPointAjax"); ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            console.log('Ответ сервера:', response);
            
            if (response.success) {
                dialog.dialog('close');
                
                var countText = $('#pointCountLabel').text();
                var count = parseInt(countText.replace('Точек: ', ''));
                if (!isNaN(count)) {
                    $('#pointCountLabel').text('Точек: ' + (count + 1));
                }
                
                location.reload();
            } else {
                alert('Ошибка при добавлении точки: ' + (response.error || 'Неизвестная ошибка'));
                // Восстанавливаем кнопку
                if (buttons && buttons[1]) {
                    buttons[1].text = 'Добавить точку';
                    buttons[1].disabled = false;
                    dialog.dialog('option', 'buttons', buttons);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr, status, error);
            console.error('Response text:', xhr.responseText);
            alert('Ошибка при отправке запроса: ' + error + '\n\nПроверьте консоль браузера (F12)');
            
            if (buttons && buttons[1]) {
                buttons[1].text = 'Добавить точку';
                buttons[1].disabled = false;
                dialog.dialog('option', 'buttons', buttons);
            }
        }
    });
}

$('<style>').text(
    '.glyphicon-spin {\n' +
    '    -webkit-animation: spin 1s infinite linear;\n' +
    '    animation: spin 1s infinite linear;\n' +
    '}\n' +
    '@-webkit-keyframes spin {\n' +
    '    0% { -webkit-transform: rotate(0deg); }\n' +
    '    100% { -webkit-transform: rotate(359deg); }\n' +
    '}\n' +
    '@keyframes spin {\n' +
    '    0% { transform: rotate(0deg); }\n' +
    '    100% { transform: rotate(359deg); }\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog-titlebar {\n' +
    '    background: #337ab7;\n' +
    '    color: #fff;\n' +
    '    border: none;\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog-titlebar-close {\n' +
    '    color: #fff;\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog-buttonpane {\n' +
    '    padding: 10px 15px;\n' +
    '    border-top: 1px solid #ddd;\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog-buttonpane button {\n' +
    '    margin: 0 5px;\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog-content {\n' +
    '    padding: 20px;\n' +
    '}\n' +
    '.click-point-dialog .form-group {\n' +
    '    margin-bottom: 15px;\n' +
    '}\n' +
    '.click-point-dialog .form-group label {\n' +
    '    display: block;\n' +
    '    font-weight: bold;\n' +
    '    margin-bottom: 5px;\n' +
    '}\n' +
    '.click-point-dialog .form-control {\n' +
    '    display: block;\n' +
    '    width: 100%;\n' +
    '    height: 34px;\n' +
    '    padding: 6px 12px;\n' +
    '    font-size: 14px;\n' +
    '    line-height: 1.42857143;\n' +
    '    color: #555;\n' +
    '    background-color: #fff;\n' +
    '    background-image: none;\n' +
    '    border: 1px solid #ccc;\n' +
    '    border-radius: 4px;\n' +
    '    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);\n' +
    '    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);\n' +
    '    -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;\n' +
    '    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;\n' +
    '}\n' +
    '.click-point-dialog .form-control[readonly] {\n' +
    '    background-color: #f5f5f5;\n' +
    '}\n' +
    '.click-point-dialog .row {\n' +
    '    margin-right: -15px;\n' +
    '    margin-left: -15px;\n' +
    '}\n' +
    '.click-point-dialog .col-md-6 {\n' +
    '    position: relative;\n' +
    '    min-height: 1px;\n' +
    '    padding-right: 15px;\n' +
    '    padding-left: 15px;\n' +
    '    float: left;\n' +
    '    width: 50%;\n' +
    '}\n' +
    '.click-point-dialog .btn {\n' +
    '    display: inline-block;\n' +
    '    margin-bottom: 0;\n' +
    '    font-weight: 400;\n' +
    '    text-align: center;\n' +
    '    white-space: nowrap;\n' +
    '    vertical-align: middle;\n' +
    '    -ms-touch-action: manipulation;\n' +
    '    touch-action: manipulation;\n' +
    '    cursor: pointer;\n' +
    '    background-image: none;\n' +
    '    border: 1px solid transparent;\n' +
    '    padding: 6px 12px;\n' +
    '    font-size: 14px;\n' +
    '    line-height: 1.42857143;\n' +
    '    border-radius: 4px;\n' +
    '    -webkit-user-select: none;\n' +
    '    -moz-user-select: none;\n' +
    '    -ms-user-select: none;\n' +
    '    user-select: none;\n' +
    '}\n' +
    '.click-point-dialog .btn-default {\n' +
    '    color: #333;\n' +
    '    background-color: #fff;\n' +
    '    border-color: #ccc;\n' +
    '}\n' +
    '.click-point-dialog .btn-success {\n' +
    '    color: #fff;\n' +
    '    background-color: #5cb85c;\n' +
    '    border-color: #4cae4c;\n' +
    '}\n' +
    '.click-point-dialog .btn-success:hover {\n' +
    '    background-color: #449d44;\n' +
    '    border-color: #398439;\n' +
    '}\n' +
    '.click-point-dialog .btn-success:disabled {\n' +
    '    opacity: 0.65;\n' +
    '    cursor: not-allowed;\n' +
    '}\n' +
    '.click-point-dialog .text-danger {\n' +
    '    color: #a94442;\n' +
    '}\n' +
    '.click-point-dialog .ui-dialog {\n' +
    '    z-index: 1000;\n' +
    '}\n' +
    '.click-point-dialog .ui-widget-overlay {\n' +
    '    background: #000;\n' +
    '    opacity: 0.5;\n' +
    '    z-index: 999;\n' +
    '}'
).appendTo('head');
</script>

<!-- ========================================== -->
<!-- JS ДЛЯ ПЕРЕТАСКИВАНИЯ ТОЧЕК                -->
<!-- ========================================== -->
<script>
$(document).ready(function() {
    var $points = $('.floorplan-point.draggable');
    var $container = $('#floorplanCanvas');

    if ($points.length > 0 && $container.length > 0) {
        $points.draggable({
            containment: $container,
            cursor: 'grab',
            handle: '.point-icon',
            start: function(e, ui) {
                $(this).find('.point-actions').show();
                $(this).css('z-index', 20);
            },
            stop: function(e, ui) {
                var $point = $(this);
                var pointId = $point.data('point-id');
                var parentWidth = $container.width();
                var parentHeight = $container.height();
                var left = ui.position.left;
                var top = ui.position.top;
                var xPercent = (left / parentWidth) * 100;
                var yPercent = (top / parentHeight) * 100;
                
                xPercent = Math.max(0, Math.min(100, xPercent));
                yPercent = Math.max(0, Math.min(100, yPercent));
                
                $point.css('left', xPercent + '%');
                $point.css('top', yPercent + '%');
                
                var data = {
                    points: [{
                        id: pointId,
                        x: xPercent,
                        y: yPercent
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
                            console.log('Position saved for point ' + pointId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        });

        $points.hover(
            function() {
                $(this).find('.point-actions').show();
            },
            function() {
                $(this).find('.point-actions').hide();
            }
        );
    }

    $('.delete-point').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!confirm('Удалить точку?')) return;
        
        var pointId = $(this).data('point-id');
        var $point = $('.floorplan-point[data-point-id="' + pointId + '"]');
        
        $.ajax({
            url: '<?php echo URL::site("floorplan/deletePointAjax"); ?>',
            type: 'POST',
            data: { point_id: pointId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $point.fadeOut(300, function() {
                        $(this).remove();
                        location.reload();
                    });
                } else {
                    alert('Ошибка при удалении точки: ' + (response.error || 'Неизвестная ошибка'));
                }
            },
            error: function(xhr, status, error) {
                alert('Ошибка при удалении точки: ' + error);
            }
        });
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

.floorplan-point.draggable:hover .point-actions {
    display: block !important;
}

.point-actions {
    display: none;
    z-index: 30;
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

.label-warning {
    background-color: #ff9800;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    margin-left: 5px;
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
    background: #fff3e0 !important;
}

@-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(359deg); }
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(359deg); }
}
.glyphicon-spin {
    -webkit-animation: spin 1s infinite linear;
    animation: spin 1s infinite linear;
}
</style>