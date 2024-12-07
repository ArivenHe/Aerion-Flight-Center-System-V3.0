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
        $id = $data['id'];
        $cid = $data['cid'];
        $time=$data['time'];
        $manner=$data['manner'];
        if (isset($id, $cid)) {
            $insert_sql = $conn->prepare("INSERT INTO moment (cid,moment,time,manner) VALUES (?, ?, ?,?);");
            $insert_sql->bind_param("ssss", $cid, $time, $id,$manner);
            if ($insert_sql->execute()) {
                http_response_code(200);
                $data=array(
                    'cid'=>$cid,
                    'time'=>$time
                );
                echo json_encode(array('status' => '200', 'data' => $data));
                exit;
            } else {
                http_response_code(404);
                echo json_encode(array('status' => '404', 'data' => '报名失败'));
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
