<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Добавление плана (этажа)</h3>
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

        <form method="POST" action="<?php echo URL::site('floorplan/add'); ?>" enctype="multipart/form-data">
            <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                <label for="name">Название плана *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo isset($post['name']) ? htmlspecialchars($post['name']) : ''; ?>" 
                       required>
                <?php if (isset($errors['name'])): ?>
                    <span class="help-block"><?php echo $errors['name']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($post['description']) ? htmlspecialchars($post['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="building_id">Здание</label>
                <select class="form-control" id="building_id" name="building_id">
                    <?php foreach ($buildings as $building): ?>
                        <option value="<?php echo $building['id_building']; ?>" 
                                <?php echo (isset($post['building_id']) && $post['building_id'] == $building['id_building']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($building['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="floor_number">Номер этажа *</label>
                        <input type="number" class="form-control" id="floor_number" name="floor_number" 
                               value="<?php echo isset($post['floor_number']) ? intval($post['floor_number']) : 1; ?>" 
                               required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="floor_name">Название этажа</label>
                        <input type="text" class="form-control" id="floor_name" name="floor_name" 
                               value="<?php echo isset($post['floor_name']) ? htmlspecialchars($post['floor_name']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="form-group <?php echo isset($errors['image']) ? 'has-error' : ''; ?>">
                <label for="image">Изображение плана *</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                <small class="text-muted">Поддерживаются форматы: JPG, PNG, GIF</small>
                <?php if (isset($errors['image'])): ?>
                    <span class="help-block"><?php echo $errors['image']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Добавить</button>
                <a href="<?php echo URL::site('floorplan'); ?>" class="btn btn-default">Отмена</a>
            </div>
        </form>

    </div>
</div>