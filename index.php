<?php

$api_url = 'http://s974927839.online-home.ca/What2Wear/server/tempController.php';

$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

$data = json_decode($response, true);

$delay = "???";
$temp = "???";
$height_red = 0;
$height_temp = 0;

if ($data[0] === true) {
    $delay = $data[1];
    $temp = $data[2];
    $height_red = $data[3];
    $height_temp = $data[4];
}
elseif ($data[0] === false) {
    $temp = $response[1];
}
else {
    $temp = "No valid answer from the server";
}

curl_close($ch);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>What2Wear (temperature logging)</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        body {
            height: 100vh;
            width: 100vw;
            background-color: #333333;
            font-family: 'Roboto';
        }
        .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .error {
            color: #DD1100;
            position: fixed;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .text {
            margin-bottom: 30px;
            color: #EEEEEE;
        }
        .boxed-text {
            display: inline-block;
            border: 2px solid #EEEEEE;
            padding: 5px;
        }
        .scale {
            position: absolute;
            right: 60px;
            color: #EEEEEE;
            height: 350px;
        }
        .thermometer {
            position: relative;
            height: 450px;
            width: 100px;
        }
        .bar {
            z-index: 1;
            height: 350px;
            width: 40px;
            background-color: #EEEEEE;
            border-radius: 25px 25px 0px 0px;
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
        }
        .circle {
            height: 100px;
            width: 100px;
            background-color: #EEEEEE;
            position: absolute;
            bottom: 0;
            border-radius: 50%;
        }
        .circle::after {
            content: "";
            display: block;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #DD1100;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .bar::after {
            content: "";
            display: block;
            height: <?php echo $height_red; ?>%;
            width: 20px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -18px;
            border-radius: 6px 6px 0px 0px;
            background-color: #DD1100;
        }
        .temp {
            position: absolute;
            bottom: <?php echo $height_temp; ?>%;
            left: 60px;
            color: #EEEEEE;
        }
        .scale-text {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            line-height: 40px;
        }
        .unit {
            position: absolute;
            bottom: -45px;
            right: 80px;
            color: #EEEEEE;
        }
    </style>
</head>
<body>
    <section class="container">
        <h4 class="text">
            <span class="error">TEST</span><br>
            <span class="boxed-text">ESP32</span> → <?php echo $delay;?> sec ago → 
            <span class="boxed-text">server</span> → now → 
            <span class="boxed-text">you</span>
        </h4>
        <div class="thermometer">
            <div class="bar">
                <div class="scale">
                    <div class="scale-text">40<br>30<br>20<br>10<br>0<br>-10<br>-20<br>-30<br>-40
                    </div>
                </div>
                <div class="temp"><?php echo $temp;?>°C</div>
            </div>
            <div class="circle"></div>
        </div>
    </section>
</body>
</html>