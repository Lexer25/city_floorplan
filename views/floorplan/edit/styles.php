<!-- ========================================== -->
<!-- СТИЛИ                                       -->
<!-- ========================================== -->
<style>
.floor-selector {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.floor-btn {
    min-width: 40px;
    border-radius: 0 !important;
    position: relative;
}

.floor-btn:first-child {
    border-radius: 4px 0 0 4px !important;
}

.floor-btn:last-child {
    border-radius: 0 4px 4px 0 !important;
}

.floor-btn .floor-badge {
    background: rgba(0,0,0,0.1);
    color: inherit;
    margin-left: 4px;
}

.floor-btn.active .floor-badge {
    background: rgba(255,255,255,0.3);
    color: #fff;
}

.floor-btn.active {
    background: #337ab7;
    color: #fff;
    border-color: #2e6da4;
}

.floor-btn.active::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #337ab7;
}

.floor-btn:hover .floor-badge {
    background: rgba(0,0,0,0.15);
}

.floor-btn.active:hover .floor-badge {
    background: rgba(255,255,255,0.4);
}

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

.floorplan-point {
    z-index: 10;
    transition: all 0.2s ease;
    user-select: none;
}

.floorplan-point:hover {
    z-index: 20;
}

.floorplan-point .point-icon {
    text-shadow: 0 0 5px rgba(255,255,255,0.8);
    cursor: grab;
}

.floorplan-point .point-icon:active {
    cursor: grabbing;
}

.floorplan-point.status-online .point-icon {
    opacity: 1;
}

.floorplan-point.status-offline .point-icon {
    opacity: 0.4;
}

.floorplan-point.draggable {
    cursor: grab;
}

.floorplan-point.draggable:active {
    cursor: grabbing;
}

.floorplan-point.draggable:hover .point-actions {
    opacity: 1 !important;
}

.floorplan-point.dragging {
    z-index: 30 !important;
    transform: translate(-50%, -50%) scale(1.2) !important;
    filter: drop-shadow(0 0 20px rgba(51, 122, 183, 0.5));
    transition: none !important;
}

.floorplan-point.preview {
    animation: preview-pulse 1s ease-in-out infinite;
}

.floorplan-point.preview .point-label {
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

@keyframes preview-pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.6;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 1;
    }
}

.point-actions {
    display: block !important;
    z-index: 30;
    opacity: 0.5;
    transition: opacity 0.2s ease;
}

.point-actions:hover {
    opacity: 1;
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

.label-info {
    background-color: #5bc0de;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

.label-warning {
    background-color: #f0ad4e;
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

#devicePanel {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.device-item {
    transition: all 0.2s ease;
}

.device-item:hover {
    transform: translateX(-3px);
}

.device-item.selected {
    border-left-color: #ff9800 !important;
    background: #e8f0fe !important;
}

.device-item.reader-item.selected {
    background: #e8f0fe !important;
}

.device-item.controller-item.selected {
    background: #fff3e0 !important;
}

#saveIndicator {
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    font-weight: bold;
}

#pointInfo, #dragCoordinates {
    font-family: monospace;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    font-size: 11px !important;
    pointer-events: none;
    white-space: nowrap;
}

#clickCoords {
    color: #ff9800;
    font-weight: bold;
}

#notification {
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    font-weight: 500;
}

.glyphicon-spin {
    animation: spin 1s infinite linear;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(359deg); }
}

.click-point-dialog .ui-dialog-titlebar {
    background: #337ab7;
    color: #fff;
    border: none;
}

.click-point-dialog .ui-dialog-titlebar-close {
    color: #fff;
}

.click-point-dialog .ui-dialog-buttonpane {
    padding: 10px 15px;
    border-top: 1px solid #ddd;
}

.click-point-dialog .ui-dialog-buttonpane button {
    margin: 0 5px;
}

.click-point-dialog .ui-dialog-content {
    padding: 20px;
}

.click-point-dialog .form-group {
    margin-bottom: 15px;
}

.click-point-dialog .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.click-point-dialog .form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}

.click-point-dialog .form-control[readonly] {
    background-color: #f5f5f5;
}

.click-point-dialog .row {
    margin-right: -15px;
    margin-left: -15px;
}

.click-point-dialog .col-md-6 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
    width: 50%;
}

.click-point-dialog .btn {
    display: inline-block;
    margin-bottom: 0;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.click-point-dialog .btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.click-point-dialog .btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}

