<!-- ========================================== -->
<!-- ПОДКЛЮЧЕНИЕ МАСШТАБИРОВАНИЯ                -->
<!-- ========================================== -->
<script>
window.floorplanId = <?php echo $floorplan['id_floorplan']; ?>;
window.floorplanWidth = <?php echo $floorplan['width']; ?>;
window.floorplanHeight = <?php echo $floorplan['height']; ?>;

<?php if (isset($highlightData) && $highlightData): ?>
window.highlightPointId = <?php echo $highlightData['id_point']; ?>;
window.highlightX = <?php echo $highlightData['x_pos']; ?>;
window.highlightY = <?php echo $highlightData['y_pos']; ?>;
<?php endif; ?>
</script>

<?php include Kohana::find_file('views', 'floorplan/zoom_script'); ?>

<script>
$(document).ready(function() {
    FloorplanZoom.init(window.floorplanId);
    
    <?php if (isset($highlightData) && $highlightData): ?>
    setTimeout(function() {
        var $point = $('.floorplan-point.highlighted');
        if ($point.length) {
            var $container = $('#floorplanScrollable');
            var containerWidth = $container.width();
            var containerHeight = $container.height();
            var pointLeft = $point.position().left;
            var pointTop = $point.position().top;
            var scrollLeft = pointLeft - containerWidth / 2;
            var scrollTop = pointTop - containerHeight / 2;
            
            $container.animate({
                scrollLeft: scrollLeft,
                scrollTop: scrollTop
            }, 500);
        }
    }, 600);
    <?php endif; ?>
});

// ==========================================
// УДАЛЕНИЕ ТОЧЕК
// ==========================================

function deletePoint(pointId, btn) {
    if (!confirm('Удалить точку?')) return;
    
    var $btn = $(btn);
    var originalHtml = $btn.html();
    $btn.html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span>')
        .prop('disabled', true);
    
    $.ajax({
        url: '<?php echo URL::site("floorplan/deletePointAjax"); ?>',
        type: 'POST',
        data: { point_id: pointId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Точка удалена!');
                location.reload();
            } else {
                alert('Ошибка: ' + (response.error || 'Неизвестная ошибка'));
                $btn.html(originalHtml).prop('disabled', false);
            }
        },
        error: function() {
            alert('Ошибка соединения');
            $btn.html(originalHtml).prop('disabled', false);
        }
    });
}

// ==========================================
// РИСОВАНИЕ СВЯЗЕЙ МЕЖДУ УСТРОЙСТВАМИ
// ==========================================

<?php if (!empty($allHighlightPoints) && count($allHighlightPoints) > 1): ?>
$(document).ready(function() {
    var $img = $('#floorplanImage');
    
    $img.on('load', function() {
        drawConnections();
    });
    
    if ($img[0].complete) {
        drawConnections();
    }
    
    function drawConnections() {
        var $canvas = $('#floorplanCanvas');
        var canvasWidth = $canvas.width();
        var canvasHeight = $canvas.height();
        
        var $svg = $('#connectionsSvg');
        if (!$svg.length) {
            $svg = $('<svg id="connectionsSvg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 5;"></svg>');
            $canvas.append($svg);
        }
        
        $svg.empty();
        
        var points = [];
        <?php foreach ($allHighlightPoints as $point): ?>
            <?php 
            $isHighlighted = ($highlightData && $point['id_point'] == $highlightData['id_point']);
            ?>
            points.push({
                id: <?php echo $point['id_point']; ?>,
                x: <?php echo $point['x_pos']; ?>,
                y: <?php echo $point['y_pos']; ?>,
                isHighlighted: <?php echo $isHighlighted ? 'true' : 'false'; ?>
            });
        <?php endforeach; ?>
        
        if (points.length < 2) return;
        
        points.sort(function(a, b) { return a.x - b.x; });
        
        var color = '#ff9800';
        var highlightColor = '#ff5722';
        
        for (var i = 0; i < points.length - 1; i++) {
            var p1 = points[i];
            var p2 = points[i + 1];
            
            var isHighlighted = p1.isHighlighted || p2.isHighlighted;
            var strokeColor = isHighlighted ? highlightColor : color;
            var strokeWidth = isHighlighted ? 3 : 2;
            
            var x1 = (p1.x / 100) * canvasWidth;
            var y1 = (p1.y / 100) * canvasHeight;
            var x2 = (p2.x / 100) * canvasWidth;
            var y2 = (p2.y / 100) * canvasHeight;
            
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
            line.setAttribute('stroke', strokeColor);
            line.setAttribute('stroke-width', strokeWidth);
            line.setAttribute('stroke-dasharray', isHighlighted ? 'none' : '5,5');
            line.setAttribute('opacity', isHighlighted ? '1' : '0.7');
            
            if (isHighlighted) {
                line.setAttribute('style', 'animation: dash 1.5s ease-in-out infinite;');
            }
            
            $svg[0].appendChild(line);
            
            addDot($svg[0], x1, y1, isHighlighted);
            addDot($svg[0], x2, y2, isHighlighted);
        }
        
        function addDot(svg, x, y, isHighlighted) {
            var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', x);
            circle.setAttribute('cy', y);
            circle.setAttribute('r', isHighlighted ? 6 : 4);
            circle.setAttribute('fill', isHighlighted ? highlightColor : color);
            circle.setAttribute('opacity', isHighlighted ? '1' : '0.6');
            
            if (isHighlighted) {
                circle.setAttribute('style', 'animation: pulse-dot 1.5s ease-in-out infinite;');
            }
            
            svg.appendChild(circle);
        }
    }
    
    $(window).on('resize', function() {
        clearTimeout(window.resizeTimer);
        window.resizeTimer = setTimeout(function() {
            drawConnections();
        }, 300);
    });
    
    var originalApplyZoom = FloorplanZoom.applyZoom;
    FloorplanZoom.applyZoom = function() {
        originalApplyZoom.call(this);
        setTimeout(drawConnections, 100);
    };
    
    $('<style>')
        .text(
            '@keyframes dash {\n' +
            '    to {\n' +
            '        stroke-dashoffset: -20;\n' +
            '    }\n' +
            '}\n' +
            '@keyframes pulse-dot {\n' +
            '    0%, 100% {\n' +
            '        r: 6;\n' +
            '        opacity: 1;\n' +
            '    }\n' +
            '    50% {\n' +
            '        r: 10;\n' +
            '        opacity: 0.7;\n' +
            '    }\n' +
            '}'
        )
        .appendTo('head');
});
<?php endif; ?>
</script>