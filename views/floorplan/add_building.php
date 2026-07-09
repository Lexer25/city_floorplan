<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Добавление здания</h3>
    </div>
    <div class="panel-body">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo URL::site('floorplan/addBuilding'); ?>">
            <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                <label for="name">Название здания *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo isset($post['name']) ? htmlspecialchars($post['name']) : ''; ?>" 
                       required>
                <?php if (isset($errors['name'])): ?>
                    <span class="help-block"><?php echo $errors['name']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="address">Адрес</label>
                <input type="text" class="form-control" id="address" name="address" 
                       value="<?php echo isset($post['address']) ? htmlspecialchars($post['address']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="floors_count">Количество этажей</label>
                <input type="number" class="form-control" id="floors_count" name="floors_count" 
                       value="<?php echo isset($post['floors_count']) ? intval($post['floors_count']) : 1; ?>">
                <small class="text-muted">Укажите общее количество этажей в здании</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Добавить</button>
                <a href="<?php echo URL::site('floorplan/buildings'); ?>" class="btn btn-default">Отмена</a>
            </div>
        </form>

    </div>
</div>