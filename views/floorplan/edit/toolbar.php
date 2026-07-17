<!-- ========================================== -->
<!-- ПАНЕЛЬ УПРАВЛЕНИЯ МАСШТАБОМ + РЕЖИМ КЛИКА -->
<!-- ========================================== -->
<div class="floorplan-toolbar">
    <div class="btn-group">
        <button type="button" class="btn btn-default btn-sm" onclick="zoomIn()" title="Увеличить (Ctrl++)">
            <span class="glyphicon glyphicon-plus"></span>
        </button>
        <button type="button" class="btn btn-default btn-sm" onclick="zoomOut()" title="Уменьшить (Ctrl+-)">
            <span class="glyphicon glyphicon-minus"></span>
        </button>
        <button type="button" class="btn btn-default btn-sm" onclick="resetZoom()" title="100% (Ctrl+0)">
            <span class="glyphicon glyphicon-resize-full"></span> 100%
        </button>
        <button type="button" class="btn btn-default btn-sm" onclick="fitToScreen()" title="Подогнать под размер экрана">
            <span class="glyphicon glyphicon-zoom-in"></span> По размеру
        </button>
        <button type="button" class="btn btn-success btn-sm" id="toggleClickMode" onclick="toggleClickMode()">
            <span class="glyphicon glyphicon-hand-up"></span> Режим клика
        </button>
    </div>
    <span class="zoom-level">Масштаб: <strong id="zoomLevelDisplay">100</strong>%</span>
    <span class="text-muted" style="font-size: 11px; margin-left: 15px;">
        <span class="glyphicon glyphicon-info-sign"></span> 
        Ctrl+Колесо для масштабирования
    </span>
    <span id="clickModeStatus" style="display: none; margin-left: 15px; color: #ff9800; font-weight: bold;">
        <span class="glyphicon glyphicon-hand-up"></span> 
        Кликните на плане для добавления точки
        <span id="selectedDeviceDisplay" style="color: #337ab7; margin-left: 10px;"></span>
        <span id="clickCoords" style="color: #ff9800; margin-left: 10px;"></span>
    </span>
</div>