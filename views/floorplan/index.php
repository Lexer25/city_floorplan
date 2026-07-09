<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-map-marker"></span>
            Планы объекта
            <?php if (isset($floorplans) && count($floorplans) > 0) echo '('.count($floorplans).')'; ?>
        </h3>
    </div>
    <div class="panel-body">

        <?php if ($is_admin): ?>
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-12">
                    <a href="<?php echo URL::site('floorplan/add'); ?>" class="btn btn-success">
                        <span class="glyphicon glyphicon-plus"></span> Добавить план
                    </a>
                    <a href="<?php echo URL::site('floorplan/buildings'); ?>" class="btn btn-info">
                        <span class="glyphicon glyphicon-building"></span> Управление зданиями
                    </a>
                    <!-- ===== НОВАЯ КНОПКА ===== -->
                    <a href="<?php echo URL::site('floorplan/install'); ?>" class="btn btn-warning">
                        <span class="glyphicon glyphicon-database"></span> Проверка БД
                    </a>
                </div>
            </div>
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

        <?php if (!empty($floorplans)): ?>
            <div class="row">
                <?php foreach ($floorplans as $plan): ?>
                    <div class="col-md-4" style="margin-bottom: 15px;">
                        <div class="thumbnail">
                            <img src="<?php echo URL::base() . $plan['image']; ?>" alt="<?php echo htmlspecialchars($plan['name']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                            <div class="caption">
                                <h4><?php echo htmlspecialchars($plan['name']); ?></h4>
                                <p><?php echo htmlspecialchars($plan['description']); ?></p>
                                <p>
                                    <span class="label label-info">Точек: <?php echo $plan['points_count']; ?></span>
                                    <?php if (isset($plan['floor_number'])): ?>
                                        <span class="label label-default">Этаж: <?php echo $plan['floor_number']; ?></span>
                                    <?php endif; ?>
                                </p>
                                <p>
                                    <a href="<?php echo URL::site('floorplan/view/' . $plan['id_floorplan']); ?>" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-eye-open"></span> Просмотр
                                    </a>
                                    <?php if ($is_admin): ?>
                                        <a href="<?php echo URL::site('floorplan/edit/' . $plan['id_floorplan']); ?>" class="btn btn-default">
                                            <span class="glyphicon glyphicon-edit"></span> Редактировать
                                        </a>
                                        <a href="<?php echo URL::site('floorplan/delete/' . $plan['id_floorplan']); ?>" class="btn btn-danger" onclick="return confirm('Удалить план?')">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <span class="glyphicon glyphicon-info-sign" style="font-size: 24px; display: block; margin-bottom: 10px;"></span>
                Нет добавленных планов
            </div>
        <?php endif; ?>

    </div>
</div>