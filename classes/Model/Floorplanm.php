<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Floorplanm extends Model
{
    private $db;
    
    public function __construct()
    {
       
        $this->db = Database::instance('fb');
    }
    
    /**
     * Преобразование ключей массива из верхнего регистра в нижний
     * и конвертация кодировки из Windows-1251 в UTF-8
     */
    private function convertToUtf8($data)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $newKey = is_string($key) ? strtolower($key) : $key;
                
                if (is_array($value)) {
                    $result[$newKey] = $this->convertToUtf8($value);
                } elseif (is_string($value)) {
                    $result[$newKey] = iconv('Windows-1251', 'UTF-8//IGNORE', $value);
                } else {
                    $result[$newKey] = $value;
                }
            }
            return $result;
        } elseif (is_string($data)) {
            return iconv('Windows-1251', 'UTF-8//IGNORE', $data);
        }
        return $data;
    }

    /**
     * Получить список планов (всех)
     */
    public function getFloorplans()
    {
        $sql = 'SELECT fp.id_floorplan, fp.name, fp.description, fp.image, fp.width, fp.height,
                       fp.id_building, fp.floor_number, fp.floor_name,
                       (SELECT COUNT(*) FROM floorplan_point fp2 WHERE fp2.id_floorplan = fp.id_floorplan) as points_count
                FROM floorplan fp
                ORDER BY fp.id_building, fp.floor_number';

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить план по ID
     */
    public function getFloorplanById($id)
    {
        $sql = 'SELECT fp.id_floorplan, fp.name, fp.description, fp.image, fp.width, fp.height,
                       fp.id_building, fp.floor_number, fp.floor_name
                FROM floorplan fp
                WHERE fp.id_floorplan = ' . intval($id);

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        if (count($query) > 0) {
            $result = $this->convertToUtf8($query);
            return $result[0];
        }

        return null;
    }

    /**
     * Получить план по зданию и номеру этажа
     */
    public function getFloorplanByBuildingAndFloor($buildingId, $floorNumber)
    {
        $sql = 'SELECT fp.id_floorplan, fp.name, fp.description, fp.image, fp.width, fp.height,
                       fp.id_building, fp.floor_number, fp.floor_name
                FROM floorplan fp
                WHERE fp.id_building = ' . intval($buildingId) . '
                AND fp.floor_number = ' . intval($floorNumber);

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        if (count($query) > 0) {
            $result = $this->convertToUtf8($query);
            return $result[0];
        }

        return null;
    }

    /**
     * Получить все этажи здания
     */
    public function getFloorsByBuilding($buildingId)
    {
        $sql = 'SELECT fp.id_floorplan, fp.name, fp.floor_number, fp.floor_name,
                       (SELECT COUNT(*) FROM floorplan_point fp2 WHERE fp2.id_floorplan = fp.id_floorplan) as points_count
                FROM floorplan fp
                WHERE fp.id_building = ' . intval($buildingId) . '
                ORDER BY fp.floor_number';

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить список зданий
     */
    public function getBuildings()
    {
        $sql = 'SELECT b.id_building, b.name, b.address, b.floors_count
                FROM building b
                ORDER BY b.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить здание по ID
     */
    public function getBuildingById($id)
    {
        $sql = 'SELECT b.id_building, b.name, b.address, b.floors_count
                FROM building b
                WHERE b.id_building = ' . intval($id);

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        if (count($query) > 0) {
            $result = $this->convertToUtf8($query);
            return $result[0];
        }

        return null;
    }

    /**
     * Добавить здание
     */
    public function addBuilding($name, $address = '', $floorsCount = 1)
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);
        $addressForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $address);
        $addressForDb = addslashes($addressForDb);

        $sql = "INSERT INTO building (name, address, floors_count)
                VALUES ('{$nameForDb}', '{$addressForDb}', " . intval($floorsCount) . ")";

        try {
            DB::query(Database::INSERT, $sql)->execute($this->db);

            $lastId = DB::query(Database::SELECT, "SELECT MAX(id_building) as last_id FROM building")
                ->execute($this->db)
                ->as_array();

            return $lastId[0]['LAST_ID'];
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding building: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновить здание
     */
    public function updateBuilding($id, $name, $address = '', $floorsCount = 1)
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);
        $addressForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $address);
        $addressForDb = addslashes($addressForDb);

        $sql = "UPDATE building
                SET name = '{$nameForDb}',
                    address = '{$addressForDb}',
                    floors_count = " . intval($floorsCount) . "
                WHERE id_building = " . intval($id);

        try {
            DB::query(Database::UPDATE, $sql)->execute($this->db);
            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error updating building: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить здание
     */
    public function deleteBuilding($id)
    {
        try {
            // Проверяем, есть ли этажи у здания
            $checkSql = "SELECT COUNT(*) as cnt FROM floorplan WHERE id_building = " . intval($id);
            $result = DB::query(Database::SELECT, $checkSql)
                ->execute($this->db)
                ->as_array();

            if ($result[0]['CNT'] > 0) {
                return array('success' => false, 'error' => 'У здания есть этажи. Сначала удалите все этажи.');
            }

            $sql = "DELETE FROM building WHERE id_building = " . intval($id);
            DB::query(Database::DELETE, $sql)->execute($this->db);

            return array('success' => true);
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error deleting building: ' . $e->getMessage());
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    /**
     * Проверить, существует ли здание
     */
    public function buildingExists($id)
    {
        $sql = "SELECT COUNT(*) as cnt FROM building WHERE id_building = " . intval($id);

        $result = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return ($result[0]['CNT'] > 0);
    }

    /**
     * Получить все точки прохода для плана
     */
    public function getPointsByFloorplan($floorplanId)
    {
        $sql = 'SELECT fp.id_point, fp.x_pos, fp.y_pos, fp.id_dev, fp.point_type, fp.label,
                       d.name as device_name, d.id_reader
                FROM floorplan_point fp
                LEFT JOIN device d ON fp.id_dev = d.id_dev
                WHERE fp.id_floorplan = ' . intval($floorplanId) . '
                ORDER BY fp.point_type, fp.label';

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить устройства для выбора (только с ридером)
     */
    public function getAvailableDevices()
    {
        $sql = 'SELECT d.id_dev, d.name, d.id_reader
                FROM device d
                WHERE d.id_reader IS NOT NULL
                ORDER BY d.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Добавить новый план (этаж)
     */
    public function addFloorplan($name, $description, $image, $width, $height, $buildingId = 1, $floorNumber = 1, $floorName = '')
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);
        $descForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $description);
        $descForDb = addslashes($descForDb);
        $floorNameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $floorName);
        $floorNameForDb = addslashes($floorNameForDb);

        $sql = "INSERT INTO floorplan (name, description, image, width, height, id_building, floor_number, floor_name)
                VALUES ('{$nameForDb}', '{$descForDb}', '{$image}', " . intval($width) . ", " . intval($height) . ", 
                        " . intval($buildingId) . ", " . intval($floorNumber) . ", '{$floorNameForDb}')";

        try {
            DB::query(Database::INSERT, $sql)->execute($this->db);

            $lastId = DB::query(Database::SELECT, "SELECT MAX(id_floorplan) as last_id FROM floorplan")
                ->execute($this->db)
                ->as_array();

            return $lastId[0]['LAST_ID'];
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding floorplan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновить план
     */
    public function updateFloorplan($id, $name, $description, $image, $width, $height, $buildingId = null, $floorNumber = null, $floorName = null)
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);
        $descForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $description);
        $descForDb = addslashes($descForDb);

        $sql = "UPDATE floorplan
                SET name = '{$nameForDb}',
                    description = '{$descForDb}',
                    image = '{$image}',
                    width = " . intval($width) . ",
                    height = " . intval($height);

        if ($buildingId !== null) {
            $sql .= ", id_building = " . intval($buildingId);
        }
        if ($floorNumber !== null) {
            $sql .= ", floor_number = " . intval($floorNumber);
        }
        if ($floorName !== null) {
            $floorNameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $floorName);
            $floorNameForDb = addslashes($floorNameForDb);
            $sql .= ", floor_name = '{$floorNameForDb}'";
        }

        $sql .= " WHERE id_floorplan = " . intval($id);

        try {
            DB::query(Database::UPDATE, $sql)->execute($this->db);
            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error updating floorplan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить план
     */
    public function deleteFloorplan($id)
    {
        try {
            DB::query(Database::DELETE,
                "DELETE FROM floorplan_point WHERE id_floorplan = " . intval($id))
                ->execute($this->db);

            DB::query(Database::DELETE,
                "DELETE FROM floorplan WHERE id_floorplan = " . intval($id))
                ->execute($this->db);

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error deleting floorplan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Добавить точку на план (ИСПРАВЛЕННАЯ ВЕРСИЯ)
     */
    public function addPoint($floorplanId, $x, $y, $deviceId, $point_type = 'door', $label = '')
    {
        try {
            // Проверяем, существует ли устройство с таким id_dev
            $checkSql = "SELECT COUNT(*) as cnt FROM device WHERE id_dev = " . intval($deviceId);
            $checkResult = DB::query(Database::SELECT, $checkSql)
                ->execute($this->db)
                ->as_array();
            
            if ($checkResult[0]['CNT'] == 0) {
                Kohana::$log->add(Log::ERROR, 'Device not found: id_dev=' . $deviceId);
                return false;
            }
            
            // Проверяем, существует ли план
            $checkFloorplanSql = "SELECT COUNT(*) as cnt FROM floorplan WHERE id_floorplan = " . intval($floorplanId);
            $checkFloorplanResult = DB::query(Database::SELECT, $checkFloorplanSql)
                ->execute($this->db)
                ->as_array();
            
            if ($checkFloorplanResult[0]['CNT'] == 0) {
                Kohana::$log->add(Log::ERROR, 'Floorplan not found: id_floorplan=' . $floorplanId);
                return false;
            }
            
            // Экранируем строки
            $labelForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $label);
            $labelForDb = addslashes($labelForDb);
            $point_type_escaped = addslashes($point_type);
            
            // Проверяем обязательные поля
            if ($floorplanId <= 0 || $deviceId <= 0) {
                Kohana::$log->add(Log::ERROR, 'Invalid parameters: floorplanId=' . $floorplanId . ', deviceId=' . $deviceId);
                return false;
            }

            // Формируем SQL-запрос
            $sql = "INSERT INTO floorplan_point (id_floorplan, x_pos, y_pos, id_dev, point_type, label)
                    VALUES (" . intval($floorplanId) . ", " . floatval($x) . ", " . floatval($y) . ", 
                            " . intval($deviceId) . ", '{$point_type_escaped}', '{$labelForDb}')";
            
            Kohana::$log->add(Log::DEBUG, 'SQL: ' . $sql);
            
            // Выполняем запрос
            DB::query(Database::INSERT, $sql)->execute($this->db);
            
            // Получаем последний ID
            $lastIdResult = DB::query(Database::SELECT, "SELECT MAX(id_point) as last_id FROM floorplan_point")
                ->execute($this->db)
                ->as_array();
            
            $lastId = $lastIdResult[0]['LAST_ID'];
            
            Kohana::$log->add(Log::INFO, 'Point added: id_point=' . $lastId . ', id_dev=' . $deviceId);
            
            return $lastId;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding point: ' . $e->getMessage());
            Kohana::$log->add(Log::ERROR, 'SQL: ' . (isset($sql) ? $sql : ''));
            return false;
        }
    }

    /**
     * Обновить точку
     */
    public function updatePoint($id, $x, $y, $deviceId, $point_type, $label)
    {
        $labelForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $label);
        $labelForDb = addslashes($labelForDb);

        $sql = "UPDATE floorplan_point
                SET x_pos = " . floatval($x) . ",
                    y_pos = " . floatval($y) . ",
                    id_dev = " . intval($deviceId) . ",
                    point_type = '{$point_type}',
                    label = '{$labelForDb}'
                WHERE id_point = " . intval($id);

        try {
            DB::query(Database::UPDATE, $sql)->execute($this->db);
            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error updating point: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить точку
     */
    public function deletePoint($id)
    {
        try {
            $sql = "DELETE FROM floorplan_point WHERE id_point = " . intval($id);
            DB::query(Database::DELETE, $sql)->execute($this->db);
            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error deleting point: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Сохранить позиции точек (массовое обновление)
     */
    public function savePointsPositions($points)
    {
        try {
            foreach ($points as $point) {
                $id = (int)$point['id'];
                $x = (float)$point['x'];
                $y = (float)$point['y'];

                $sql = "UPDATE floorplan_point
                        SET x_pos = " . $x . ", y_pos = " . $y . "
                        WHERE id_point = " . $id;

                DB::query(Database::UPDATE, $sql)->execute($this->db);
            }

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error saving points positions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Проверить, существует ли план
     */
    public function floorplanExists($id)
    {
        $sql = "SELECT COUNT(*) as cnt FROM floorplan WHERE id_floorplan = " . intval($id);

        $result = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return ($result[0]['CNT'] > 0);
    }

    /**
     * Проверить, существует ли этаж в здании
     */
    public function floorExists($buildingId, $floorNumber)
    {
        $sql = "SELECT COUNT(*) as cnt FROM floorplan 
                WHERE id_building = " . intval($buildingId) . " 
                AND floor_number = " . intval($floorNumber);

        $result = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return ($result[0]['CNT'] > 0);
    }

    /**
     * Копировать этаж
     */
    public function copyFloorplan($fromId, $newFloorNumber, $newFloorName = '')
    {
        $source = $this->getFloorplanById($fromId);
        if (!$source) {
            return false;
        }

        $points = $this->getPointsByFloorplan($fromId);

        $newName = $source['name'] . ' (копия ' . $newFloorNumber . ' эт.)';
        $newId = $this->addFloorplan(
            $newName,
            $source['description'],
            $source['image'],
            $source['width'],
            $source['height'],
            $source['id_building'],
            $newFloorNumber,
            $newFloorName ?: $newFloorNumber . ' этаж'
        );

        if (!$newId) {
            return false;
        }

        foreach ($points as $point) {
            $this->addPoint(
                $newId,
                $point['x_pos'],
                $point['y_pos'],
                $point['id_dev'],
                $point['point_type'],
                $point['label']
            );
        }

        return $newId;
    }

    /**
     * Получить максимальный номер этажа в здании
     */
    public function getMaxFloorNumber($buildingId)
    {
        $sql = "SELECT MAX(floor_number) as max_floor FROM floorplan 
                WHERE id_building = " . intval($buildingId);

        $result = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();

        return $result[0]['MAX_FLOOR'] ?: 0;
    }
}
