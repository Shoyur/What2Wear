<?php 

// require_once 'tempModel.php';

date_default_timezone_set('US/Eastern');

class TempController {

    // CRUD

    // CREATE

    // READ
    public function getTemp() {

        $result = array();

        // response
        array_push($result, true);

        // delay
        array_push($result, 43);
        // array_push($result, time() - 143);

        // temp
        $temp = 15.94765;
        array_push($result, round($temp, 1));

        // height_red percent
        $height_red = ($temp + 40) / 0.8; // % mapped on -40C to 40C
        $height_red = 8 + ($height_red / 100) * (100 - 8); // % mapped on 8% to 100%
        $height_red = ceil($height_red); // remove decimals
        array_push($result, $height_red);

        // height_red percent
        $height_temp = (92 / (100 - 8)) * ($height_red - 8); // % mapped on 8% to 100%
        // $height_temp = ($height_red / 100) * (100 - 8); // % mapped on 8% to 100%
        $height_temp = ceil($height_temp);
        array_push($result, $height_temp);

        /*
        $tempModel = new TempModel();
        $result = $tempModel->getTemp();
        if ($result[0]) {
            foreach ($result[1] as &$order) {
                $update_time = strtotime($order['update_time']);
                $delay_sec = time() - $update_time;
                $order['delay'] = $delay_sec;
            }
        }
        */
        header('Content-Type: application/json');
        return json_encode($result);
    }

    // UPDATE
    public function updateTemp($temp) {
        $tempModel = new TempModel();
        $result = $tempModel->updateTemp($temp);
        header('Content-Type: application/json');
        return json_encode($result);
    }

}

// echo "WTFFFFFFF!!!!!!!!!!";
$tempController = new TempController();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET': {
        echo $tempController->getTemp();
        // echo "YEAH!";
        break;
    }
    case 'PATCH': {
        $data = json_decode(file_get_contents('php://input'), true);
        $temp = $data['temp'];
        break;
    }

}