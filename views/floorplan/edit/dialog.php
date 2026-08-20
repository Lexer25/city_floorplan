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
            <input type="text" class="form-control" id="clickDeviceName" readonly style="background: #f5f5f5; width: 100%; font-weight: bold; color: #337ab7;">
            <input type="hidden" id="clickDeviceId" value="">
            <input type="hidden" id="clickPointType" value="">
            <small class="text-muted">Устройство выбрано при перетаскивании на план</small>
        </div>
        
        <div class="form-group">
            <label>Метка (название)</label>
            <input type="text" class="form-control" id="clickLabel" 
                   placeholder="Например: Главный вход" maxlength="100" style="width: 100%;">
        </div>
    </form>
</div>