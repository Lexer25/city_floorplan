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