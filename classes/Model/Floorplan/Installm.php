<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Floorplan_Installm extends Model
{
    /**
     * Прочитать SQL файл
     */
    private function readSqlFile($filename)
    {
        $path = MODPATH . 'floorplan/sql/' . $filename;
        if (!file_exists($path)) {
            return false;
        }
        return file_get_contents($path);
    }



    /**
     * Проверить существование таблицы
     */
    public function tableExists($tableName)
    {
        try {
            $sql = 'SELECT 1 FROM RDB$RELATIONS WHERE RDB$RELATION_NAME = \'' . strtoupper($tableName) . '\'';
            $result = DB::query(Database::SELECT, $sql)
                ->execute(Database::instance('fb'))
                ->as_array();
            return count($result) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Проверить существование генератора
     */
    public function generatorExists($generatorName)
    {
        try {
            $sql = 'SELECT 1 FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME = \'' . strtoupper($generatorName) . '\'';
            $result = DB::query(Database::SELECT, $sql)
                ->execute(Database::instance('fb'))
                ->as_array();
            return count($result) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Проверить существование триггера
     */
    public function triggerExists($triggerName)
    {
        try {
            $sql = 'SELECT 1 FROM RDB$TRIGGERS WHERE RDB$TRIGGER_NAME = \'' . strtoupper($triggerName) . '\'';
            $result = DB::query(Database::SELECT, $sql)
                ->execute(Database::instance('fb'))
                ->as_array();
            return count($result) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Проверить наличие всех необходимых таблиц
     */
    public function checkDatabase()
    {
        $result = array(
            'tables' => array(),
            'generators' => array(),
            'triggers' => array(),
            'all_ok' => true,
        );
        
        $tables = array('FLOORPLAN', 'FLOORPLAN_POINT', 'BUILDING');
        foreach ($tables as $table) {
            $exists = $this->tableExists($table);
            $result['tables'][$table] = $exists;
            if (!$exists) {
                $result['all_ok'] = false;
            }
        }
        
        $generators = array('GEN_FLOORPLAN_ID', 'GEN_FLOORPLAN_POINT_ID', 'GEN_BUILDING_ID');
        foreach ($generators as $generator) {
            $exists = $this->generatorExists($generator);
            $result['generators'][$generator] = $exists;
            if (!$exists) {
                $result['all_ok'] = false;
            }
        }
        
        $triggers = array('TRG_FLOORPLAN_BI', 'TRG_FLOORPLAN_POINT_BI', 'TRG_BUILDING_BI');
        foreach ($triggers as $trigger) {
            $exists = $this->triggerExists($trigger);
            $result['triggers'][$trigger] = $exists;
            if (!$exists) {
                $result['all_ok'] = false;
            }
        }
        
        return $result;
    }

   
}
