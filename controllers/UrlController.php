<?php
require_once 'models/Url.php';

class UrlController {

    private $model;

    public function __construct($db) {
        $this->model = new Url($db);
    }

    public function shorten() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['url'])) {
            http_response_code(400);
            echo json_encode(["error" => "URL requerida"]);
            return;
        }

        $code = $this->model->create($data);

        if (isset($code['error'])) {
            http_response_code(400);
            echo json_encode($code);
            return;
        }

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "short_code" => $code,
            "short_url" => "http://localhost/$code"
        ]);
    }
}