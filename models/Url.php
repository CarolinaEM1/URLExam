<?php
class Url {

    private $conn;
    private $table = "urls";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateCode($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters)-1)];
            }

            $stmt = $this->conn->prepare("SELECT id FROM urls WHERE short_code = ?");
            $stmt->execute([$code]);

        } while($stmt->rowCount() > 0);

        return $code;
    }

    public function create($data) {

        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            return ["error" => "URL inválida"];
        }

        $code = $this->generateCode();

        $stmt = $this->conn->prepare("
            INSERT INTO urls (original_url, short_code, expiration_date, max_uses, creator_ip)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['url'],
            $code,
            $data['expiration_date'] ?? null,
            $data['max_uses'] ?? null,
            $_SERVER['REMOTE_ADDR']
        ]);

        return $code;
    }
}