<?php
require_once 'config/database.php';
require_once 'controllers/UrlController.php';

header("Content-Type: application/json");

$db = (new Database())->connect();
$controller = new UrlController($db);

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($request === "/api/shorten" && $method === "POST") {
    $controller->shorten();
}
else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint no encontrado"]);
}