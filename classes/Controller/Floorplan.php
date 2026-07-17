<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Floorplan extends Controller_Template
{
    public $template = 'template';
    protected $db_ready = false;
    
    public function before()
    {
        parent::before();
        
        $this->is_admin = Auth::instance()->logged_in('admin');
        View::bind_global('is_admin', $this->is_admin);
        
        $this->checkDatabaseTables();
    }
    
    private function checkDatabaseTables()
    {
        try {
            $installModel = Model::factory('Floorplan_Installm');
            $result = $installModel->checkDatabase();
            
            if (!$result['all_ok']) {
                $this->db_ready = false;
                View::bind_global('db_check_result', $this->db_ready);
                View::bind_global('db_ready', $this->db_ready);
                
                $current_action = $this->request->action();
                $current_controller = $this->request->controller();
                
                if ($current_controller !== 'Floorplan_Install' && $current_action !== 'install') {
                    Session::instance()->set('db_error', 'База данных модуля "Планы объекта" не установлена. Требуется установка.');
                    Session::instance()->set('message_type', 'danger');
                }
            } else {
                $this->db_ready = true;
                View::bind_global('db_ready', $this->db_ready);
            }
        } catch (Exception $e) {
            $this->db_ready = false;
            View::bind_global('db_ready', $this->db_ready);
            Kohana::$log->add(Log::ERROR, 'Database check error: ' . $e->getMessage());
        }
    }


/**
 * Поиск устройства по id_dev с подсветкой связанных
 */
/**
 * Поиск устройства по id_dev с подсветкой связанных
 */
public function action_findDevice()
{
    // Получаем параметр id_dev
    $id_dev = $this->request->query('id_dev');
    
    if (empty($id_dev)) {
        $this->redirect('floorplan');
        return;
    }
    
    $id_dev = (int)$id_dev;
    $model = Model::factory('Floorplanm');
    
    $result = $model->findDeviceWithRelated($id_dev);
    
    if ($result && isset($result['device'])) {
        $device = $result['device'];
        $related = $result['related'];
        $id_ctrl = $result['id_ctrl'];
        
        $highlightIds = array();
        $relatedIds = array();
        
        foreach ($related as $point) {
            if ($point['id_point'] == $device['id_point']) {
                $highlightIds[] = $point['id_point'];
            } else {
                $relatedIds[] = $point['id_point'];
            }
        }
        
        if (empty($highlightIds)) {
            $highlightIds[] = $device['id_point'];
        }
        
        $params = array(
            'highlight' => implode(',', $highlightIds),
            'related' => implode(',', $relatedIds),
            'id_ctrl' => $id_ctrl,
            'id_dev' => $id_dev
        );
        
        $url = 'floorplan/view/' . $device['id_floorplan'] . '?' . http_build_query($params);
        $this->redirect($url);
    } else {
        Session::instance()->set('message', 'Устройство с ID ' . $id_dev . ' не найдено ни на одном плане');
        Session::instance()->set('message_type', 'warning');
        $this->redirect('floorplan');
    }
}


    /**
     * AJAX: Проверка, занято ли устройство
     */
    public function action_checkDeviceUsed()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');
        
        $deviceId = (int)$this->request->post('device_id');
        $floorplanId = (int)$this->request->post('floorplan_id', 0);
        
        if (!$deviceId) {
            echo json_encode(array('success' => false, 'error' => 'No device ID'));
            return;
        }
        
        $model = Model::factory('Floorplanm');
        $isUsed = $model->isDeviceUsed($deviceId, $floorplanId);
        
        echo json_encode(array(
            'success' => true,
            'is_used' => $isUsed,
            'device_id' => $deviceId
        ));
    }

    public function action_index()
    {
        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }
        
        $model = Model::factory('Floorplanm');
        $floorplans = $model->getFloorplans();
        $buildings = $model->getBuildings();

        $content = View::factory('floorplan/index', array(
            'floorplans' => $floorplans,
            'buildings' => $buildings,
            'is_admin' => $this->is_admin,
            'db_ready' => $this->db_ready,
        ));

        $this->template->content = $content;
    }

    /**
     * Просмотр плана
     */
