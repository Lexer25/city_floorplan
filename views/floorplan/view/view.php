<!-- ========================================== -->
<!-- ПРОСМОТР ПЛАНА                             -->
<!-- ========================================== -->

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-eye-open"></span>
            Просмотр плана: <?php echo htmlspecialchars($floorplan['name']); ?>
            <?php if (isset($highlightData) && $highlightData): ?>
                <span class="label label-success" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-flag"></span> 
                    Точка найдена (id_dev=<?php echo $searchIdDev; ?>)
                </span>
            <?php endif; ?>
            <?php if (!empty($relatedData)): ?>
                <span class="label label-info" style="margin-left: 10px;">
                    <span class="glyphicon glyphicon-link"></span> 
                    Связанных: <?php echo count($relatedData); ?>
                </span>
            <?php endif; ?>
        </h3>
    </div>
    <div class="panel-body">

        <?php include 'view/header.php'; ?>
        <?php include 'view/toolbar.php'; ?>
        <?php include 'view/canvas.php'; ?>
        <?php include 'view/points_table.php'; ?>
        <?php include 'view/related_table.php'; ?>

    </div>
</div>

<?php include 'view/scripts.php'; ?>
<?php include 'view/styles.php'; ?>