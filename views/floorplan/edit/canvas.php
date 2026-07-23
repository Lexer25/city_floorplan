<!-- ========================================== -->
<!-- КОНТЕЙНЕР ДЛЯ ПЛАНА                        -->
<!-- ========================================== -->
<div class="floorplan-scrollable" id="floorplanScrollable">
    <div id="floorplanCanvas" style="position: relative; width: <?php echo $current_floor['width']; ?>px; height: <?php echo $current_floor['height']; ?>px; margin: 0 auto; transform: scale(1); transform-origin: top left;">
        <img src="<?php echo URL::base() . $current_floor['image']; ?>" 
             id="floorplanImage" 
             style="width: 100%; height: 100%; display: block;"
             alt="<?php echo htmlspecialchars($current_floor['name']); ?>">

        <?php 
        $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
        
        foreach ($points as $point): 
            $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
            $statusClass = $status == 'online' ? 'status-online' : 'status-offline';
            
            $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
            
            $tooltip = $point['label'] ?: $point['device_name'];
            if ($point['id_dev']) {
                $tooltip .= ' (id_dev=' . $point['id_dev'] . ')';
            }
            if ($isHighlighted) {
                $tooltip .= ' ★ ВЫДЕЛЕНА';
            }
        ?>
            <div class="floorplan-point <?php echo $statusClass; ?> draggable <?php echo $isHighlighted ? 'highlighted' : ''; ?>" 
     data-point-id="<?php echo $point['id_point']; ?>"
     data-device-id="<?php echo $point['id_dev']; ?>"
     style="position: absolute; left: <?php echo str_replace(',', '.', $point['x_pos']); ?>%; top: <?php echo str_replace(',', '.', $point['y_pos']); ?>%; cursor: grab; transform: translate(-50%, -50%); <?php echo $isHighlighted ? 'z-index: 50;' : ''; ?>">
                
                <div class="point-icon" title="<?php echo htmlspecialchars($tooltip); ?>">
                    <?php 
                    $iconPath = URL::base() . 'media/floorplan/icons/';
                    $iconSize = $isHighlighted ? 36 : 28;
                    ?>
                    <?php if ($point['point_type'] == 'reader'): ?>
                        <img src="<?php echo $iconPath; ?>reader.svg" 
                             style="width: <?php echo $iconSize; ?>px; height: <?php echo $iconSize; ?>px; vertical-align: middle;"
                             alt="Считыватель">
                    <?php elseif ($point['point_type'] == 'controller'): ?>
                        <img src="<?php echo $iconPath; ?>controller.svg" 
                             style="width: <?php echo $iconSize; ?>px; height: <?php echo $iconSize; ?>px; vertical-align: middle;"
                             alt="Контроллер">
                    <?php elseif ($point['point_type'] == 'door'): ?>
                        <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'ok-circle text-success' : 'ban-circle text-danger'; ?>" 
                              style="font-size: <?php echo $iconSize; ?>px;"></span>
                    <?php else: ?>
                        <span class="glyphicon glyphicon-record text-muted" 
                              style="font-size: <?php echo $iconSize; ?>px;"></span>
                    <?php endif; ?>
                    
                    <?php if ($isHighlighted): ?>
                        <span class="highlight-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px; border-radius: 50%; border: 3px solid #ff9800; animation: pulse-ring 1.5s ease-in-out infinite; pointer-events: none;"></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($point['label']): ?>
                    <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 10px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : '#ddd'; ?>; <?php echo $isHighlighted ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                        <?php echo htmlspecialchars($point['label']); ?>
                        <?php if ($point['id_dev']): ?>
                            <span style="color: #999; font-size: 8px;"> (id_dev=<?php echo $point['id_dev']; ?>)</span>
                        <?php endif; ?>
                        <?php if ($isHighlighted): ?>
                            <span style="color: #ff9800; font-size: 10px;"> ★</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if ($point['id_dev']): ?>
                        <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 9px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : '#ddd'; ?>; <?php echo $isHighlighted ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                            <span style="color: #999;">id_dev=<?php echo $point['id_dev']; ?></span>
                            <?php if ($isHighlighted): ?>
                                <span style="color: #ff9800; font-size: 10px;"> ★</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="point-actions" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); display: block; z-index: 20; opacity: 0.5;">
                    <button class="btn btn-xs btn-danger delete-point" 
                            data-point-id="<?php echo $point['id_point']; ?>"
                            onclick="deletePoint(<?php echo $point['id_point']; ?>, this)"
                            style="opacity: 1;">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>