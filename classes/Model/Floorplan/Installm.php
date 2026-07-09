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
     * Выполнить SQL скрипт (разбивка по командам)
	 *$this->mess = Database::instance('fb')->query(NULL, $query);
     */
    private function executeSqlScript($sql)
    {
        if (empty($sql)) {
            return array('success' => false, 'error' => 'Empty SQL');
        }
        
        $db = Database::instance('fb');
        $results = array();
        
        // Разбиваем на отдельные команды
        $commands = $this->splitSqlCommands($sql);
 //echo Debug::vars('32', $commands);exit;  
        foreach ($commands as $command) {
            $command = trim($command);
            if (empty($command)) continue;
            
            try {
                //DB::query(Database::RAW, $command)->execute($db);
				Database::instance('fb')->query(NULL, $command);
                $results[] = array('success' => true, 'command' => substr($command, 0, 100) . '...');
            } catch (Exception $e) {
                $results[] = array('success' => false, 'command' => substr($command, 0, 100) . '...', 'error' => $e->getMessage());
				Kohana::$log->add(Log::ERROR, '43 executeSqlScript: ' . $e->getMessage());
            }
        }
        
        return $results;
    }

/**
 * Разбить SQL на отдельные команды и удалить SET TERM и разделители
 */
private function splitSqlCommands($sql)
{
    // Убираем комментарии
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Нормализуем переносы строк
    $sql = str_replace("\r\n", "\n", $sql);
    
    $commands = [];
    $currentCommand = '';
    $lines = explode("\n", $sql);
    $inComplexBlock = false;
    $skipNextLine = false;
    $delimiter = ';';
    
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        
        if ($trimmedLine === '') {
            continue;
        }
        
        // Пропускаем все SET TERM команды
        if (stripos($trimmedLine, 'SET TERM') === 0) {
            // Если это SET TERM ^ ; - запоминаем разделитель
            if (preg_match('/SET TERM\s+([^\s;]+)\s*;/i', $trimmedLine, $matches)) {
                $delimiter = $matches[1];
                $skipNextLine = false;
                continue;
            }
            // Если это SET TERM ; ^ - возвращаем стандартный разделитель
            if (preg_match('/SET TERM\s+;\s+([^\s;]+)/i', $trimmedLine, $matches)) {
                $delimiter = ';';
                // Если мы внутри блока, закрываем его
                if ($inComplexBlock) {
                    // Убираем последний перенос и ^ если есть
                    $currentCommand = rtrim($currentCommand);
                    // Удаляем ^ из конца, если он есть
                    $currentCommand = preg_replace('/\s*\^\s*$/', '', $currentCommand);
                    $commands[] = $currentCommand;
                    $currentCommand = '';
                    $inComplexBlock = false;
                }
                continue;
            }
            // Просто SET TERM - пропускаем
            continue;
        }
        
        // Если строка содержит только ^ - пропускаем её
        if (preg_match('/^\s*\^\s*$/', $trimmedLine)) {
            // Если мы внутри блока, это конец триггера
            if ($inComplexBlock) {
                // Добавляем команду без ^
                $commands[] = rtrim($currentCommand);
                $currentCommand = '';
                $inComplexBlock = false;
            }
            continue;
        }
        
        // Проверяем начало сложного блока (триггер, процедура, функция)
        if (preg_match('/^\s*(CREATE|ALTER|RECREATE)\s+(TRIGGER|PROCEDURE|FUNCTION)\s+/i', $trimmedLine)) {
            $inComplexBlock = true;
            $currentCommand = $line . "\n";
            continue;
        }
        
        if ($inComplexBlock) {
            // Проверяем, содержит ли строка END^
            if (preg_match('/END\s*\^\s*$/i', $trimmedLine)) {
                // Убираем ^ из конца
                $line = preg_replace('/\s*\^\s*$/', '', $trimmedLine);
                $currentCommand .= $line . "\n";
                // Добавляем команду без ^
                $commands[] = rtrim($currentCommand);
                $currentCommand = '';
                $inComplexBlock = false;
                continue;
            }
            
            // Проверяем, заканчивается ли строка на разделитель (но это не END)
            if (preg_match('/' . preg_quote($delimiter, '/') . '\s*$/i', $trimmedLine) && 
                !preg_match('/^\s*END\s*$/i', $trimmedLine)) {
                $currentCommand .= $line . "\n";
                // Добавляем команду
                $commands[] = rtrim($currentCommand);
                $currentCommand = '';
                $inComplexBlock = false;
                continue;
            }
            
            $currentCommand .= $line . "\n";
            continue;
        }
        
        // Обычные команды, разделенные ;
        if (substr($trimmedLine, -1) === ';') {
            if (!empty($currentCommand)) {
                $currentCommand .= $line . "\n";
                $commands[] = rtrim($currentCommand);
                $currentCommand = '';
            } else {
                $commands[] = $trimmedLine;
            }
        } else {
            if (!empty($currentCommand)) {
                $currentCommand .= $line . "\n";
            } else {
                $currentCommand = $line . "\n";
            }
        }
    }
    
    // Добавляем остаток, если есть
    if (!empty($currentCommand)) {
        $commands[] = rtrim($currentCommand);
    }
    
    return $commands;
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

    /**
     * Установить базу данных
     */
    public function installDatabase()
    {
        $sql = $this->readSqlFile('install.sql');
        if (!$sql) {
            return array(
                'success' => false,
                'error' => 'Не найден файл install.sql'
            );
        }
        
        $results = $this->executeSqlScript($sql);
        
        $errors = array();
        $successes = array();
        foreach ($results as $result) {
            if ($result['success']) {
                $successes[] = $result['command'];
            } else {
                $errors[] = $result['command'] . ' - ' . $result['error'];
            }
        }
        
        return array(
            'success' => empty($errors),
            'messages' => $successes,
            'errors' => $errors,
            'results' => $results,
        );
    }

    /**
     * Удалить базу данных
     */
    public function uninstallDatabase()
    {
        $sql = $this->readSqlFile('uninstall.sql');
        if (!$sql) {
            return array(
                'success' => false,
                'error' => 'Не найден файл uninstall.sql'
            );
        }
        
        $results = $this->executeSqlScript($sql);
        
        $errors = array();
        $successes = array();
        foreach ($results as $result) {
            if ($result['success']) {
                $successes[] = $result['command'];
            } else {
                $errors[] = $result['command'] . ' - ' . $result['error'];
            }
        }
        
        return array(
            'success' => empty($errors),
            'messages' => $successes,
            'errors' => $errors,
            'results' => $results,
        );
    }

    /**
     * Обновить базу данных с версии 1 до версии 2
     */
    public function upgradeDatabase()
    {
        $sql = $this->readSqlFile('upgrade_v1_to_v2.sql');
        if (!$sql) {
            return array(
                'success' => false,
                'error' => 'Не найден файл upgrade_v1_to_v2.sql'
            );
        }
        
        $results = $this->executeSqlScript($sql);
        
        $errors = array();
        $successes = array();
        foreach ($results as $result) {
            if ($result['success']) {
                $successes[] = $result['command'];
            } else {
                $errors[] = $result['command'] . ' - ' . $result['error'];
            }
        }
        
        return array(
            'success' => empty($errors),
            'messages' => $successes,
            'errors' => $errors,
            'results' => $results,
        );
    }
}
