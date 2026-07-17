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
                                                    onclick="deletePoint(<?php echo $point['id_point']; ?>, this)"
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