<?php
// api/listar.php

// Headers CORS para permitir acesso local e declarar o JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido. Use GET.']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    // Buscamos tudo da tabela gastos ordernados pela data (com o mais novo acima)
    $sql = "SELECT * FROM gastos ORDER BY data DESC, created_at DESC";
    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll();

    // Se der bom, retorna a array direto e o Javascript fará o resto!
    http_response_code(200);
    echo json_encode([
        'sucesso' => true,
        'total' => count($resultados),
        'dados' => $resultados
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha ao buscar os gastos: ' . $e->getMessage()]);
}
