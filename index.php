<?php
require_once 'config/database.php';
require_once 'controllers/UrlController.php';

$db = (new Database())->connect();
$controller = new UrlController($db);

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// 🔥 Quitar nombre de carpeta
$request = str_replace("/URLExam", "", $request);

// 🔥 Quitar index.php si viene en la URL
$request = str_replace("/index.php", "", $request);

// Debug temporal (puedes quitarlo después)
// echo "Ruta detectada: " . $request; exit;

if ($request === "/api/shorten" && $method === "POST") {
    header("Content-Type: application/json");
    $controller->shorten();
    exit;
}

elseif ($request !== "/" && $method === "GET") {
    // Redirección
    $code = ltrim($request, "/");

    if (preg_match('/^[a-zA-Z0-9]{5}$/', $code)) {

        $stmt = $db->prepare("SELECT * FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        $url = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$url) {
            http_response_code(404);
            echo "URL no encontrada";
            exit;
        }

        header("Location: " . $url['original_url'], true, 302);
        exit;
    }
}

http_response_code(404);
echo json_encode(["error" => "Endpoint no encontrado"]);