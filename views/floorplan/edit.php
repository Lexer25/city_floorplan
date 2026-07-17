<!-- ========================================== -->
<!-- РЕДАКТИРОВАНИЕ ПЛАНА                       -->
<!-- ========================================== -->

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-edit"></span>
            Редактирование плана: <?php echo htmlspecialchars($floorplan['name']); ?>
            <?php if (isset($highlightData) && $highlightData): ?>
                <span class="label label-success" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-flag"></span> 
                    Точка найдена (id_dev=<?php echo $searchIdDev; ?>)
                </span>
            <?php endif; ?>
            <span class="pull-right">
                <span class="label label-info" id="pointCountLabel">Точек: <?php echo count($points); ?></span>
            </span>
        </h3>
    </div>
    <div class="panel-body">

        <?php include 'edit/header.php'; ?>
        <?php include 'edit/form_update.php'; ?>
        <?php include 'edit/floor_selector.php'; ?>
        <?php include 'edit/toolbar.php'; ?>
        <?php include 'edit/canvas.php'; ?>
        <?php include 'edit/points_table.php'; ?>
        <?php include 'edit/form_reader.php'; ?>
        <?php include 'edit/form_controller.php'; ?>

    </div>
</div>

<?php include 'edit/print_elements.php'; ?>
<?php include 'edit/device_panel.php'; ?>
<?php include 'edit/modals.php'; ?>
<?php include 'edit/dialog.php'; ?>

<?php include 'edit/scripts.php'; ?>
<?php include 'edit/styles.php'; ?>