<!-- ========================================== -->
<!-- БОКОВАЯ ПАНЕЛЬ С УСТРОЙСТВАМИ              -->
<!-- ========================================== -->
<div id="devicePanelWrapper" style="position: fixed; right: 20px; top: 80px; z-index: 9999; display: flex; align-items: flex-start; cursor: default;">
    
    <!-- Якорь для сворачивания/разворачивания -->
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
        box-shadow: -2px 0 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        flex-shrink: 0;
        z-index: 10001;
        margin-right: 0;
        margin-top: 40px;
    ">
        <span id="anchorIcon" class="glyphicon glyphicon-chevron-right" style="font-size: 14px;"></span>
    </div>
    
    <!-- Основная панель -->
    <div id="devicePanel" style="
        width: 290px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px 0 4px 4px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.15);
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        transform: translateX(0);
        opacity: 1;
        margin-right: -1px;
        cursor: default;
        position: relative;
    ">
        <!-- Заголовок с drag-ручкой и кнопкой закрытия -->
        <div id="panelDragHandle" style="
            background: #337ab7; 
            color: #fff; 
            padding: 8px 12px; 
            border-radius: 4px 4px 0 0; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-shrink: 0;
            cursor: move;
            user-select: none;
        ">
            <strong style="font-size: 13px;">
                <span class="glyphicon glyphicon-list"></span> Доступные устройства
            </strong>
            <span style="font-size: 11px; opacity: 0.7; display: flex; align-items: center; gap: 8px;">
                <span id="totalDevicesCount"><?php echo count($readers) + count($controllers); ?></span>
                <span style="color: #999; font-size: 10px; margin-left: 3px;">(свободны)</span>
                <span style="font-size: 10px; opacity: 0.5; cursor: move;">⠿</span>
                <button type="button" onclick="toggleDevicePanel()" style="
                    background: transparent;
                    border: none;
                    color: #fff;
                    padding: 0 4px;
                    font-size: 16px;
                    cursor: pointer;
                    opacity: 0.7;
                    transition: opacity 0.2s;
                " onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            </span>
        </div>
        
        <!-- Поле поиска/фильтрации -->
        <div style="padding: 6px 10px; background: #f8f9fa; border-bottom: 1px solid #e7e7e7; flex-shrink: 0;">
            <div style="display: flex; gap: 4px; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <span class="glyphicon glyphicon-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #999;"></span>
                    <input type="text" id="deviceFilterInput" 
                           placeholder="Поиск устройств..." 
                           style="width: 100%; padding: 4px 8px 4px 28px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px; outline: none; transition: border-color 0.3s;"
                           onfocus="this.style.borderColor='#337ab7'"
                           onblur="this.style.borderColor='#ddd'">
                </div>
                <button type="button" id="clearDeviceFilter" 
                        style="display: none; padding: 2px 6px; border: none; background: #d9534f; color: #fff; border-radius: 3px; font-size: 11px; cursor: pointer;"
                        title="Сбросить фильтр">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            </div>
            <div style="display: flex; gap: 4px; margin-top: 4px; flex-wrap: wrap;">
                <button type="button" class="filter-type-btn active" data-filter="all" 
                        style="padding: 1px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 10px; cursor: pointer; background: #337ab7; color: #fff;">
                    Все (<span id="filterAllCount"><?php echo count($readers) + count($controllers); ?></span>)
                </button>
                <button type="button" class="filter-type-btn" data-filter="reader" 
                        style="padding: 1px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 10px; cursor: pointer; background: #fff; color: #333;">
                    <span style="color: #5bc0de;">●</span> Считыватели (<span id="filterReaderCount"><?php echo count($readers); ?></span>)
                </button>
                <button type="button" class="filter-type-btn" data-filter="controller" 
                        style="padding: 1px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 10px; cursor: pointer; background: #fff; color: #333;">
                    <span style="color: #f0ad4e;">●</span> Контроллеры (<span id="filterControllerCount"><?php echo count($controllers); ?></span>)
                </button>
            </div>
        </div>
        
        <!-- Подсказка -->
        <div style="padding: 4px 10px; background: #f0f8ff; border-bottom: 1px solid #d9edf7; font-size: 10px; color: #31708f; flex-shrink: 0;">
            <span class="glyphicon glyphicon-info-sign"></span>
            Перетащите устройство на план, чтобы добавить
        </div>
        
        <!-- Вкладки -->
        <ul class="nav nav-tabs" style="padding: 0 5px; flex-shrink: 0;">
            <li class="active" style="width: 50%; text-align: center;">
                <a href="#tabReaders" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-qrcode"></span> Считыватели (<span id="readerTabCount"><?php echo count($readers); ?></span>)
                </a>
            </li>
            <li style="width: 50%; text-align: center;">
                <a href="#tabControllers" data-toggle="tab" style="padding: 6px 10px; font-size: 12px;">
                    <span class="glyphicon glyphicon-cog"></span> Контроллеры (<span id="controllerTabCount"><?php echo count($controllers); ?></span>)
                </a>
            </li>
        </ul>
        
        <!-- Содержимое вкладок с прокруткой -->
        <div class="tab-content" style="flex: 1; overflow: hidden; padding: 5px; min-height: 0;">
            <!-- Считыватели -->
            <div class="tab-pane active" id="tabReaders" style="height: 100%; overflow-y: auto; padding: 5px; max-height: 35vh;">
                <div id="readersList">
                    <?php if (!empty($readers)): ?>
                        <?php 
                        $iconPath = URL::base() . 'media/floorplan/icons/';
                        foreach ($readers as $device): 
                        ?>
                            <div class="device-item reader-item draggable-device" 
                                 data-device-id="<?php echo $device['id_dev']; ?>"
                                 data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                                 data-device-type="reader"
                                 data-device-search="<?php echo strtolower(htmlspecialchars($device['name'] . ' ' . $device['id_dev'])); ?>"
                                 style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: grab; border-left: 3px solid #5bc0de; font-size: 12px; transition: all 0.2s ease; user-select: none;"
                                 title="Перетащите на план">
                                <img src="<?php echo $iconPath; ?>reader.svg" 
                                     style="width: 16px; height: 16px; margin-right: 5px; vertical-align: middle;"
                                     alt="Считыватель">
                                <?php echo htmlspecialchars($device['name']); ?>
                                <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div id="noReadersMsg" style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                            <span class="glyphicon glyphicon-info-sign"></span><br>
                            Нет свободных считывателей
                        </div>
                    <?php endif; ?>
                </div>
                <div id="readersEmptyFilter" style="display: none; padding: 15px; text-align: center; color: #999; font-size: 12px;">
                    <span class="glyphicon glyphicon-search"></span><br>
                    Считыватели не найдены
                </div>
            </div>
            
            <!-- Контроллеры -->
            <div class="tab-pane" id="tabControllers" style="height: 100%; overflow-y: auto; padding: 5px; max-height: 35vh;">
                <div id="controllersList">
                    <?php if (!empty($controllers)): ?>
                        <?php 
                        $iconPath = URL::base() . 'media/floorplan/icons/';
                        foreach ($controllers as $device): 
                        ?>
                            <div class="device-item controller-item draggable-device" 
                                 data-device-id="<?php echo $device['id_dev']; ?>"
                                 data-device-name="<?php echo htmlspecialchars($device['name']); ?>"
                                 data-device-type="controller"
                                 data-device-search="<?php echo strtolower(htmlspecialchars($device['name'] . ' ' . $device['id_dev'])); ?>"
                                 style="padding: 5px 8px; margin: 2px 0; background: #f9f9f9; border-radius: 3px; cursor: grab; border-left: 3px solid #f0ad4e; font-size: 12px; transition: all 0.2s ease; user-select: none;"
                                 title="Перетащите на план">
                                <img src="<?php echo $iconPath; ?>controller.svg" 
                                     style="width: 16px; height: 16px; margin-right: 5px; vertical-align: middle;"
                                     alt="Контроллер">
                                <?php echo htmlspecialchars($device['name']); ?>
                                <span style="color: #999; font-size: 10px;">(id=<?php echo $device['id_dev']; ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div id="noControllersMsg" style="padding: 15px; text-align: center; color: #999; font-size: 12px;">
                            <span class="glyphicon glyphicon-info-sign"></span><br>
                            Нет свободных контроллеров
                        </div>
                    <?php endif; ?>
                </div>
                <div id="controllersEmptyFilter" style="display: none; padding: 15px; text-align: center; color: #999; font-size: 12px;">
                    <span class="glyphicon glyphicon-search"></span><br>
                    Контроллеры не найдены
                </div>
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