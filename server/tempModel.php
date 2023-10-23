<?php 

require_once 'db.php';

date_default_timezone_set('US/Eastern');

class TempModel {

    private $db;

    public function __construct() {

        $conn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $this->db = new PDO($conn, DB_USER, DB_PASSWORD);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    // CRUD

    // CREATE
    public function postTemp($temp) {
        try {
            $query = "INSERT INTO `temp1`(`temp1_value`) VALUES (?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$temp]);
        } 
        catch (PDOException $e) {  }
        finally {  }
    }

    // READ
    public function getTemp() {

        try {

            $return = array();

            $query = "SELECT * FROM `temp1` ORDER BY `temp1_id` DESC LIMIT 1";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            array_push($return, true);
            array_push($return, $result);

        } 
        catch (PDOException $e) {

            array_push($return, false);
            array_push($return, $e);

        }
        finally {

            return $return;

        }

    }

}


?>