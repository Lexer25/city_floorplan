<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-building"></span>
            Управление зданиями
            <?php if (isset($buildings) && count($buildings) > 0) echo '('.count($buildings).')'; ?>
        </h3>
    </div>
    <div class="panel-body">

        <?php if ($is_admin): ?>
            <a href="<?php echo URL::site('floorplan/addBuilding'); ?>" class="btn btn-success" style="margin-bottom: 15px;">
                <span class="glyphicon glyphicon-plus"></span> Добавить здание
            </a>
            <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default" style="margin-bottom: 15px;">
                <span class="glyphicon glyphicon-arrow-left"></span> Назад к планам
            </a>
        <?php endif; ?>

        <!-- Сообщения -->
        <?php
        $message = Session::instance()->get_once('message');
        $message_type = Session::instance()->get_once('message_type', 'info');
        if ($message):
        ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade in">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($buildings)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">Название здания</th>
                            <th width="30%">Адрес</th>
                            <th width="15%">Этажей</th>
                            <th width="25%">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($buildings as $building): ?>
                            <tr>
                                <td><?php echo $building['id_building']; ?></td>
                                <td><strong><?php echo htmlspecialchars($building['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($building['address']); ?></td>
                                <td>
                                    <span class="label label-info">
                                        <?php echo $building['floors_count_actual']; ?> 
                                        (всего <?php echo $building['floors_count']; ?>)
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <a href="<?php echo URL::site('floorplan/editBuilding/' . $building['id_building']); ?>" 
                                           class="btn btn-primary" title="Редактировать">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="<?php echo URL::site('floorplan/deleteBuilding/' . $building['id_building']); ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Удалить здание? Все этажи будут удалены!')"
                                           title="Удалить">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <span class="glyphicon glyphicon-info-sign" style="font-size: 24px; display: block; margin-bottom: 10px;"></span>
                Нет добавленных зданий
            </div>
        <?php endif; ?>

    </div>
</div>