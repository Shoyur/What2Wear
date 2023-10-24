<?php 

require_once 'tempModel.php';

date_default_timezone_set('US/Eastern');

class TempController {

    // CRUD

    // CREATE
    public function postTemp($temp) {
        $tempModel = new TempModel();
        $tempModel->postTemp($temp);
    }

    // READ
    public function getTemp() {

        $tempModel = new TempModel();
        $result = $tempModel->getTemp();

        $return = array();

        if ($result[0]) {

            array_push($return, true);

            // delay
            $delay = time() - strtotime($result[1]['temp1_time']);
            $hours = floor($delay / 3600);
            $minutes = floor(($delay % 3600) / 60);
            $seconds = $delay % 60;
            $delay_string = '';
            if ($hours > 0) { $delay_string .= $hours . ' hour '; }
            if ($minutes > 0) { $delay_string .= $minutes . ' min '; }
            $delay_string .= $seconds . ' sec';
            array_push($return, $delay_string);

            // temp
            $temp = $result[1]['temp1_value'];
            array_push($return, round($temp, 1));

            // height_red percent
            $height_red = ($temp + 40) / 0.8; // % mapped on -40C to 40C
            $height_red = 8 + ($height_red / 100) * (100 - 8); // % mapped on 8% to 100%
            $height_red = ceil($height_red); // remove decimals
            array_push($return, $height_red);

            // height_temp percent
            $height_temp = (92 / (100 - 8)) * ($height_red - 8); // % mapped on 8% to 100%
            // $height_temp = ($height_red / 100) * (100 - 8); // % mapped on 8% to 100%
            $height_temp = ceil($height_temp);
            array_push($return, $height_temp);

        }
        else {
            array_push($return, false);
            array_push($return, $result[1]);
        }

        header('Content-Type: application/json');
        return json_encode($return);
    }

}

$tempController = new TempController();

switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST': {
        $temp = isset($_POST['temp']) ? floatval($_POST['temp']) : 0;
        echo $tempController->postTemp($temp);
        break;
    }
    case 'GET': {
        echo $tempController->getTemp();
        break;
    }

}