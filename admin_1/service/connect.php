<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL); 
date_default_timezone_set('Asia/Bangkok');

/** Class Database สำหรับติดต่อฐานข้อมูล */
class Database {
    private $host = "localhost";
    private $dbname = "club_app";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function connect() {
        try{
        //    การดักจับ error
            $this->conn = new PDO('mysql:host='.$this->host.'; 
                                dbname='.$this->dbname.'; 
                                charset=utf8', 
                                $this->username, 
                                $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $exception){
            echo "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $exception->getMessage();
            exit();
        }
        return $this->conn;
    }
}

/**
 * ประกาศ Instance ของ Class Database
 */
$Database = new Database();
$conn = $Database->connect();
?>