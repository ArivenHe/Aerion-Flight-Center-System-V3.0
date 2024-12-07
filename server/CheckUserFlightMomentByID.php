<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include "setting.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
    $token = hash('sha256', $token);
    if ($token == $token_Correct) {
        /** 获取数据 **/
        $cid = $data['cid'];
        $time = $data['time'];
        if (isset($cid, $time)) {
            $sql = $conn->prepare("SELECT * FROM moment WHERE cid=? AND  time =?");
            $sql->bind_param("ss", $cid, $time);
            $sql->execute();
            $result = $sql->get_result();
            $data = array(
                'cid' => $cid,
                'time' => $time
            );
            if ($result->num_rows <= 0) {
                http_response_code(200);
                echo json_encode(array('status' => '200', 'data' => $data));
                exit;
            } else {
                http_response_code(202);
                echo json_encode(array('status' => '202', 'data' => $data));
                exit;
            }
        } else {
            http_response_code(300);
            echo json_encode(array('status' => '300', 'code' => '缺少必要的值'));
            exit;
        }
    } else {
        http_response_code(505);
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }
} else {
    http_response_code(500);
    echo json_encode(array('status' => '500', 'code' => '错误提交方法'));
    exit;
}

