<?php defined('SYSPATH') OR die('No direct script access.'); ?>

<script>
// ==========================================
// МАСШТАБИРОВАНИЕ (встроенное)
// ==========================================
var FloorplanZoom = {
    currentZoom: 1,
    minZoom: 0.1,
    maxZoom: 3,
    zoomStep: 0.1,
    currentFloorId: 0,
    isInitialized: false,
    
    init: function(floorId) {
        if (this.isInitialized) return;
        this.isInitialized = true;
        
        this.currentFloorId = floorId;
        
        console.log('FloorplanZoom init for floor ' + this.currentFloorId);
        this.restoreZoom();
        this.bindEvents();
        
        console.log('FloorplanZoom initialized');
    },
    
    restoreZoom: function() {
        var self = this;
        var zoom = null;
        
        // 1. Проверяем URL параметр
        var url = new URL(window.location.href);
        var urlZoom = url.searchParams.get('zoom');
        if (urlZoom !== null && urlZoom !== '') {
            zoom = parseFloat(urlZoom);
            if (zoom > 0) {
                self.currentZoom = zoom;
                self.applyZoom();
                return;
            }
        }
        
        // 2. Проверяем localStorage
        if (typeof(Storage) !== 'undefined') {
            var localZoom = localStorage.getItem('floorplan_zoom_' + this.currentFloorId);
            if (localZoom !== null && localZoom !== '') {
                zoom = parseFloat(localZoom);
                if (zoom > 0) {
                    self.currentZoom = zoom;
                    self.applyZoom();
                    return;
                }
            }
        }
        
        // 3. Автоподгонка
        setTimeout(function() { self.fitToScreen(); }, 400);
    },
    
    applyZoom: function() {
        var $canvas = $('#floorplanCanvas');
        if ($canvas.length === 0) return;
        
        this.currentZoom = Math.max(this.minZoom, Math.min(this.maxZoom, this.currentZoom));
        
        $canvas.css({
            'transform': 'scale(' + this.currentZoom + ')',
            'transform-origin': 'top left',
            'transition': 'transform 0.15s ease'
        });
        
        var $display = $('#zoomLevelDisplay');
        if ($display.length) {
            $display.text(Math.round(this.currentZoom * 100));
        }
        
        this.saveZoomAll(this.currentZoom);
    },
    
    saveZoomAll: function(zoom) {
        if (typeof(Storage) !== 'undefined') {
            try {
                localStorage.setItem('floorplan_zoom_' + this.currentFloorId, zoom.toString());
            } catch(e) {}
        }
        
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('zoom', zoom.toString());
            window.history.replaceState({}, '', url.href);
        } catch(e) {}
    },
    
    fitToScreen: function() {
        var $container = $('#floorplanScrollable');
        var $canvas = $('#floorplanCanvas');
        
        if ($container.length === 0 || $canvas.length === 0) return;
        
        var containerWidth = $container.width() - 30;
        var containerHeight = $container.height() - 30;
        
        var canvasWidth = typeof window.floorplanWidth !== 'undefined' ? window.floorplanWidth : 800;
        var canvasHeight = typeof window.floorplanHeight !== 'undefined' ? window.floorplanHeight : 600;
        
        if (canvasWidth <= 0 || canvasHeight <= 0) {
            this.currentZoom = 1;
            this.applyZoom();
            return;
        }
        
        var scaleX = containerWidth / canvasWidth;
        var scaleY = containerHeight / canvasHeight;
        var newZoom = Math.min(scaleX, scaleY, 1);
        
        this.currentZoom = newZoom < 1 ? newZoom : 1;
        this.applyZoom();
    },
    
    zoomIn: function() {
        if (this.currentZoom < this.maxZoom) {
            this.currentZoom = Math.min(this.currentZoom + this.zoomStep, this.maxZoom);
            this.applyZoom();
        }
    },
    
    zoomOut: function() {
        if (this.currentZoom > this.minZoom) {
            this.currentZoom = Math.max(this.currentZoom - this.zoomStep, this.minZoom);
            this.applyZoom();
        }
    },
    
    resetZoom: function() {
        this.currentZoom = 1;
        this.applyZoom();
    },
    
    bindEvents: function() {
        var self = this;
        
        // Колесико мыши (Ctrl+Scroll) — ТОЛЬКО НАД КАРТИНКОЙ
        $('#floorplanScrollable').on('wheel', function(e) {
            if (e.ctrlKey || e.altKey) {
                e.preventDefault();
                e.stopPropagation();
                
                var delta = e.originalEvent.deltaY;
                if (delta < 0) {
                    self.zoomIn();
                } else if (delta > 0) {
                    self.zoomOut();
                }
                return false;
            }
        }, { passive: false });
        
        // Блокируем масштабирование браузера
        $(document).on('wheel', function(e) {
            if ((e.ctrlKey || e.altKey) && $(e.target).closest('#floorplanScrollable').length) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }, { passive: false });
        
        // Клавиатура
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === '=' || e.key === '+')) {
                e.preventDefault();
                self.zoomIn();
                return false;
            }
            if ((e.ctrlKey || e.metaKey) && e.key === '-') {
                e.preventDefault();
                self.zoomOut();
                return false;
            }
            if ((e.ctrlKey || e.metaKey) && e.key === '0') {
                e.preventDefault();
                self.resetZoom();
                return false;
            }
        });
        
        // Изменение размера окна
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() { self.fitToScreen(); }, 500);
        });
    }
};

// Глобальные функции
function zoomIn() { FloorplanZoom.zoomIn(); }
function zoomOut() { FloorplanZoom.zoomOut(); }
function resetZoom() { FloorplanZoom.resetZoom(); }
function fitToScreen() { FloorplanZoom.fitToScreen(); }
</script>