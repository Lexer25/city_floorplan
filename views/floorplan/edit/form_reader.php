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