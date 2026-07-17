<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Floorplanm extends Model
{
    // Константы для загрузки файлов
    const UPLOAD_DIR = 'uploads/floorplan/';
    const MAX_FILE_SIZE = 20 * 1024 * 1024;
    const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
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
     * Проверить, занято ли устройство на другом плане
     */
    public function isDeviceUsed($deviceId, $floorplanId = 0)
    {
        $sql = "SELECT COUNT(*) as cnt FROM floorplan_point 
                WHERE id_dev = " . intval($deviceId);
        
        if ($floorplanId > 0) {
            $sql .= " AND id_floorplan != " . intval($floorplanId);
        }
        
        $result = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();
        
        return ($result[0]['CNT'] > 0);
    }

    /**
     * Поиск устройства по id_dev во всех планах
     */
    public function findDeviceInAllPlans($deviceId)
    {
        $sql = "SELECT fp.id_floorplan, fp.name as floorplan_name, 
                       fp.id_building, fp.floor_number,
                       fpp.id_point, fpp.x_pos, fpp.y_pos, fpp.point_type, fpp.label,
                       d.name as device_name
                FROM floorplan_point fpp
                JOIN floorplan fp ON fpp.id_floorplan = fp.id_floorplan
                LEFT JOIN device d ON fpp.id_dev = d.id_dev
                WHERE fpp.id_dev = " . intval($deviceId);
 echo Debug::vars('303');exit;       
        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();
        
        if (count($query) > 0) {
            $result = $this->convertToUtf8($query);
			echo Debug::vars('310', $result);exit;
            return $result[0];
        }
        
        return null;
    }

    /**
     * Получить устройства с id_reader (считыватели)
     */
    public function getAvailableReaders($floorplanId = 0)
    {
        $sql = "SELECT d.id_dev, d.name, d.id_reader
                FROM device d
                WHERE d.id_reader IS NOT NULL";
        
        $usedDevices = $this->getUsedDeviceIds($floorplanId);
        if (!empty($usedDevices)) {
            $sql .= " AND d.id_dev NOT IN (" . implode(',', $usedDevices) . ")";
        }
        
        $sql .= " ORDER BY d.name";
        
        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();
        
        return $this->convertToUtf8($query);
    }

    /**
     * Получить устройства без id_reader (контроллеры)
     */
    public function getAvailableControllers($floorplanId = 0)
    {
        $sql = "SELECT d.id_dev, d.name, d.id_reader
                FROM device d
                WHERE d.id_reader IS NULL";
        
        $usedDevices = $this->getUsedDeviceIds($floorplanId);
        if (!empty($usedDevices)) {
            $sql .= " AND d.id_dev NOT IN (" . implode(',', $usedDevices) . ")";
        }
        
        $sql .= " ORDER BY d.name";
        
        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();
        
        return $this->convertToUtf8($query);
    }

    /**
     * Получить ID уже использованных устройств
     */
    public function getUsedDeviceIds($floorplanId = 0)
    {
        $sql = "SELECT DISTINCT id_dev FROM floorplan_point";
        if ($floorplanId > 0) {
            $sql .= " WHERE id_floorplan = " . intval($floorplanId);
        }
        
        $query = DB::query(Database::SELECT, $sql)
            ->execute($this->db)
            ->as_array();
        
        $ids = array();
        foreach ($query as $row) {
            if ($row['ID_DEV']) {
                $ids[] = $row['ID_DEV'];
            }
        }
        
        return $ids;
    }

    /**
     * Получить все устройства с указанием, используются они или нет
     */
    public function getAllDevicesWithStatus($floorplanId = 0)
    {
        $sql = "SELECT d.id_dev, d.name, d.id_reader,
                CASE 
                    WHEN fp.id_point IS NOT NULL THEN 1 
                    ELSE 0 
                END as is_used
                FROM device d
                LEFT JOIN floorplan_point fp ON fp.id_dev = d.id_dev
                GROUP BY d.id_dev, d.name, d.id_reader, fp.id_point
                ORDER BY d.name";
        
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
     * Добавить точку на план
     */
    public function addPoint($floorplanId, $x, $y, $deviceId, $point_type = 'reader', $label = '')
    {
        try {
            // Проверяем, не занято ли устройство
            if ($this->isDeviceUsed($deviceId, $floorplanId)) {
                Kohana::$log->add(Log::ERROR, 'Device already used: id_dev=' . $deviceId);
                return false;
            }
            
            // Проверяем, существует ли устройство
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
            
            $labelForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $label);
            $labelForDb = addslashes($labelForDb);
            $point_type_escaped = addslashes($point_type);

            $sql = "INSERT INTO floorplan_point (id_floorplan, x_pos, y_pos, id_dev, point_type, label)
                    VALUES (" . intval($floorplanId) . ", " . floatval($x) . ", " . floatval($y) . ", 
                            " . intval($deviceId) . ", '{$point_type_escaped}', '{$labelForDb}')";
            
            DB::query(Database::INSERT, $sql)->execute($this->db);
            
            $lastIdResult = DB::query(Database::SELECT, "SELECT MAX(id_point) as last_id FROM floorplan_point")
                ->execute($this->db)
                ->as_array();
            
            $lastId = $lastIdResult[0]['LAST_ID'];
            
            Kohana::$log->add(Log::INFO, 'Point added: id_point=' . $lastId . ', id_dev=' . $deviceId);
            
            return $lastId;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding point: ' . $e->getMessage());
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

    // ==========================================
    // МЕТОДЫ ДЛЯ БЕЗОПАСНОЙ ЗАГРУЗКИ ФАЙЛОВ
    // ==========================================

    /**
     * Безопасная загрузка изображения плана
     */
    public function uploadFloorplanImage($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('Файл слишком большой (максимум ' . (self::MAX_FILE_SIZE / 1024 / 1024) . ' МБ)');
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception('Файл был загружен частично');
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception('Файл не выбран');
                default:
                    throw new Exception('Ошибка загрузки файла');
            }
        }
        
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new Exception('Файл не должен превышать ' . (self::MAX_FILE_SIZE / 1024 / 1024) . ' МБ');
        }
        
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                throw new Exception('Разрешены только изображения (JPG, PNG, GIF, WebP). Получен тип: ' . $mimeType);
            }
        } else {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                throw new Exception('Файл не является изображением');
            }
            
            $mimeType = $imageInfo['mime'];
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                throw new Exception('Разрешены только изображения (JPG, PNG, GIF, WebP). Получен тип: ' . $mimeType);
            }
        }
        
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('Файл не является корректным изображением');
        }
        
        $extension = ltrim(image_type_to_extension($imageInfo[2]), '.');
        $filename = uniqid('floorplan_', true) . '.' . $extension;
        
        $uploadPath = DOCROOT . self::UPLOAD_DIR;
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                throw new Exception('Не удалось создать директорию для загрузки');
            }
        }
        
        if (!is_writable($uploadPath)) {
            throw new Exception('Нет прав на запись в директорию ' . self::UPLOAD_DIR);
        }
        
        $targetPath = $uploadPath . $filename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Не удалось сохранить файл');
        }
        
        return self::UPLOAD_DIR . $filename;
    }

    /**
     * Удаление изображения плана
     */
    public function deleteFloorplanImage($imagePath)
    {
        if (empty($imagePath)) {
            return true;
        }
        
        $fullPath = DOCROOT . $imagePath;
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Получить URL изображения для отображения
     */
    public function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }
        
        $fullPath = DOCROOT . $imagePath;
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return '';
        }
        
        return URL::base() . $imagePath;
    }
	
	
	/**
 * Поиск устройств на плане с таким же id_ctrl
 * @param int $floorplanId - ID плана
 * @param int $id_ctrl - ID контроллера
 * @return array - список устройств с этим id_ctrl
 */
