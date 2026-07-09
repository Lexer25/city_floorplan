<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Floorplan extends Controller_Template
{
    public $template = 'template';
    
    public function before()
    {
        parent::before();
		$session = Session::instance();
        
        $this->is_admin = Auth::instance()->logged_in('admin');
        View::bind_global('is_admin', $this->is_admin);
        
       /*  if (!$this->is_admin) {
            Session::instance()->set('message', 'Доступ запрещен');
            Session::instance()->set('message_type', 'danger');
            $this->redirect('floorplan');
        } */
    }

    /**
     * Список планов
     */
    public function action_index()
    {
        $model = Model::factory('Floorplanm');
        $floorplans = $model->getFloorplans();
        $buildings = $model->getBuildings();

        $content = View::factory('floorplan/index', array(
            'floorplans' => $floorplans,
            'buildings' => $buildings,
            'is_admin' => $this->is_admin,
        ));

        $this->template->content = $content;
    }

    /**
     * Просмотр плана
     */
    public function action_view()
    {
        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if (!$id || !$model->floorplanExists($id)) {
            $this->redirect('floorplan');
        }

        $floorplan = $model->getFloorplanById($id);
        $points = $model->getPointsByFloorplan($id);
        $building = $model->getBuildingById($floorplan['id_building']);
        $floors = $model->getFloorsByBuilding($floorplan['id_building']);

        $deviceStatuses = $this->getDeviceStatuses($points);

        $content = View::factory('floorplan/view', array(
            'floorplan' => $floorplan,
            'points' => $points,
            'floors' => $floors,
            'building' => $building,
            'deviceStatuses' => $deviceStatuses,
            'is_admin' => $this->is_admin,
        ));

        $this->template->full_width = true;
        $this->template->content = $content;
    }

    /**
     * Редактирование плана (с поддержкой этажей)
     */
    public function action_edit()
    {
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
                    $uploadDir = DOCROOT . 'media/floorplan/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'floorplan_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        if (!empty($currentFloor['image']) && file_exists(DOCROOT . $currentFloor['image'])) {
                            @unlink(DOCROOT . $currentFloor['image']);
                        }
                        $image = 'media/floorplan/' . $filename;
                        $imageInfo = getimagesize($uploadDir . $filename);
                        $width = $imageInfo[0];
                        $height = $imageInfo[1];
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

            if ($action == 'add_point') {
                $x = Arr::get($post, 'x', 0);
                $y = Arr::get($post, 'y', 0);
                $deviceId = Arr::get($post, 'device_id', 0);
                $point_type = Arr::get($post, 'point_type', 'door');
                $label = Arr::get($post, 'label', '');

                if ($deviceId > 0) {
                    $model->addPoint($currentFloorId, $x, $y, $deviceId, $point_type, $label);
                    Session::instance()->set('message', 'Точка добавлена');
                    Session::instance()->set('message_type', 'success');
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
                    $model->deleteFloorplan($deleteFloorId);
                    Session::instance()->set('message', 'Этаж удален');
                    Session::instance()->set('message_type', 'success');
                } else {
                    Session::instance()->set('message', 'Нельзя удалить основной этаж');
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
            'availableDevices' => $model->getAvailableDevices(),
            'is_admin' => $this->is_admin,
            'mode' => 'edit',
            'current_floor_id' => $currentFloorId,
            'main_floor_id' => $id,
        ));

        $this->template->full_width = true;
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

        $model = Model::factory('Floorplanm');
        $result = $model->savePointsPositions($points);

        echo json_encode(array('success' => $result));
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

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $floorplanId = (int)$this->request->post('floorplan_id');
        $x = (float)$this->request->post('x');
        $y = (float)$this->request->post('y');
        $deviceId = (int)$this->request->post('device_id');
        $point_type = $this->request->post('point_type', 'door');
        $label = $this->request->post('label', '');

        if ($floorplanId <= 0 || $deviceId <= 0) {
            echo json_encode(array('success' => false, 'error' => 'Invalid parameters'));
            return;
        }

        $model = Model::factory('Floorplanm');
        $result = $model->addPoint($floorplanId, $x, $y, $deviceId, $point_type, $label);

        if ($result) {
            echo json_encode(array('success' => true, 'id' => $result));
        } else {
            echo json_encode(array('success' => false, 'error' => 'Ошибка при добавлении точки'));
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
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = DOCROOT . 'media/floorplan/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'floorplan_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = 'media/floorplan/' . $filename;
                }
            }

            if (empty($name) || empty($image)) {
                $errors = array();
                if (empty($name)) $errors['name'] = 'Название обязательно';
                if (empty($image)) $errors['image'] = 'Изображение обязательно';

                $content = View::factory('floorplan/add', array(
                    'errors' => $errors,
                    'post' => $post,
                    'buildings' => $buildings,
                    'is_admin' => $this->is_admin,
                ));
                $this->template->content = $content;
                return;
            }

            $imageInfo = getimagesize($uploadDir . $filename);
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            $result = $model->addFloorplan($name, $description, $image, $width, $height, 
                $buildingId, $floorNumber, $floorName);

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

        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if ($id && $model->floorplanExists($id)) {
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

        $model = Model::factory('Floorplanm');
        $buildings = $model->getBuildings();

        foreach ($buildings as &$building) {
            $floors = $model->getFloorsByBuilding($building['id_building']);
            $building['floors_count_actual'] = count($floors);
        }

        $content = View::factory('floorplan/buildings', array(
            'buildings' => $buildings,
            'is_admin' => $this->is_admin,
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
            ));
            $this->template->content = $content;
            return;
        }

        $content = View::factory('floorplan/add_building', array(
            'errors' => array(),
            'post' => array(),
            'is_admin' => $this->is_admin,
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
            ));
            $this->template->content = $content;
            return;
        }

        $content = View::factory('floorplan/edit_building', array(
            'building' => $building,
            'errors' => array(),
            'post' => array(),
            'is_admin' => $this->is_admin,
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

        $id = (int)$this->request->param('id', 0);
        $model = Model::factory('Floorplanm');

        if ($id && $model->buildingExists($id)) {
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

    /**
     * Получить статусы устройств (имитация)
     */
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
}
