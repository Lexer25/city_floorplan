<!-- ========================================== -->
<!-- БОКОВАЯ ПАНЕЛЬ С УСТРОЙСТВАМИ              -->
<!-- ========================================== -->
<div id="devicePanelWrapper" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); z-index: 100; display: flex; align-items: center;">
    
    <div id="panelAnchor" onclick="toggleDevicePanel()" style="
        width: 32px;
        height: 80px;
        background: #337ab7;
        border-radius: 4px 0 0 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        flex-shrink: 0;
        z-index: 101;
        margin-right: 0;
    ">
        <span id="anchorIcon" class="glyphicon glyphicon-chevron-right" style="font-size: 14px;"></span>
    </div>
    
    <div id="devicePanel" style="
        width: 280px;
        background: #fff;
        border: 1px solid #ddd;
        border-right: none;
        border-radius: 4px 0 0 4px;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform: translateX(0);
        margin-right: -1px;
    ">
        <div style="background: #337ab7; color: #fff; padding: 8px 12px; border-radius: 4px 0 0 0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <strong style="font-size: 13px;">
                <span class="glyphicon glyphicon-list"></span> Доступные устройства
            </strong>
            <span style="font-size: 11px; opacity: 0.7;">
                <?php echo count($readers) + count($controllers); ?>
                <span style="color: #999; font-size: 10px; margin-left: 3px;">(свободны)</span>
            </span>
        </div>
        
        <!-- Подсказка -->
        <div style="padding: 5px 10px; background: #f0f8ff; border-bottom: 1px solid #d9edf7; font-size: 11px; color: #31708f;">
            <span class="glyphicon glyphicon-info-sign"></span>
            Перетащите устройство на план, чтобы добавить
        </div>
        
        <!-- Вкладки -->
        <ul class="nav nav-tabs" style="padding: 0 5px; flex-shrink: 0;">
            <li class="active" style="width: 50%; text-align: center;">
                <a href="#tabReaders" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-qrcode"></span> Считыватели (<?php echo count($readers); ?>)
                </a>
            </li>
            <li style="width: 50%; text-align: center;">
                <a href="#tabControllers" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-cog"></span> Контроллеры (<?php echo count($controllers); ?>)
                </a>
            </li>
        </ul>
        
        <!-- Содержимое вкладок -->
        <div class="tab-content" style="flex: 1; overflow: hidden; padding: 5px;">
            <!-- Считыватели -->
            <div class="tab-pane active" id="tabReaders" style="height: 100%; overflow-y: auto; padding: 5px;">
                <?php if (!empty($readers)): ?>
                    <?php foreach ($readers as $device): ?>
                        <div class="device-item reader-item draggable-device" 
                             data-device-id="<?php echo $device['id_dev']; ?>"
                             data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                             data-device-type="reader"
                             style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: grab; border-left: 3px solid #5bc0de; font-size: 12px; transition: all 0.2s ease; user-select: none;"
                             title="Перетащите на план">
                            <span class="glyphicon glyphicon-qrcode text-info" style="margin-right: 5px;"></span>
                            <?php echo htmlspecialchars($device['name']); ?>
                            <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                        <span class="glyphicon glyphicon-info-sign"></span><br>
                        Нет свободных считывателей
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Контроллеры -->
            <div class="tab-pane" id="tabControllers" style="height: 100%; overflow-y: auto; padding: 5px;">
                <?php if (!empty($controllers)): ?>
                    <?php foreach ($controllers as $device): ?>
                        <div class="device-item controller-item draggable-device" 
                             data-device-id="<?php echo $device['id_dev']; ?>"
                             data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                             data-device-type="controller"
                             style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: grab; border-left: 3px solid #f0ad4e; font-size: 12px; transition: all 0.2s ease; user-select: none;"
                             title="Перетащите на план">
                            <span class="glyphicon glyphicon-cog text-warning" style="margin-right: 5px;"></span>
                            <?php echo htmlspecialchars($device['name']); ?>
                            <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                        <span class="glyphicon glyphicon-info-sign"></span><br>
                        Нет свободных контроллеров
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div id="devicePanelFooter" style="padding: 5px 10px; background: #f5f5f5; border-top: 1px solid #ddd; font-size: 11px; color: #999; flex-shrink: 0;">
            <span id="selectedDeviceInfo">Перетащите устройство на план</span>
            <span style="display: block; font-size: 9px; color: #ccc; margin-top: 2px;">
                <span class="glyphicon glyphicon-hand-up"></span> drag &amp; drop
            </span>
        </div>
    </div>
</div>