public function getDevicesByCtrlOnFloorplan($floorplanId, $id_ctrl)
{
    if (!$id_ctrl) {
        return array();
    }
    
    $sql = "SELECT fpp.id_point, fpp.x_pos, fpp.y_pos, fpp.id_dev, fpp.point_type, fpp.label,
                   d.name as device_name, d.id_ctrl, d.id_reader
            FROM floorplan_point fpp
            LEFT JOIN device d ON fpp.id_dev = d.id_dev
            WHERE fpp.id_floorplan = " . intval($floorplanId) . "
            AND d.id_ctrl = " . intval($id_ctrl) . "
            ORDER BY fpp.id_point";
    
    $query = DB::query(Database::SELECT, $sql)
        ->execute($this->db)
        ->as_array();
    
    return $this->convertToUtf8($query);
}

/**
 * Поиск устройства и всех связанных по id_ctrl
 */
public function findDeviceWithRelated($deviceId)
{
    $device = $this->findDeviceInAllPlans($deviceId);
    
    if (!$device) {
        return null;
    }
    
    // Проверяем, есть ли id_ctrl у найденного устройства
    $id_ctrl = isset($device['id_ctrl']) ? $device['id_ctrl'] : null;
    $floorplanId = $device['id_floorplan'];
    
    if ($id_ctrl) {
        $related = $this->getDevicesByCtrlOnFloorplan($floorplanId, $id_ctrl);
    } else {
        $related = array();
    }
    
    return array(
        'device' => $device,
        'related' => $related,
        'id_ctrl' => $id_ctrl,
        'floorplan_id' => $floorplanId
    );
}

}