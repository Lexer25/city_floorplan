<!-- ========================================== -->
<!-- ЭЛЕМЕНТЫ ДЛЯ ПЕЧАТИ                        -->
<!-- ========================================== -->

<!-- Шапка для печати -->
<div class="print-header" style="display: none; text-align: center; margin-bottom: 20px; padding: 10px; border-bottom: 2px solid #333;">
    <div style="font-size: 20px; font-weight: bold; color: #333;">
        <?php echo isset($building) && $building ? htmlspecialchars($building['name']) : 'План объекта'; ?>
    </div>
    <div style="font-size: 14px; color: #666; margin-top: 5px;">
        План: <?php echo htmlspecialchars($floorplan['name']); ?> 
        | Этаж: <?php echo htmlspecialchars($current_floor['floor_name'] ?: $current_floor['floor_number'] . ' этаж'); ?>
        | Дата: <?php echo date('d.m.Y H:i'); ?>
    </div>
</div>

<!-- Легенда для печати -->
<div class="print-legend" style="display: none; margin-top: 20px; padding: 15px; border-top: 2px solid #333; font-size: 13px;">
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <div style="font-weight: bold; min-width: 120px;">Легенда:</div>
        <?php
        $types = array();
        foreach ($points as $point) {
            $type = $point['point_type'];
            $types[$type] = isset($types[$type]) ? $types[$type] + 1 : 1;
        }
        $icons = array(
            'reader' => '📡 Считыватель',
            'controller' => '⚙️ Контроллер',
            'door' => '🚪 Дверь',
            'turnstile' => '🚧 Турникет',
            'camera' => '📷 Камера',
        );
        foreach ($types as $type => $count) {
            $label = isset($icons[$type]) ? $icons[$type] : $type;
            echo '<div style="margin-right: 15px;">' . $label . ' — <strong>' . $count . '</strong> шт.</div>';
        }
        ?>
        <div style="margin-left: auto; font-weight: bold;">
            Всего точек: <strong><?php echo count($points); ?></strong>
        </div>
    </div>
</div>

<!-- Подпись для печати -->
<div class="print-footer" style="display: none; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; font-size: 13px; text-align: right;">
    <div style="display: inline-block; width: 200px; text-align: center;">
        ____________________
        <br>
        <span style="font-size: 11px; color: #999;">Подпись ответственного</span>
    </div>
</div>