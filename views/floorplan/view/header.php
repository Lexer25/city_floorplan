<!-- ========================================== -->
<!-- ПРОВЕРКА БАЗЫ ДАННЫХ                       -->
<!-- ========================================== -->
<?php
if ($is_admin) {
    $installModel = Model::factory('Floorplan_Installm');
    $checkResult = $installModel->checkDatabase();
    
    if (!$checkResult['all_ok']):
?>
    <div class="alert alert-warning alert-dismissible fade in">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-warning-sign"></span> 
            Внимание!
        </strong>
        <p>База данных модуля "Планы объекта" не полностью установлена.</p>
        <p>
            <a href="<?php echo URL::site('floorplan/install'); ?>" class="btn btn-warning btn-sm">
                <span class="glyphicon glyphicon-database"></span> Перейти к установке
            </a>
        </p>
    </div>
<?php
    endif;
}
?>

<!-- ========================================== -->
<!-- ИНФОРМАЦИЯ О ПОДСВЕЧЕННОЙ ТОЧКЕ            -->
<!-- ========================================== -->
<?php if (isset($highlightData) && $highlightData): ?>
    <div class="alert alert-success alert-dismissible fade in" style="margin-bottom: 15px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-search"></span> 
            🎯 Искомое устройство (id_dev=<?php echo $searchIdDev; ?>):
        </strong>
        <ul style="margin: 5px 0 0 20px;">
            <li><strong>ID точки:</strong> <?php echo $highlightData['id_point']; ?></li>
            <li><strong>Тип:</strong> <?php echo $highlightData['point_type']; ?></li>
            <li><strong>Устройство:</strong> <?php echo htmlspecialchars($highlightData['device_name'] ?: 'Не привязано'); ?></li>
            <li><strong>ID устройства:</strong> <?php echo $highlightData['id_dev']; ?></li>
            <li><strong>ID контроллера:</strong> <?php echo isset($id_ctrl) ? htmlspecialchars($id_ctrl) : '—'; ?></li>
            <li><strong>Метка:</strong> <?php echo htmlspecialchars($highlightData['label'] ?: '—'); ?></li>
            <li><strong>Позиция:</strong> X: <?php echo round($highlightData['x_pos'], 1); ?>%, Y: <?php echo round($highlightData['y_pos'], 1); ?>%</li>
        </ul>
    </div>
<?php else: ?>
    <?php if (isset($searchIdDev) && $searchIdDev): ?>
        <div class="alert alert-warning alert-dismissible fade in" style="margin-bottom: 15px;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>
                <span class="glyphicon glyphicon-warning-sign"></span> 
                Точка не найдена:
            </strong>
            <p style="margin: 5px 0 0 0;">
                Устройство с ID=<strong><?php echo $searchIdDev; ?></strong> не найдено на этом плане.
            </p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Информация о связанных устройствах -->
<?php if (!empty($relatedData)): ?>
    <div class="alert alert-info alert-dismissible fade in" style="margin-bottom: 15px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <span class="glyphicon glyphicon-link"></span> 
            Связанные устройства (id_ctrl=<?php echo htmlspecialchars($id_ctrl); ?>):
        </strong>
        <ul style="margin: 5px 0 0 20px;">
            <?php foreach ($relatedData as $related): ?>
                <li>
                    <strong>ID точки:</strong> <?php echo $related['id_point']; ?> |
                    <strong>Тип:</strong> <?php echo $related['point_type']; ?> |
                    <strong>Устройство:</strong> <?php echo htmlspecialchars($related['device_name'] ?: 'Не привязано'); ?> |
                    <strong>ID устройства:</strong> <?php echo $related['id_dev']; ?>
                    <?php if ($related['label']): ?>
                        | <strong>Метка:</strong> <?php echo htmlspecialchars($related['label']); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>