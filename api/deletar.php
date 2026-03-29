<?php
// api/deletar.php

// Headers necessários para lidar com CORS e garantir que a resposta é JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Permite requisições preflight do CORS (OPTIONS) passarem
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Aceitamos POST ou DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido. Use POST.']);
    exit;
}

// Traz nossa conexão ativa
require_once __DIR__ . '/../config/database.php';

// Coletar ID do input
$id = $_POST['id'] ?? null;

// Se for enviando como JSON ou outro payload (fallback)
if (!$id) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['id'])) {
        $id = $data['id'];
    }
}

// Validação
if (empty($id)) {
    http_response_code(400); 
    echo json_encode(['sucesso' => false, 'erro' => 'ID não foi fornecido.']);
    exit;
}

try {
    // Validar se existe
    $sql_check = "SELECT id FROM gastos WHERE id = :id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_check->execute();
    
    if ($stmt_check->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'erro' => 'Gasto não encontrado.']);
        exit;
    }

    // Excluir
    $sql = "DELETE FROM gastos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Gasto deletado com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível deletar o gasto no banco.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro interno na query: ' . $e->getMessage()]);
}
