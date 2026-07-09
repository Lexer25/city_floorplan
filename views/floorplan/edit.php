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
            <span class="glyphicon glyphicon-<?php echo $mode == 'view' ? 'eye-open' : 'edit'; ?>"></span>
            <?php echo $mode == 'view' ? 'Просмотр' : 'Редактирование'; ?> плана: <?php echo htmlspecialchars($floorplan['name']); ?>
            <?php if (isset($highlightData) && $highlightData): ?>
                <span class="label label-success" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-flag"></span> 
                    Точка найдена (id_dev=<?php echo $searchIdDev; ?>)
                </span>
            <?php endif; ?>
        </h3>
    </div>
    <div class="panel-body">

        <?php if ($mode == 'edit'): ?>
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
        <?php else: ?>
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-12">
                    <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-arrow-left"></span> Назад
                    </a>
                    <?php if ($is_admin): ?>
                        <a href="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>" class="btn btn-primary">
                            <span class="glyphicon glyphicon-edit"></span> Редактировать
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

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
                    <div class="floorplan-point <?php echo $statusClass; ?> <?php echo $mode == 'edit' ? 'draggable' : ''; ?> <?php echo $isHighlighted ? 'highlighted' : ''; ?>" 
                         data-point-id="<?php echo $point['id_point']; ?>"
                         data-device-id="<?php echo $point['id_dev']; ?>"
                         style="position: absolute; left: <?php echo $point['x_pos']; ?>%; top: <?php echo $point['y_pos']; ?>%; cursor: <?php echo $mode == 'edit' ? 'grab' : 'default'; ?>; transform: translate(-50%, -50%); <?php echo $isHighlighted ? 'z-index: 50;' : ''; ?>">
                        
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

                        <?php if ($mode == 'edit'): ?>
                            <div class="point-actions" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); display: none; z-index: 20;">
                                <button class="btn btn-xs btn-danger delete-point" data-point-id="<?php echo $point['id_point']; ?>">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </div>
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
                                        <th>ID</th>
                                        <th>Тип</th>
                                        <th>Устройство</th>
                                        <th>ID устройства (id_dev)</th>
                                        <th>Метка</th>
                                        <th>Позиция</th>
                                        <th>Статус</th>
                                        <th><?php echo $mode == 'edit' ? 'Действия' : ''; ?></th>
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
                                                    <?php if ($mode == 'edit'): ?>
                                                        <button class="btn btn-xs btn-danger delete-point" data-point-id="<?php echo $point['id_point']; ?>">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                        </button>
                                                    <?php endif; ?>
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
        <!-- ФОРМА ДОБАВЛЕНИЯ ТОЧКИ                     -->
        <!-- ========================================== -->
        <?php if ($mode == 'edit'): ?>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4 class="panel-title">Добавить точку прохода</h4>
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
        <?php endif; ?>

    </div>
</div>

<!-- ========================================== -->
<!-- МОДАЛЬНЫЕ ОКНА -->
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
});
</script>

<?php if ($mode == 'edit'): ?>
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
<?php endif; ?>

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
</style>