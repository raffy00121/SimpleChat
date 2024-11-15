<?php

$info = (Object)[]; // this info object will be the base of the respose to ajax

global $DATA_OBJ, $DB, $Error;

$data = false;
$data['username'] = $DATA_OBJ->username;

if ($DATA_OBJ->username == "") {
    $Error .= "Username is required";
}
if ($DATA_OBJ->password == "") {
    $Error .= "Password is required";
}

if ($Error == "") {

    $query = "select * from users where username = :username limit 1"; // to get the user first
    $result = $DB->read($query, $data);

    if (is_array($result)) {
        $result = $result[0]; // get the first result (array)
        if ($result['password'] == $DATA_OBJ->password) {

        } else {
            $info->message = "Wrong password";
            $info->data_type = "error";
            echo json_encode($info);
        }
    } else {
        $info->message = "Wrong username";
        $info->data_type = "error";
    }

} else {
    $info->message = $Error;
    $info->data_type = "error";
}
echo json_encode($info);
