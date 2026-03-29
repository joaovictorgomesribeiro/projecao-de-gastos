<?php
// api/salvar.php

// Headers necessários para lidar com CORS e garantir que a resposta é JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Permite requisições preflight do CORS (OPTIONS) passarem de fininho
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Interrompe se o método não for POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido. Só aceitamos POST.']);
    exit;
}

// Traz nossa conexão ativa
require_once __DIR__ . '/../config/database.php';

// Coleta os dados que vieram do formulário FormData (por isso $_POST em vez de php://input)
// null como fallback evitará alertas de Undefined Index
$descricao = $_POST['descricao'] ?? null;
$valor = $_POST['valor'] ?? null;
$classificacao = $_POST['classificacao'] ?? null;
$parcela = $_POST['parcela'] ?? null;
$pagamento = $_POST['forma_pagamento'] ?? null; // Match com o "name" do input do frontend
$instituicao = $_POST['instituicao'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$data_gasto = $_POST['data'] ?? date('Y-m-d'); // Default para hoje se não enviado

// Validação dos nossos campos obrigatórios (front e back alinhados)
if (empty($descricao) || empty($valor) || empty($classificacao)) {
    http_response_code(400); // 400 Bad Request
    echo json_encode(['sucesso' => false, 'erro' => 'Campos obrigatórios ausentes: descrição, valor e/ou classificação.']);
    exit;
}

try {
    // Prepara o SQL (usar bindings evira que o backend sofra SQL Injection)
    $sql = "INSERT INTO gastos (descricao, valor, parcela, pagamento, instituicao, tipo, classificacao, data) 
            VALUES (:descricao, :valor, :parcela, :pagamento, :instituicao, :tipo, :classificacao, :data)";
            
    $stmt = $pdo->prepare($sql);
    
    // Bind dos parãmetros extraídos acima
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':parcela', $parcela);
    $stmt->bindParam(':pagamento', $pagamento);
    $stmt->bindParam(':instituicao', $instituicao);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':classificacao', $classificacao);
    $stmt->bindParam(':data', $data_gasto);
    
    // Finalmente, executa!
    if ($stmt->execute()) {
        http_response_code(201); // 201 Created
        echo json_encode([
            'sucesso' => true, 
            'mensagem' => 'Gasto salvo com sucesso!',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar o gasto no banco.']);
    }

} catch (PDOException $e) {
    http_response_code(500); // Qualquer falha a nível de DB é capturada aqui
    echo json_encode(['sucesso' => false, 'erro' => 'Erro interno na query: ' . $e->getMessage()]);
}