public function action_view()
{
    if (!$this->db_ready) {
        $content = $this->getDbNotReadyView();
        $this->template->content = $content;
        return;
    }
    
    $id = (int)$this->request->param('id', 0);
    $model = Model::factory('Floorplanm');

    // Если id не указан, проверяем параметры highlight/related
    if (!$id) {
        $highlight = $this->request->query('highlight');
        $related = $this->request->query('related');
        
        if ($highlight) {
            // Берем первый ID из списка
            $ids = explode(',', $highlight);
            $firstId = (int)$ids[0];
            
            if ($firstId) {
                $sql = "SELECT id_floorplan FROM floorplan_point WHERE id_point = " . intval($firstId);
                $result = DB::query(Database::SELECT, $sql)
                    ->execute(Database::instance('fb'))
                    ->as_array();
                if (!empty($result)) {
                    $id = $result[0]['ID_FLOORPLAN'];
                }
            }
        }
    }

    if (!$id || !$model->floorplanExists($id)) {
        $this->redirect('floorplan');
    }

    $floorplan = $model->getFloorplanById($id);
    $points = $model->getPointsByFloorplan($id);
    $building = $model->getBuildingById($floorplan['id_building']);
    $floors = $model->getFloorsByBuilding($floorplan['id_building']);

    $deviceStatuses = $this->getDeviceStatuses($points);

    // Обработка подсветки
    $highlightIds = array();
    $relatedIds = array();
    $highlightData = null;
    $relatedData = array();
    $id_ctrl = $this->request->query('id_ctrl');
    $id_dev = $this->request->query('id_dev');
    
    // Получаем ID для подсветки
    $highlightParam = $this->request->query('highlight');
    if ($highlightParam) {
        $highlightIds = array_map('intval', explode(',', $highlightParam));
    }
    
    $relatedParam = $this->request->query('related');
    if ($relatedParam) {
        $relatedIds = array_map('intval', explode(',', $relatedParam));
    }
    
    // Собираем данные о точках для подсветки
    foreach ($points as $point) {
        if (in_array($point['id_point'], $highlightIds)) {
            $highlightData = $point;
        }
        if (in_array($point['id_point'], $relatedIds)) {
            $relatedData[] = $point;
        }
    }
    
    // Если highlight не передан, но есть id_dev - ищем точку
    if (!$highlightData && $id_dev) {
        foreach ($points as $point) {
            if ($point['id_dev'] == $id_dev) {
                $highlightData = $point;
                break;
            }
        }
    }

    // Собираем все ID для построения связей
    $allHighlightIds = array_merge($highlightIds, $relatedIds);
    $allHighlightPoints = array();
    foreach ($points as $point) {
        if (in_array($point['id_point'], $allHighlightIds)) {
            $allHighlightPoints[] = $point;
        }
    }

    $content = View::factory('floorplan/view', array(
        'floorplan' => $floorplan,
        'points' => $points,
        'floors' => $floors,
        'building' => $building,
        'deviceStatuses' => $deviceStatuses,
        'is_admin' => $this->is_admin,
        'db_ready' => $this->db_ready,
        'highlightData' => $highlightData,
        'relatedData' => $relatedData,
        'highlightIds' => $highlightIds,
        'relatedIds' => $relatedIds,
        'allHighlightPoints' => $allHighlightPoints,
        'searchIdDev' => $id_dev,
        'id_ctrl' => $id_ctrl,
    ));

    $this->set_full_width(true);
    $this->template->content = $content;
}

    /**
     * Редактирование плана
     */
    public function action_edit()
    {
        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }
        
        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$id || !$model->floorplanExists($id)) {
            $this->redirect('floorplan');
        }

        $floorplan = $model->getFloorplanById($id);
        $building = $model->getBuildingById($floorplan['id_building']);
        $floors = $model->getFloorsByBuilding($floorplan['id_building']);

        $floorParam = $this->request->query('floor');
        if ($floorParam !== null && $floorParam !== '') {
            $currentFloorId = (int)$floorParam;
        } else {
            $currentFloorId = $id;
        }
        
        $currentFloor = $model->getFloorplanById($currentFloorId);
        if (!$currentFloor) {
            $currentFloor = $floorplan;
            $currentFloorId = $id;
        }
        
        $points = $model->getPointsByFloorplan($currentFloorId);

        // Подсветка точки
        $highlight = $this->request->query('highlight');
        $highlightData = null;
        $id_dev = $this->request->query('id_dev');
        
        if ($highlight) {
            foreach ($points as $point) {
                if ($point['id_point'] == $highlight) {
                    $highlightData = $point;
                    break;
                }
            }
        } elseif ($id_dev) {
            foreach ($points as $point) {
                if ($point['id_dev'] == $id_dev) {
                    $highlightData = $point;
                    break;
                }
            }
        }

        // Получаем устройства
        $readers = $model->getAvailableReaders($currentFloorId);
        $controllers = $model->getAvailableControllers($currentFloorId);
        $allDevices = $model->getAllDevicesWithStatus($currentFloorId);

        // Обработка POST запроса
        if ($this->request->method() == HTTP_Request::POST) {
            $post = $this->request->post();
            $action = Arr::get($post, 'action');

            if ($action == 'update_plan') {
                $name = Arr::get($post, 'name');
                $description = Arr::get($post, 'description');
                $width = Arr::get($post, 'width', 800);
                $height = Arr::get($post, 'height', 600);
                
                $image = $currentFloor['image'];
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    try {
                        $newImage = $model->uploadFloorplanImage($_FILES['image']);
                        
                        if (!empty($currentFloor['image'])) {
                            $model->deleteFloorplanImage($currentFloor['image']);
                        }
                        
                        $image = $newImage;
                        $fullPath = DOCROOT . $image;
                        $imageInfo = getimagesize($fullPath);
                        if ($imageInfo !== false) {
                            $width = $imageInfo[0];
                            $height = $imageInfo[1];
                        }
                    } catch (Exception $e) {
                        Session::instance()->set('message', 'Ошибка загрузки изображения: ' . $e->getMessage());
                        Session::instance()->set('message_type', 'danger');
                        $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
                    }
                }

                $result = $model->updateFloorplan(
                    $currentFloorId, 
                    $name, 
                    $description, 
                    $image, 
                    $width, 
                    $height
                );

                if ($result) {
                    Session::instance()->set('message', 'План успешно обновлен');
                    Session::instance()->set('message_type', 'success');
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }

            // Добавление считывателя
            if ($action == 'add_reader') {
                $x = Arr::get($post, 'x', 0);
                $y = Arr::get($post, 'y', 0);
                $deviceId = Arr::get($post, 'device_id', 0);
                $label = Arr::get($post, 'label', '');
                
                if ($deviceId > 0) {
                    if ($model->isDeviceUsed($deviceId, $currentFloorId)) {
                        Session::instance()->set('message', 'Это устройство уже используется на другом плане');
                        Session::instance()->set('message_type', 'danger');
                    } else {
                        $result = $model->addPoint($currentFloorId, $x, $y, $deviceId, 'reader', $label);
                        if ($result) {
                            Session::instance()->set('message', 'Считыватель добавлен');
                            Session::instance()->set('message_type', 'success');
                        }
                    }
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }

            // Добавление контроллера
            if ($action == 'add_controller') {
                $x = Arr::get($post, 'x', 0);
                $y = Arr::get($post, 'y', 0);
                $deviceId = Arr::get($post, 'device_id', 0);
                $label = Arr::get($post, 'label', '');
                
                if ($deviceId > 0) {
                    if ($model->isDeviceUsed($deviceId, $currentFloorId)) {
                        Session::instance()->set('message', 'Это устройство уже используется на другом плане');
                        Session::instance()->set('message_type', 'danger');
                    } else {
                        $result = $model->addPoint($currentFloorId, $x, $y, $deviceId, 'controller', $label);
                        if ($result) {
                            Session::instance()->set('message', 'Контроллер добавлен');
                            Session::instance()->set('message_type', 'success');
                        }
                    }
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }

            if ($action == 'delete_point') {
                $pointId = Arr::get($post, 'point_id', 0);
                if ($pointId > 0) {
                    $model->deletePoint($pointId);
                    Session::instance()->set('message', 'Точка удалена');
                    Session::instance()->set('message_type', 'success');
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }

            if ($action == 'copy_floor') {
                $newFloorNumber = (int)Arr::get($post, 'new_floor_number', 0);
                $newFloorName = Arr::get($post, 'new_floor_name', '');

                if ($newFloorNumber > 0 && !$model->floorExists($floorplan['id_building'], $newFloorNumber)) {
                    $newId = $model->copyFloorplan($currentFloorId, $newFloorNumber, $newFloorName);
                    if ($newId) {
                        Session::instance()->set('message', 'Этаж скопирован успешно');
                        Session::instance()->set('message_type', 'success');
                        $this->redirect('floorplan/edit/' . $id . '?floor=' . $newId);
                    }
                } else {
                    Session::instance()->set('message', 'Ошибка при копировании этажа');
                    Session::instance()->set('message_type', 'danger');
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }

            if ($action == 'delete_floor') {
                $deleteFloorId = (int)Arr::get($post, 'delete_floor_id', 0);
                if ($deleteFloorId && $deleteFloorId != $id) {
                    $floorToDelete = $model->getFloorplanById($deleteFloorId);
                    if ($floorToDelete && !empty($floorToDelete['image'])) {
                        $model->deleteFloorplanImage($floorToDelete['image']);
                    }
                    $model->deleteFloorplan($deleteFloorId);
                    Session::instance()->set('message', 'Этаж удален');
                    Session::instance()->set('message_type', 'success');
                } else {
                    Session::instance()->set('message', 'Нельзя удалить основной этаж');
                    Session::instance()->set('message_type', 'danger');
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }
            
            if ($action == 'add_floor') {
                $newFloorNumber = (int)Arr::get($post, 'new_floor_number', 0);
                $newFloorName = Arr::get($post, 'new_floor_name', '');
                
                $image = '';
                $errors = array();
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    try {
                        $image = $model->uploadFloorplanImage($_FILES['image']);
                    } catch (Exception $e) {
                        $errors['image'] = $e->getMessage();
                    }
                } else {
                    $errors['image'] = 'Изображение обязательно';
                }
                
                if (empty($errors) && $newFloorNumber > 0 && !$model->floorExists($floorplan['id_building'], $newFloorNumber)) {
                    $fullPath = DOCROOT . $image;
                    $imageInfo = getimagesize($fullPath);
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    
                    $newId = $model->addFloorplan(
                        'Этаж ' . $newFloorNumber,
                        '',
                        $image,
                        $width,
                        $height,
                        $floorplan['id_building'],
                        $newFloorNumber,
                        $newFloorName ?: $newFloorNumber . ' этаж'
                    );
                    
                    if ($newId) {
                        Session::instance()->set('message', 'Этаж добавлен успешно');
                        Session::instance()->set('message_type', 'success');
                        $this->redirect('floorplan/edit/' . $id . '?floor=' . $newId);
                    }
                } else {
                    Session::instance()->set('message', 'Ошибка при добавлении этажа: ' . implode(', ', $errors));
                    Session::instance()->set('message_type', 'danger');
                }
                $this->redirect('floorplan/edit/' . $id . '?floor=' . $currentFloorId);
            }
        }

        $deviceStatuses = $this->getDeviceStatuses($points);

        $content = View::factory('floorplan/edit', array(
            'floorplan' => $floorplan,
            'current_floor' => $currentFloor,
            'floors' => $floors,
            'building' => $building,
            'points' => $points,
            'deviceStatuses' => $deviceStatuses,
            'readers' => $readers,
            'controllers' => $controllers,
            'allDevices' => $allDevices,
            'is_admin' => $this->is_admin,
            'mode' => 'edit',
            'current_floor_id' => $currentFloorId,
            'main_floor_id' => $id,
            'db_ready' => $this->db_ready,
            'highlightData' => $highlightData,
            'searchIdDev' => $id_dev,
        ));

        $this->set_full_width(true);
        $this->template->content = $content;
    }

    /**
     * AJAX: Сохранение позиций точек
     */
    public function action_savePositions()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        if (!$this->is_admin) {
            echo json_encode(array('success' => false, 'error' => 'Доступ запрещён'));
            return;
        }

        if (!$this->db_ready) {
            echo json_encode(array('success' => false, 'error' => 'База данных не установлена'));
            return;
        }

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        $points = isset($data['points']) ? $data['points'] : array();

        if (empty($points)) {
            echo json_encode(array('success' => true, 'message' => 'Нет изменений'));
            return;
        }

        $validatedPoints = array();
        foreach ($points as $point) {
            $id = isset($point['id']) ? (int)$point['id'] : 0;
            $x = isset($point['x']) ? (float)$point['x'] : 0;
            $y = isset($point['y']) ? (float)$point['y'] : 0;
            
            $x = max(0, min(100, $x));
            $y = max(0, min(100, $y));
            
            if ($id > 0) {
                $validatedPoints[] = array(
                    'id' => $id,
                    'x' => $x,
                    'y' => $y
                );
            }
        }
        
        if (empty($validatedPoints)) {
            echo json_encode(array('success' => false, 'error' => 'Нет валидных точек'));
            return;
        }

        $model = Model::factory('Floorplanm');
        $result = $model->savePointsPositions($validatedPoints);

        if ($result) {
            echo json_encode(array(
                'success' => true, 
                'message' => 'Сохранено ' . count($validatedPoints) . ' точек',
                'count' => count($validatedPoints)
            ));
        } else {
            echo json_encode(array('success' => false, 'error' => 'Ошибка при сохранении'));
        }
    }

    /**
     * AJAX: Добавление точки
     */
    public function action_addPointAjax()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        if (!$this->is_admin) {
            echo json_encode(array('success' => false, 'error' => 'Доступ запрещён'));
            return;
        }

        if (!$this->db_ready) {
            echo json_encode(array('success' => false, 'error' => 'База данных не установлена'));
            return;
        }

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $post = $this->request->post();

        $floorplanId = isset($post['floorplan_id']) ? (int)$post['floorplan_id'] : 0;
        $x = isset($post['x']) ? (float)$post['x'] : 0;
        $y = isset($post['y']) ? (float)$post['y'] : 0;
        $deviceId = isset($post['device_id']) ? (int)$post['device_id'] : 0;
        $point_type = isset($post['point_type']) ? trim($post['point_type']) : 'reader';
        $label = isset($post['label']) ? trim($post['label']) : '';

        if ($floorplanId <= 0 || $deviceId <= 0) {
            echo json_encode(array('success' => false, 'error' => 'Invalid parameters'));
            return;
        }

        $model = Model::factory('Floorplanm');
        
        // Проверяем, не занято ли устройство
        if ($model->isDeviceUsed($deviceId, $floorplanId)) {
            echo json_encode(array('success' => false, 'error' => 'Это устройство уже используется на другом плане'));
            return;
        }
        
        $result = $model->addPoint($floorplanId, $x, $y, $deviceId, $point_type, $label);

        if ($result) {
            echo json_encode(array('success' => true, 'id' => $result));
        } else {
            echo json_encode(array('success' => false, 'error' => 'Ошибка при добавлении точки в БД'));
        }
    }

    /**
     * AJAX: Удаление точки
     */
    public function action_deletePointAjax()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        if (!$this->is_admin) {
            echo json_encode(array('success' => false, 'error' => 'Доступ запрещён'));
            return;
        }

        if (!$this->db_ready) {
            echo json_encode(array('success' => false, 'error' => 'База данных не установлена'));
            return;
        }

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $pointId = (int)$this->request->post('point_id');

        if ($pointId <= 0) {
            echo json_encode(array('success' => false, 'error' => 'Invalid point ID'));
            return;
        }

        $model = Model::factory('Floorplanm');
        $result = $model->deletePoint($pointId);

        echo json_encode(array('success' => $result));
    }

    /**
     * Добавление нового плана (этажа)
     */
    public function action_add()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $model = Model::factory('Floorplanm');
        $buildings = $model->getBuildings();

        if ($this->request->method() == HTTP_Request::POST) {
            $post = $this->request->post();

            $name = Arr::get($post, 'name');
            $description = Arr::get($post, 'description');
            $buildingId = (int)Arr::get($post, 'building_id', 1);
            $floorNumber = (int)Arr::get($post, 'floor_number', 1);
            $floorName = Arr::get($post, 'floor_name', '');

            $image = '';
            $errors = array();

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $model->uploadFloorplanImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors['image'] = $e->getMessage();
                }
            } else {
                $errors['image'] = 'Изображение обязательно';
            }

            if (empty($name)) {
                $errors['name'] = 'Название обязательно';
            }

            if (!empty($errors)) {
                $content = View::factory('floorplan/add', array(
                    'errors' => $errors,
                    'post' => $post,
                    'buildings' => $buildings,
                    'is_admin' => $this->is_admin,
                    'db_ready' => $this->db_ready,
                ));
                $this->template->content = $content;
                return;
            }

            $fullPath = DOCROOT . $image;
            $imageInfo = getimagesize($fullPath);
            if ($imageInfo !== false) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            } else {
                $width = 800;
                $height = 600;
            }

            $result = $model->addFloorplan(
                $name, 
                $description, 
                $image, 
                $width, 
                $height, 
                $buildingId, 
                $floorNumber, 
                $floorName
            );

            if ($result) {
                Session::instance()->set('message', 'План успешно добавлен');
                Session::instance()->set('message_type', 'success');
                $this->redirect('floorplan/edit/' . $result);
            } else {
                Session::instance()->set('message', 'Ошибка при добавлении плана');
                Session::instance()->set('message_type', 'danger');
            }
        }

        $content = View::factory('floorplan/add', array(
            'errors' => array(),
            'post' => array(),
            'buildings' => $buildings,
            'is_admin' => $this->is_admin,
            'db_ready' => $this->db_ready,
        ));

        $this->template->content = $content;
    }

    /**
     * Удаление плана
     */
    public function action_delete()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if ($id && $model->floorplanExists($id)) {
            $floorplan = $model->getFloorplanById($id);
            if ($floorplan && !empty($floorplan['image'])) {
                $model->deleteFloorplanImage($floorplan['image']);
            }
            
            $model->deleteFloorplan($id);
            Session::instance()->set('message', 'План удален');
            Session::instance()->set('message_type', 'success');
        }

        $this->redirect('floorplan');
    }

    /**
     * Управление зданиями - список
     */
    public function action_buildings()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $model = Model::factory('Floorplanm');
        $buildings = $model->getBuildings();

        foreach ($buildings as &$building) {
            $floors = $model->getFloorsByBuilding($building['id_building']);
            $building['floors_count_actual'] = count($floors);
        }

        $content = View::factory('floorplan/buildings', array(
            'buildings' => $buildings,
            'is_admin' => $this->is_admin,
            'db_ready' => $this->db_ready,
        ));

        $this->template->content = $content;
    }

    /**
     * Добавление здания
     */
    public function action_addBuilding()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $model = Model::factory('Floorplanm');

        if ($this->request->method() == HTTP_Request::POST) {
            $post = $this->request->post();

            $name = Arr::get($post, 'name');
            $address = Arr::get($post, 'address', '');
            $floorsCount = (int)Arr::get($post, 'floors_count', 1);

            $errors = array();
            if (empty($name)) {
                $errors['name'] = 'Название здания обязательно';
            }

            if (empty($errors)) {
                $result = $model->addBuilding($name, $address, $floorsCount);

                if ($result) {
                    Session::instance()->set('message', 'Здание успешно добавлено');
                    Session::instance()->set('message_type', 'success');
                    $this->redirect('floorplan/buildings');
                } else {
                    Session::instance()->set('message', 'Ошибка при добавлении здания');
                    Session::instance()->set('message_type', 'danger');
                }
            }

            $content = View::factory('floorplan/add_building', array(
                'errors' => $errors,
                'post' => $post,
                'is_admin' => $this->is_admin,
                'db_ready' => $this->db_ready,
            ));
            $this->template->content = $content;
            return;
        }

        $content = View::factory('floorplan/add_building', array(
            'errors' => array(),
            'post' => array(),
            'is_admin' => $this->is_admin,
            'db_ready' => $this->db_ready,
        ));

        $this->template->content = $content;
    }

    /**
     * Редактирование здания
     */
    public function action_editBuilding()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if (!$id || !$model->buildingExists($id)) {
            $this->redirect('floorplan/buildings');
        }

        $building = $model->getBuildingById($id);

        if ($this->request->method() == HTTP_Request::POST) {
            $post = $this->request->post();

            $name = Arr::get($post, 'name');
            $address = Arr::get($post, 'address', '');
            $floorsCount = (int)Arr::get($post, 'floors_count', 1);

            $errors = array();
            if (empty($name)) {
                $errors['name'] = 'Название здания обязательно';
            }

            if (empty($errors)) {
                $result = $model->updateBuilding($id, $name, $address, $floorsCount);

                if ($result) {
                    Session::instance()->set('message', 'Здание успешно обновлено');
                    Session::instance()->set('message_type', 'success');
                    $this->redirect('floorplan/buildings');
                } else {
                    Session::instance()->set('message', 'Ошибка при обновлении здания');
                    Session::instance()->set('message_type', 'danger');
                }
            }

            $content = View::factory('floorplan/edit_building', array(
                'building' => $building,
                'errors' => $errors,
                'post' => $post,
                'is_admin' => $this->is_admin,
                'db_ready' => $this->db_ready,
            ));
            $this->template->content = $content;
            return;
        }

        $content = View::factory('floorplan/edit_building', array(
            'building' => $building,
            'errors' => array(),
            'post' => array(),
            'is_admin' => $this->is_admin,
            'db_ready' => $this->db_ready,
        ));

        $this->template->content = $content;
    }

    /**
     * Удаление здания
     */
    public function action_deleteBuilding()
    {
        if (!$this->is_admin) {
            $this->redirect('floorplan');
        }

        if (!$this->db_ready) {
            $content = $this->getDbNotReadyView();
            $this->template->content = $content;
            return;
        }

        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if ($id && $model->buildingExists($id)) {
            $floors = $model->getFloorsByBuilding($id);
            foreach ($floors as $floor) {
                $floorData = $model->getFloorplanById($floor['id_floorplan']);
                if ($floorData && !empty($floorData['image'])) {
                    $model->deleteFloorplanImage($floorData['image']);
                }
            }
            
            $result = $model->deleteBuilding($id);
            if ($result['success']) {
                Session::instance()->set('message', 'Здание удалено');
                Session::instance()->set('message_type', 'success');
            } else {
                Session::instance()->set('message', $result['error']);
                Session::instance()->set('message_type', 'danger');
            }
        }

        $this->redirect('floorplan/buildings');
    }

    private function getDbNotReadyView()
    {
        $message = Session::instance()->get_once('db_error', 'База данных модуля "Планы объекта" не установлена. Перейдите в раздел установки.');
        $message_type = Session::instance()->get_once('message_type', 'danger');
        
        $content = '<div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-warning-sign"></span> 
                    Ошибка: База данных не установлена
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    <strong>' . $message . '</strong>
                </div>
                <p>Для работы модуля "Планы объекта" необходимо установить следующие таблицы:</p>
                <ul>
                    <li><code>BUILDING</code> - таблица зданий</li>
                    <li><code>FLOORPLAN</code> - таблица планов</li>
                    <li><code>FLOORPLAN_POINT</code> - таблица точек на планах</li>
                </ul>
                <p>Также необходимы соответствующие генераторы и триггеры.</p>
                <a href="' . URL::site('floorplan/install') . '" class="btn btn-primary">
                    <span class="glyphicon glyphicon-database"></span> Перейти к установке
                </a>
                <a href="' . URL::site('floorplan') . '" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> Назад
                </a>
            </div>
        </div>';
        
        return $content;
    }

    private function getDeviceStatuses($points)
    {
        $statuses = array();
        foreach ($points as $point) {
            $deviceId = $point['id_dev'];
            if ($deviceId) {
                $statuses[$deviceId] = array(
                    'status' => 'online',
                    'mode' => 'normal',
                    'last_event' => date('Y-m-d H:i:s'),
                );
            }
        }
        return $statuses;
    }
	
	
	
	
public function action_testFind()
{
    $this->auto_render = false;
    
    // Получаем параметр id_dev из URL
    $id_dev = $this->request->query('id_dev');
    
    // Если параметр не передан или пустой
    if (empty($id_dev)) {
        echo 'Ошибка: параметр id_dev не передан';
        return;
    }
    
    // Приводим к int
    $id_dev = (int)$id_dev;
    
    echo '<pre>';
    echo '=== ТЕСТ ПОИСКА ===<br>';
    echo 'ID устройства: ' . $id_dev . '<br><br>';
    
    $model = Model::factory('Floorplanm');
    
    // Проверяем findDeviceInAllPlans
    echo '1. findDeviceInAllPlans:<br>';
    $device = $model->findDeviceInAllPlans($id_dev);
    var_dump($device);
    echo '<br><br>';
    
    // Проверяем findDeviceWithRelated
    echo '2. findDeviceWithRelated:<br>';
    $result = $model->findDeviceWithRelated($id_dev);
    var_dump($result);
    echo '<br><br>';
    
    echo '</pre>';
}
}
