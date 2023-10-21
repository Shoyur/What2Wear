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
    public function createOrder($user_id, $order_name, $order_address, $order_phone, $order_cc_last4, $order_total, $order_deliv, $order_notes, $cart_data) {
        try {

            $order_date = (new DateTime())->format('Y-m-d H:i:s');

            $return = array();

            // transaction to commit or rollback, in case there is an error, because we are making multiple queries
            $this->db->beginTransaction();

            $query1 = "INSERT INTO `order` (user_id, order_name, order_address, order_phone, order_cc_last4, order_total, order_date, order_deliv, order_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->execute([$user_id, $order_name, $order_address, $order_phone, $order_cc_last4, $order_total, $order_date, $order_deliv, $order_notes]);
            $order_id = $this->db->lastInsertId();

            $query2 = "INSERT INTO foodbyorder (order_id, food_id, foodbyorder_options) VALUES ";

            $multi_values = [];

            // var_dump($cart_data);

            foreach ($cart_data as $food) {
                $food_id = $food[0];
                // $foodbyorder_options = $food[1]; // not receiving index 1
                $foodbyorder_options = "";
                $multi_values[] = "($order_id, $food_id, '$foodbyorder_options')";

                $query3 = "UPDATE food SET food_sold = food_sold + 1 WHERE food_id = ?";
                $stmt2 = $this->db->prepare($query3);
                $stmt2->execute([$food_id]);
            }

            $query2 .= implode(", ", $multi_values);

            $stmt2 = $this->db->prepare($query2);
            $stmt2->execute();

            // everything went fine so commit the transaction
            $this->db->commit();

            array_push($return, true);
            array_push($return, $order_id);
        } 
        catch (PDOException $e) {

            // error, so revert the transaction
            $this->db->rollBack();

            array_push($return, false);
            array_push($return, $e);

        }
        finally {

            return $return;

        }

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

    // public function getTemp () {

    //     try {

    //         $return = array();

    //         $query = "SELECT * FROM `temp1`";
    //         $stmt = $this->db->query($query);
    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         array_push($return, true);
    //         array_push($return, $result);

    //     } 
    //     catch (PDOException $e) {

    //         array_push($return, false);
    //         array_push($return, $e);

    //     }
    //     finally {

    //         return $return;

    //     }

    // }

}


?>