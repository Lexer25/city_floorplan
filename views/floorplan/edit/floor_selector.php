<!-- ========================================== -->
<!-- ПЕРЕКЛЮЧАТЕЛЬ ЭТАЖЕЙ -->
<!-- ========================================== -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <div class="floor-selector">
            <div class="btn-group" role="group">
                <?php foreach ($floors as $floor): ?>
                    <a href="<?php echo URL::site('floorplan/edit/' . $main_floor_id . '?floor=' . $floor['id_floorplan']); ?>" 
                       class="btn btn-<?php echo $floor['id_floorplan'] == $current_floor_id ? 'primary' : 'default'; ?> floor-btn"
                       title="<?php echo htmlspecialchars($floor['floor_name'] ?: $floor['floor_number'] . ' этаж'); ?>">
                        <?php echo $floor['floor_number']; ?>
                        <span class="badge floor-badge"><?php echo $floor['points_count']; ?></span>
                    </a>
                <?php endforeach; ?>
                <?php if ($is_admin): ?>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addFloorModal">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#copyFloorModal">
                        <span class="glyphicon glyphicon-copy"></span>
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteFloorModal">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                <?php endif; ?>
            </div>
            <span class="label label-info" style="margin-left: 15px;">
                <span class="glyphicon glyphicon-<?php echo isset($building) && $building ? 'building' : 'map-marker'; ?>"></span>
                <?php echo isset($building) && $building ? htmlspecialchars($building['name']) : 'Здание'; ?>
                &bull; 
                <?php echo htmlspecialchars($current_floor['floor_name'] ?: $current_floor['floor_number'] . ' этаж'); ?>
            </span>
            <span class="pull-right text-muted">
                Всего этажей: <strong><?php echo count($floors); ?></strong>
                &bull; Точек: <strong><?php echo count($points); ?></strong>
            </span>
        </div>
    </div>
</div>