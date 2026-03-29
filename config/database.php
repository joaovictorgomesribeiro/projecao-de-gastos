<?php
// config/database.php
$host = 'localhost';
$dbname = 'projetogastos';
$user = 'root'; // Ajuste conforme seu setup (ex: root no XAMPP/WAMP)
$pass = '';     // Ajuste conforme seu setup (ex: vazio no XAMPP, ou 'root' no MAMP)

try {
    // Configuração do DSN para conectar ao banco MySQL
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    
    // Configura o PDO para lançar exceções em caso de erro (Segurança/Debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura o retorno padrão do fetch como array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Se não conseguir conectar, retorna um erro em JSON com log da falha
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);
    echo json_encode([
        'sucesso' => false, 
        'erro' => 'Erro de conexão com o banco de dados. Verifique as credenciais no config/database.php.'
    ]);
    exit;
}
