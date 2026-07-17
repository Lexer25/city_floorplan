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
                <?php foreach ($allDevices as $device): ?>
                    <option value="<?php echo $device['id_dev']; ?>" <?php echo $device['is_used'] ? 'disabled style="color:#999;background:#f5f5f5;"' : ''; ?>>
                        <?php echo htmlspecialchars($device['name']); ?> (id=<?php echo $device['id_dev']; ?>)
                        <?php if ($device['is_used']): ?>
                            ✓ (используется)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Тип точки</label>
            <select class="form-control" id="clickPointType" style="width: 100%;">
                <option value="reader">📡 Считыватель</option>
                <option value="controller">⚙️ Контроллер</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Метка (название)</label>
            <input type="text" class="form-control" id="clickLabel" 
                   placeholder="Например: Главный вход" maxlength="100" style="width: 100%;">
        </div>
    </form>
</div>