.click-point-dialog .btn-success:hover {
    background-color: #449d44;
    border-color: #398439;
}

.click-point-dialog .btn-success:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.click-point-dialog .text-danger {
    color: #a94442;
}

.click-point-dialog .ui-dialog {
    z-index: 1000;
}

.click-point-dialog .ui-widget-overlay {
    background: #000;
    opacity: 0.5;
    z-index: 999;
}

.nav-tabs {
    border-bottom: 1px solid #ddd;
}

.nav-tabs > li {
    margin-bottom: -1px;
}

.nav-tabs > li > a {
    border-radius: 4px 4px 0 0;
    color: #555;
}

.nav-tabs > li.active > a {
    background: #fff;
    border-bottom-color: transparent;
    font-weight: bold;
}

.tab-content {
    padding: 5px 0;
}

/* ========================================== */
/* СТИЛИ ДЛЯ ПЕЧАТИ                           */
/* ========================================== */

@media print {
    .navbar,
    .navbar-fixed-top,
    .navbar-default,
    .floorplan-toolbar,
    .btn,
    .btn-group,
    .panel-heading .btn,
    #devicePanelWrapper,
    .point-actions,
    #clickAddPointDialog,
    .modal,
    .modal-backdrop,
    .floor-selector .btn-group .btn-success,
    .floor-selector .btn-group .btn-info,
    .floor-selector .btn-group .btn-danger,
    form,
    #saveIndicator,
    #pointInfo,
    #dragCoordinates,
    #notification,
    #myBtn,
    .navbar-right,
    .navbar-text,
    .print-header .btn,
    .print-legend .btn,
    .panel-heading .pull-right .label {
        display: none !important;
    }

    .print-header,
    .print-legend,
    .print-footer {
        display: block !important;
    }

    body {
        margin: 0 !important;
        padding: 10px !important;
        background: #fff !important;
    }

    .container-fluid {
        padding: 0 !important;
        max-width: 100% !important;
    }

    .panel {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }

    .panel-body {
        padding: 10px 0 !important;
    }

    .floorplan-scrollable {
        overflow: visible !important;
        border: 1px solid #333 !important;
        border-radius: 0 !important;
        max-height: none !important;
        min-height: auto !important;
        height: auto !important;
        background: #fff !important;
        padding: 5px !important;
    }

    #floorplanCanvas {
        transform: scale(1) !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 auto !important;
    }

    #floorplanCanvas img {
        width: 100% !important;
        height: auto !important;
        display: block !important;
    }

    .floorplan-point {
        z-index: 10 !important;
    }

    .floorplan-point .point-icon .glyphicon {
        font-size: 32px !important;
        opacity: 1 !important;
    }

    .floorplan-point .point-label {
        font-size: 11px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid #666 !important;
        padding: 1px 6px !important;
        border-radius: 2px !important;
        color: #000 !important;
        font-weight: normal !important;
        white-space: nowrap !important;
    }

    .highlight-ring {
        display: none !important;
    }

    .floor-selector .btn-group .btn {
        border: 1px solid #ccc !important;
        background: #fff !important;
        color: #000 !important;
        padding: 2px 8px !important;
        font-size: 12px !important;
    }

    .floor-selector .btn-group .btn-primary {
        background: #333 !important;
        color: #fff !important;
        border-color: #333 !important;
    }

    .floor-selector .btn-group .btn-primary .floor-badge {
        background: rgba(255,255,255,0.3) !important;
        color: #fff !important;
    }

    .floor-selector .btn-group .btn-default .floor-badge {
        background: #eee !important;
        color: #333 !important;
    }

    .floor-selector .btn-group .btn .floor-badge {
        display: inline-block !important;
    }

    .floor-selector .pull-right {
        display: none !important;
    }

    .panel-heading .pull-right {
        display: none !important;
    }

    .print-legend {
        border-top: 2px solid #333 !important;
        padding-top: 10px !important;
        margin-top: 15px !important;
        font-size: 12px !important;
    }

    .print-legend div {
        display: inline-block !important;
        margin-right: 20px !important;
    }

    .print-footer {
        margin-top: 30px !important;
        padding-top: 15px !important;
        border-top: 1px solid #ccc !important;
    }

    .print-footer div {
        display: inline-block !important;
        width: 200px !important;
        text-align: center !important;
    }
}
</style>