<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">
            <span class="glyphicon glyphicon-arrow-left"></span> Назад
        </a>
        <?php if ($is_admin): ?>
            <a href="<?php echo URL::site('floorplan/edit/' . $floorplan['id_floorplan']); ?>" class="btn btn-primary">
                <span class="glyphicon glyphicon-edit"></span> Редактировать
            </a>
        <?php endif; ?>
        <span class="pull-right text-muted">
            <span class="glyphicon glyphicon-info-sign"></span>
            <?php echo count($points); ?> точек на плане
            <?php if (!empty($relatedData)): ?>
                &bull; <span class="text-info">Связанных: <?php echo count($relatedData); ?></span>
            <?php endif; ?>
        </span>
    </div>
</div>

<!-- ========================================== -->
<!-- ПАНЕЛЬ УПРАВЛЕНИЯ МАСШТАБОМ                -->
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
    </div>
    <span class="zoom-level">Масштаб: <strong id="zoomLevelDisplay">100</strong>%</span>
    <span class="text-muted" style="font-size: 11px; margin-left: 15px;">
        <span class="glyphicon glyphicon-info-sign"></span> 
        Ctrl+Колесо для масштабирования
    </span>
</div>