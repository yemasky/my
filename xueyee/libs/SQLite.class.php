<?php
/**
 * @author pengjing
 * @copyright 2012
 */
class SQLite
{
    /**
     * database object
     */
    private $conn;

    /**
     * error information
     */
    private $error;
    
    /**
     * SQLite::__construct()
     * 
     * @return
     */
    function __construct($dbname) {
        $this->conn = $this->connect($dbname);
    }

    /**
     * SQLite::__destruct()
     * 
     * @return
     */
    function __destruct() {
        if($this->conn){
            sqlite_close($this->conn);
        }
    }
    
    /**
     * SQLite::query()
     * 
     * @param mixed $sql
     * @return
     */
    public function query( $sql )  {
        $data = Array();
        $i = 0;
        $err_msg = "";
        $result = sqlite_query($this->conn, $sql, SQLITE_ASSOC, $err_msg);
        
        if($result == false){
            throw new Exception("{$sql}<br />执行错误:" . $err_msg);
        } else {   
            while($arr = sqlite_fetch_array($result, SQLITE_ASSOC)) {       
                $data[$i++] = $arr;
            }
        }
        if(count($data) > 0){
            return $data;
        } else{
            return sqlite_changes($this->conn);
        }
    }
    
    /**
     * SQLite::connect()
     * 
     * @param bool $is_master
     * @return
     */
    private function connect($dbname) {
    	$sqliteerror = "";
        $conn = sqlite_open($dbname, 0666, $sqliteerror);
        if( !$conn ) {
            $this->error = $sqliteerror;
            return NULL;
        }
        return $conn;
    }
    
    /**
     * SQLite::error()
     * 
     * @return
     */
    public function error(){
        return $this->error;
    }
}

?>