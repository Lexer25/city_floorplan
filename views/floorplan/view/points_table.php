<!-- ========================================== -->
<!-- ИНФОРМАЦИЯ О ТОЧКАХ (ТАБЛИЦА)              -->
<!-- ========================================== -->
<div class="row" style="margin-top: 15px;">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    Точки прохода на плане
                    <span class="pull-right text-muted" style="font-size: 12px; font-weight: normal;">
                        <span class="glyphicon glyphicon-info-sign"></span>
                        Нажмите <span class="glyphicon glyphicon-search"></span> чтобы найти устройство на плане
                    </span>
                </h4>
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
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($points)): ?>
                                <?php 
                                $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
                                $relatedIds = isset($relatedIds) ? $relatedIds : array();
                                $counter = 0;
                                foreach ($points as $index => $point): 
                                    $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
                                    $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
                                    $isRelated = in_array($point['id_point'], $relatedIds);
                                    $counter++;
                                ?>
                                    <tr data-point-id="<?php echo $point['id_point']; ?>" 
                                        data-device-id="<?php echo $point['id_dev']; ?>"
                                        <?php echo $isHighlighted ? 'class="success"' : ($isRelated ? 'class="info"' : ''); ?>>
                                        <td><?php echo $counter; ?></td>
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
                                            <!-- Кнопка "Найти на плане" -->
                                            <?php if ($point['id_dev']): ?>
                                                <a href="<?php echo URL::site('floorplan/findDevice?id_dev=' . $point['id_dev']); ?>" 
                                                   class="btn btn-xs btn-primary" 
                                                   title="Найти устройство на плане"
                                                   target="_blank">
                                                    <span class="glyphicon glyphicon-search"></span>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-xs btn-default" disabled title="Нет ID устройства">
                                                    <span class="glyphicon glyphicon-search"></span>
                                                </button>
                                            <?php endif; ?>
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