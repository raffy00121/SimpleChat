<?php

Class Database {
    private $con;

    function __construct() {
        $this->con = $this->connect();
    }

    private function connect() {
        $info = "mysql:host=localhost;dbname=simple_chat";
        try {
            return new PDO($info, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function write($query, $data_array=[]) { // if no data, the default is empty array
        $con = $this->connect();
        $stmt = $con->prepare($query);
        $check = $stmt->execute($data_array);
        if ($check) {
            return true;
        }
        return false;
    }

    public function read($query, $data_array=[]) { // if no data, the default is empty array
        $con = $this->connect();
        $stmt = $con->prepare($query);
        $check = $stmt->execute($data_array);
        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result; // return the result object
            }
            return false;
        }
        return false;
    }

    public function generate_id($max) {
        $generated_id = 0;
        for ($i = 0; $i < 10; $i++) {
            $generated_id += rand(4, $max);
        }
        return $generated_id;
    }
}