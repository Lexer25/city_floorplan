<!-- ========================================== -->
<!-- КОНТЕЙНЕР ДЛЯ ПЛАНА                        -->
<!-- ========================================== -->
<div class="floorplan-scrollable" id="floorplanScrollable">
    <div id="floorplanCanvas" style="position: relative; width: <?php echo $floorplan['width']; ?>px; height: <?php echo $floorplan['height']; ?>px; margin: 0 auto; transform: scale(1); transform-origin: top left;">
        <img src="<?php echo URL::base() . $floorplan['image']; ?>" 
             id="floorplanImage" 
             style="width: 100%; height: 100%; display: block;"
             alt="<?php echo htmlspecialchars($floorplan['name']); ?>">

        <?php 
        $highlightPointId = isset($highlightData) && $highlightData ? $highlightData['id_point'] : null;
        $relatedIds = isset($relatedIds) ? $relatedIds : array();
        
        foreach ($points as $point): 
            $status = isset($deviceStatuses[$point['id_dev']]) ? $deviceStatuses[$point['id_dev']]['status'] : 'unknown';
            $statusClass = $status == 'online' ? 'status-online' : 'status-offline';
            
            $isHighlighted = ($highlightPointId && $point['id_point'] == $highlightPointId);
            $isRelated = in_array($point['id_point'], $relatedIds);
            
            $classes = $statusClass . ' draggable';
            if ($isHighlighted) $classes .= ' highlighted';
            if ($isRelated) $classes .= ' related';
            
            $tooltip = $point['label'] ?: $point['device_name'];
            if ($point['id_dev']) {
                $tooltip .= ' (id_dev=' . $point['id_dev'] . ')';
            }
            if ($isHighlighted) {
                $tooltip .= ' ★ ИСКОМОЕ';
            } elseif ($isRelated) {
                $tooltip .= ' 🔗 СВЯЗАННОЕ';
            }
        ?>
            <div class="floorplan-point <?php echo $classes; ?>" 
                 data-point-id="<?php echo $point['id_point']; ?>"
                 data-device-id="<?php echo $point['id_dev']; ?>"
                 style="position: absolute; left: <?php echo $point['x_pos']; ?>%; top: <?php echo $point['y_pos']; ?>%; transform: translate(-50%, -50%); <?php echo $isHighlighted ? 'z-index: 50;' : ''; ?>">
                
                <div class="point-icon" title="<?php echo htmlspecialchars($tooltip); ?>">
                    <?php if ($point['point_type'] == 'reader'): ?>
                        <span class="glyphicon glyphicon-qrcode text-info" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                    <?php elseif ($point['point_type'] == 'controller'): ?>
                        <span class="glyphicon glyphicon-cog text-warning" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                    <?php elseif ($point['point_type'] == 'door'): ?>
                        <span class="glyphicon glyphicon-<?php echo $status == 'online' ? 'ok-circle text-success' : 'ban-circle text-danger'; ?>" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                    <?php elseif ($point['point_type'] == 'turnstile'): ?>
                        <span class="glyphicon glyphicon-resize-horizontal text-warning" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                    <?php else: ?>
                        <span class="glyphicon glyphicon-record text-muted" style="font-size: <?php echo ($isHighlighted || $isRelated) ? '36px' : '28px'; ?>;"></span>
                    <?php endif; ?>
                    
                    <?php if ($isHighlighted): ?>
                        <span class="highlight-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px; border-radius: 50%; border: 3px solid #ff9800; animation: pulse-ring 1.5s ease-in-out infinite; pointer-events: none;"></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($point['label']): ?>
                    <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 10px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : ($isRelated ? '#ff9800' : '#ddd'); ?>; <?php echo ($isHighlighted || $isRelated) ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                        <?php echo htmlspecialchars($point['label']); ?>
                        <?php if ($point['id_dev']): ?>
                            <span style="color: #999; font-size: 8px;"> (id_dev=<?php echo $point['id_dev']; ?>)</span>
                        <?php endif; ?>
                        <?php if ($isHighlighted): ?>
                            <span style="color: #ff9800; font-size: 10px;"> ★</span>
                        <?php elseif ($isRelated): ?>
                            <span style="color: #ff9800; font-size: 10px;"> 🔗</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if ($point['id_dev']): ?>
                        <div class="point-label" style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 9px; white-space: nowrap; background: rgba(255,255,255,0.9); padding: 1px 6px; border-radius: 3px; border: 1px solid <?php echo $isHighlighted ? '#ff9800' : ($isRelated ? '#ff9800' : '#ddd'); ?>; <?php echo ($isHighlighted || $isRelated) ? 'font-weight: bold; color: #ff9800;' : ''; ?>">
                            <span style="color: #999;">id_dev=<?php echo $point['id_dev']; ?></span>
                            <?php if ($isHighlighted): ?>
                                <span style="color: #ff9800; font-size: 10px;"> ★</span>
                            <?php elseif ($isRelated): ?>
                                <span style="color: #ff9800; font-size: 10px;"> 🔗</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="point-actions" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); display: none; z-index: 20;">
                    <button class="btn btn-xs btn-danger delete-point" 
                            data-point-id="<?php echo $point['id_point']; ?>"
                            onclick="deletePoint(<?php echo $point['id_point']; ?>, this)">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>