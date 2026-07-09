<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-database"></span>
            Проверка базы данных модуля "Планы объекта"
        </h3>
    </div>
    <div class="panel-body">
        
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

        <!-- Общий статус -->
        <div class="alert alert-<?php echo $result['all_ok'] ? 'success' : 'warning'; ?>">
            <strong>
                <?php if ($result['all_ok']): ?>
                    <span class="glyphicon glyphicon-ok-circle"></span> 
                    Все таблицы, генераторы и триггеры установлены корректно
                <?php else: ?>
                    <span class="glyphicon glyphicon-exclamation-sign"></span> 
                    Некоторые компоненты базы данных отсутствуют. Рекомендуется выполнить установку.
                <?php endif; ?>
            </strong>
        </div>

        <!-- Таблицы -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Таблицы</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Имя таблицы</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['tables'] as $table => $exists): ?>
                            <tr>
                                <td><code><?php echo $table; ?></code></td>
                                <td>
                                    <?php if ($exists): ?>
                                        <span class="label label-success">Существует</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Отсутствует</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Генераторы -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Генераторы (Sequences)</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Имя генератора</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['generators'] as $generator => $exists): ?>
                            <tr>
                                <td><code><?php echo $generator; ?></code></td>
                                <td>
                                    <?php if ($exists): ?>
                                        <span class="label label-success">Существует</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Отсутствует</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Триггеры -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Триггеры</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Имя триггера</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['triggers'] as $trigger => $exists): ?>
                            <tr>
                                <td><code><?php echo $trigger; ?></code></td>
                                <td>
                                    <?php if ($exists): ?>
                                        <span class="label label-success">Существует</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Отсутствует</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Действия -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <?php if (!$result['all_ok']): ?>
                    <a href="<?php echo URL::site('floorplan/install/install'); ?>" 
                       class="btn btn-success"
                       onclick="return confirm('Установить все необходимые таблицы, генераторы и триггеры?')">
                        <span class="glyphicon glyphicon-play"></span> Установить базу данных
                    </a>
                <?php endif; ?>
                
                <?php if ($result['all_ok']): ?>
                    <button type="button" class="btn btn-danger" 
                            onclick="if(confirm('Удалить все таблицы модуля? Данные будут потеряны!')) 
                                     { document.getElementById('uninstallForm').submit(); }">
                        <span class="glyphicon glyphicon-trash"></span> Удалить базу данных
                    </button>
                    
                    <form id="uninstallForm" method="POST" action="<?php echo URL::site('floorplan/install/uninstall'); ?>">
                        <?php echo Form::hidden('confirm', 'yes'); ?>
                    </form>
                <?php endif; ?>
                
                <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> Назад к планам
                </a>
            </div>
        </div>

    </div>
</div>