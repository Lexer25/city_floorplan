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
                <!-- ===== КНОПКА "ПОЛУЧИТЬ SQL" - СКАЧИВАЕТ ФАЙЛ УСТАНОВКИ ===== -->
                <a href="<?php echo URL::site('floorplan/install/downloadSql/install'); ?>" 
                   class="btn btn-info"
                   target="_blank">
                    <span class="glyphicon glyphicon-download"></span> SQL установить таблицы
                </a>
                
                <!-- ===== КНОПКА "SQL УДАЛИТЬ ТАБЛИЦЫ" - СКАЧИВАЕТ ФАЙЛ УДАЛЕНИЯ ===== -->
                <a href="<?php echo URL::site('floorplan/install/downloadSql/uninstall'); ?>" 
                   class="btn btn-danger"
                   target="_blank">
                    <span class="glyphicon glyphicon-download"></span> SQL удалить таблицы
                </a>
                
                <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> Назад к планам
                </a>
            </div>
        </div>

        <!-- Инструкция для пользователя -->
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    <strong>Как установить или удалить базу данных:</strong>
                    <ol style="margin-top: 5px; margin-bottom: 0;">
                        <li>
                            <strong>Для установки:</strong> нажмите кнопку 
                            <span class="label label-info">SQL установить таблицы</span> 
                            чтобы скачать файл <code>install.sql</code>, затем выполните его в вашем SQL-клиенте
                        </li>
                        <li>
                            <strong>Для удаления:</strong> нажмите кнопку 
                            <span class="label label-danger">SQL удалить таблицы</span> 
                            чтобы скачать файл <code>uninstall.sql</code>, затем выполните его в вашем SQL-клиенте
                            <span class="text-danger">(ВНИМАНИЕ: все данные будут потеряны!)</span>
                        </li>
                        <li>После выполнения SQL-скрипта обновите эту страницу для проверки статуса</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
</div>