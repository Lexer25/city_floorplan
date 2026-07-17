<!-- Форма обновления плана -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <form method="POST" action="<?php echo URL::site('floorplan/edit/' . $main_floor_id); ?>" 
              class="form-inline" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_plan">
            <div class="form-group">
                <label>Название: </label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($current_floor['name']); ?>" class="form-control" style="width: 200px;">
            </div>
            <div class="form-group">
                <label>Описание: </label>
                <input type="text" name="description" value="<?php echo htmlspecialchars($current_floor['description']); ?>" class="form-control" style="width: 250px;">
            </div>
            <div class="form-group" style="margin-left: 10px;">
                <label>Новое изображение: </label>
                <input type="file" name="image" class="form-control" style="display: inline-block; width: auto;" accept="image/*">
                <small class="text-muted">(оставьте пустым, чтобы сохранить текущее)</small>
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
            <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">Назад</a>
            <button type="button" class="btn btn-info" onclick="printFloorplan()">
                <span class="glyphicon glyphicon-print"></span> Печать
            </button>
        </form>
    </div>
</div>