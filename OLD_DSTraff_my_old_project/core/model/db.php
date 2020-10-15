<?php
require_once(confDB);

class DB
{
    
    function __construct($DBName = 'dstraff')
    { 
        $this->DBName = $DBName;
        if ($this->DBName == 'aggregator') {
            $DB = mysqlDBaggregator;
        } elseif ($this->DBName == 'forum') {
            $DB = mysqlDBforum;
        } else {
            $DB = mysqlDB;
        }
        try {
            $this->db = new PDO('mysql:host=' . mysqlHost . ';dbname=' . $DB . '', mysqlLogin, mysqlPassword);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec("set names utf8");
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    function selectAnd($args)
    {
        
        $table = $args['table'];
        $where = " 1 = '1' ";
        
        foreach ($args['where'] as $key => $value) {
            
            $where .= "and " . $key . " = " . $this->db->quote($value);
            
        }
        
        $select = $this->db->query("SELECT * FROM $table WHERE $where")->fetch(PDO::FETCH_ASSOC);
        
        self::logInsertInDB("SELECT * FROM $table WHERE $where");
        
        return $select;
        
    }
    
    
    function selectAllAnd($args)
    {
        
        $table = $args['table'];
        $where = " 1 = '1' ";
        
        foreach ($args['where'] as $key => $value) {
            
            $where .= "and " . $key . " = " . $this->db->quote($value);
            
        }
		
		$sort = $args['sort'] ?? 'id';
		$sortType = $args['sortType'] ?? 'DESC';
        
        $select = $this->db->query("SELECT * FROM $table WHERE $where ORDER by $sort $sortType");
        
        self::logInsertInDB("SELECT * FROM $table WHERE $where ORDER by $sort $sortType");
        
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            
            $result[] = $row;
            
        }
        
        if (!isset($result)) {
            $result = false;
        }
        
        return $result;
        
    }
    
	    function selectAllOr($args)
    {
        
        $table = $args['table'];
        $where = "";
        $i = 0;
        foreach ($args['where'] as $key => $value) {
        if ($i > 0) {  
				$where .= "or " . $key . " = " . $this->db->quote($value);
			} else {
				$where .= "" . $key . " = " . $this->db->quote($value);
			}
        $i++; 
        }
		
		$sort = $args['sort'] ?? 'id';
		$sortType = $args['sortType'] ?? 'DESC';
        
        $select = $this->db->query("SELECT * FROM $table WHERE $where ORDER by $sort $sortType");

        self::logInsertInDB("SELECT * FROM $table WHERE $where ORDER by $sort $sortType");
        
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            
            $result[] = $row;
            
        }
        
        if (!isset($result)) {
            $result = false;
        }
        
        return $result;
        
    }
    
    function insert($args)
    {
        
        $table = $args['table'];
        $set   = "";
        
        foreach ($args['set'] as $key => $value) {
            
            $value = prepareStr($value);
            
            $set .= $key . " = " . $this->db->quote($value) . ",";
            
        }
        
        $set = mb_substr($set, 0, -1);
        
        $this->db->query("INSERT INTO $table SET $set");
        $insert = $this->db->lastInsertId();
        
        self::logInsertInDB("INSERT INTO $table SET $set");
        
        return $insert;
        
    }
    
    function deleteAnd($args)
    {
        
        $table = $args['table'];
        $where = " 1 = '1' ";
        
        foreach ($args['where'] as $key => $value) {
            
            $where .= "and " . $key . " = " . $this->db->quote($value);
            
        }
        
        $delete = $this->db->exec("DELETE FROM $table WHERE $where");
        
        self::logInsertInDB("DELETE FROM $table WHERE $where");
        
        return $delete;
        
    }
    
    
    function update($args)
    {
        
        if (!$args['where']) {
            return ('Error where');
            exit;
        }
        
        $table = $args['table'];
        $set   = "";
        $where = " 1 = '1' ";
        
        foreach ($args['where'] as $key => $value) {
            
            $where .= "and " . $key . " = " . $this->db->quote($value);
            
        }
        
        foreach ($args['set'] as $key => $value) {
            
            $value = prepareStr($value);
            
            $set .= $key . " = " . $this->db->quote($value) . ",";
            
        }
        
        $set = mb_substr($set, 0, -1);
        
        $update = $this->db->query("UPDATE $table SET $set WHERE $where");
        
        
        //	echo "UPDATE $table SET $set WHERE $where";
        //	die();
        
        self::logInsertInDB("UPDATE $table SET $set WHERE $where");
        
        return $update;
        
    }
    
    
    function query($args)
    {
        
        $query = $this->db->query("$args");
        
        if ($this->DBName == 'dstraff') {
            self::logInsertInDB("$args");
        }
        
        if ($args[0] == 'S') {
            
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                
                $result[] = $row;
                
            }
        } else {
            
            $result = '';
            
        }
        
        
        if (!isset($result)) {
            $result = false;
        }
        
        return $result;
        
    }
    
    
    private function logInsertInDB($query)
    {
        
        if (logDBOn == 'true') {
            $logQuery = prepareStr($query);
            $logIp    = $_SERVER['REMOTE_ADDR'];
            if (isset($_COOKIE['sessionId'])) {
                $logSessionID = $_COOKIE['sessionId'];
            } else {
                $logSessionID = false;
            }
            if ($logSessionID) {
                $selectBuffer = $this->db->query("SELECT * FROM sessions WHERE session = '$logSessionID'")->fetch(PDO::FETCH_ASSOC);
                if ($selectBuffer) {
                    $logUid   = $selectBuffer['uid'];
                    $logLogin = $selectBuffer['login'];
                } else {
                    $logUid   = '';
                    $logLogin = 'something wrong';
                }
            } else {
                $logSessionID = "unauthorized";
                $logUid       = "0";
                $logLogin     = "unauthorized";
            }
            $this->db->query("INSERT INTO logdb SET event = 'selectAnd', query = '$logQuery', ip = '$logIp', uid = '$logUid', login = '$logLogin' ,sessionid = '$logSessionID'");
        }
        
    }
    
    
    
    
    
    
}