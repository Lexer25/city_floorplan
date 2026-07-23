<!-- ========================================== -->
<!-- СТИЛИ                                       -->
<!-- ========================================== -->
<style>
.floorplan-toolbar {
    background: #f8f9fa;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.floorplan-toolbar .btn-group {
    display: flex;
    gap: 2px;
}

.floorplan-toolbar .btn-group .btn {
    padding: 4px 10px;
    font-size: 12px;
}

.floorplan-toolbar .zoom-level {
    font-size: 12px;
    color: #666;
    min-width: 80px;
}

.floorplan-toolbar .zoom-level strong {
    color: #333;
}

.floorplan-toolbar .text-muted {
    font-size: 11px;
    color: #999;
}

.floorplan-scrollable {
    overflow: auto;
    position: relative;
    border: 1px solid #ddd;
    border-radius: 0 0 4px 4px;
    background: #fafafa;
    max-height: 600px;
    min-height: 400px;
}

#floorplanCanvas {
    position: relative;
    margin: 0 auto;
    transform-origin: top left;
    transition: transform 0.15s ease;
}

.floorplan-container {
    background: #fafafa;
    min-height: 400px;
}

.floorplan-point {
    position: absolute;
    /* Центрирование через margin */
    margin-left: -14px;  /* половина ширины иконки (28px/2) */
    margin-top: -14px;   /* половина высоты иконки (28px/2) */
}

.floorplan-point:hover {
    z-index: 20;
    transform: translate(-50%, -50%) scale(1.1) !important;
}

.floorplan-point .point-icon {
    text-shadow: 0 0 5px rgba(255,255,255,0.8);
}

.floorplan-point.status-online .point-icon {
    opacity: 1;
}

.floorplan-point.status-offline .point-icon {
    opacity: 0.4;
}

.floorplan-point.related .point-icon {
    animation: related-pulse 2s ease-in-out infinite;
}

.floorplan-point.related .point-icon .glyphicon {
    filter: drop-shadow(0 0 15px rgba(255, 152, 0, 0.5)) !important;
}

@keyframes related-pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        filter: drop-shadow(0 0 10px rgba(255, 152, 0, 0.3));
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        filter: drop-shadow(0 0 25px rgba(255, 152, 0, 0.7));
    }
}

.text-success {
    color: #5cb85c;
}

.text-danger {
    color: #d9534f;
}

.label-default {
    background-color: #777;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

@keyframes pulse-ring {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

.floorplan-point.highlighted {
    z-index: 50 !important;
    animation: highlight-bounce 1.5s ease-in-out infinite;
}

.floorplan-point.highlighted .point-icon {
    filter: drop-shadow(0 0 20px rgba(255, 152, 0, 0.8));
}

@keyframes highlight-bounce {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        transform: translate(-50%, -50%) scale(1.15);
    }
}

tr.success {
    background-color: #fff3e0 !important;
    border-left: 3px solid #ff9800;
}

tr.success td {
    background-color: #fff3e0 !important;
}

tr.info {
    background-color: #e3f2fd !important;
    border-left: 3px solid #2196f3;
}

tr.info td {
    background-color: #e3f2fd !important;
}

.label-warning {
    background-color: #ff9800;
    color: #fff;
}

#connectionsSvg {
    pointer-events: none;
}
</style>