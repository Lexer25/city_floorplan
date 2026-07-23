/**
 * Масштабирование для планов (без БД)
 * Хранение: localStorage + URL параметр
 * 
 * Приоритет восстановления:
 * 1. URL параметр (?zoom=0.8)
 * 2. localStorage
 * 3. Автоподгонка (fitToScreen)
 */
var FloorplanZoom = {
    currentZoom: 1,
    minZoom: 0.1,
    maxZoom: 3,
    zoomStep: 0.1,
    currentFloorId: 0,
    isInitialized: false,
    
    /**
     * Инициализация
     * @param {number} floorId - ID текущего этажа
     */
    init: function(floorId) {
        if (this.isInitialized) return;
        this.isInitialized = true;
        
        this.currentFloorId = floorId;
        this.restoreZoom();
        this.bindEvents();
        
        console.log('FloorplanZoom initialized for floor ' + floorId);
    },
    
    /**
     * Восстановление масштаба (в порядке приоритета)
     */
    restoreZoom: function() {
        var self = this;
        var zoom = null;
        
        // 1. Проверяем URL параметр (приоритет выше)
        var url = new URL(window.location.href);
        var urlZoom = url.searchParams.get('zoom');
        if (urlZoom !== null && urlZoom !== '') {
            zoom = parseFloat(urlZoom);
            if (zoom > 0 && zoom <= this.maxZoom) {
                console.log('Zoom restored from URL: ' + zoom);
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
                if (zoom > 0 && zoom <= this.maxZoom) {
                    console.log('Zoom restored from localStorage: ' + zoom);
                    self.currentZoom = zoom;
                    self.applyZoom();
                    return;
                }
            }
        }
        
        // 3. Если ничего нет — автоподгонка
        console.log('Zoom: using auto-fit');
        setTimeout(function() {
            self.fitToScreen();
        }, 400);
    },
    
    /**
     * Применение масштаба
     */
    applyZoom: function() {
        var $canvas = $('#floorplanCanvas');
        if ($canvas.length === 0) return;
        
        // Ограничиваем масштаб
        this.currentZoom = Math.max(this.minZoom, Math.min(this.maxZoom, this.currentZoom));
        
        // Применяем трансформацию
        $canvas.css('transform', 'scale(' + this.currentZoom + ')');
        $canvas.css('transform-origin', 'top left');
        
        // Обновляем дисплей
        var $display = $('#zoomLevelDisplay');
        if ($display.length) {
            $display.text(Math.round(this.currentZoom * 100));
        }
        
        // Сохраняем
        this.saveZoomAll(this.currentZoom);
    },
    
    /**
     * Сохранение масштаба (localStorage + URL)
     * @param {number} zoom - текущий масштаб
     */
    saveZoomAll: function(zoom) {
        // 1. Сохраняем в localStorage
        if (typeof(Storage) !== 'undefined') {
            try {
                localStorage.setItem('floorplan_zoom_' + this.currentFloorId, zoom.toString());
            } catch(e) {
                console.warn('Could not save to localStorage:', e);
            }
        }
        
        // 2. Сохраняем в URL (без перезагрузки страницы)
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('zoom', zoom.toString());
            window.history.replaceState({ path: url.href }, '', url.href);
        } catch(e) {
            console.warn('Could not update URL:', e);
        }
    },
    
    /**
     * Подогнать под размер экрана
     */
    fitToScreen: function() {
        var $container = $('#floorplanScrollable');
        var $canvas = $('#floorplanCanvas');
        
        if ($container.length === 0 || $canvas.length === 0) return;
        
        var containerWidth = $container.width() - 20;
        var containerHeight = $container.height() - 20;
        
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
    
    /**
     * Увеличить
     */
    zoomIn: function() {
        if (this.currentZoom < this.maxZoom) {
            this.currentZoom = Math.min(this.currentZoom + this.zoomStep, this.maxZoom);
            this.applyZoom();
        }
    },
    
    /**
     * Уменьшить
     */
    zoomOut: function() {
        if (this.currentZoom > this.minZoom) {
            this.currentZoom = Math.max(this.currentZoom - this.zoomStep, this.minZoom);
            this.applyZoom();
        }
    },
    
    /**
     * Сброс масштаба (100%)
     */
    resetZoom: function() {
        this.currentZoom = 1;
        this.applyZoom();
    },
    
    /**
     * Привязка событий
     */
    bindEvents: function() {
        var self = this;
        
        // Колесико мыши (Ctrl+Scroll)
        $(document).on('wheel', '#floorplanScrollable', function(e) {
            if (e.ctrlKey || e.altKey) {
                e.preventDefault();
                e.stopPropagation();
                
                var delta = e.originalEvent.deltaY;
                if (delta < 0) {
                    self.zoomIn();
                } else if (delta > 0) {
                    self.zoomOut();
                }
            }
        }, { passive: false });
        
        // Клавиатура: Ctrl + = (увеличение)
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === '=' || e.key === '+')) {
                e.preventDefault();
                self.zoomIn();
            }
            // Ctrl + - (уменьшение)
            if ((e.ctrlKey || e.metaKey) && e.key === '-') {
                e.preventDefault();
                self.zoomOut();
            }
            // Ctrl + 0 (сброс)
            if ((e.ctrlKey || e.metaKey) && e.key === '0') {
                e.preventDefault();
                self.resetZoom();
            }
        });
        
        // Изменение размера окна (с задержкой)
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                self.fitToScreen();
            }, 500);
        });
        
        console.log('Events bound');
    }
};

// ==========================================
// Глобальные функции для вызова из HTML
// ==========================================

function zoomIn() { 
    FloorplanZoom.zoomIn(); 
}

function zoomOut() { 
    FloorplanZoom.zoomOut(); 
}

function resetZoom() { 
    FloorplanZoom.resetZoom(); 
}

function fitToScreen() { 
    FloorplanZoom.fitToScreen(); 
}