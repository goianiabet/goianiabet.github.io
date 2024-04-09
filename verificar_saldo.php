<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "id21899766_birdbet";
$password = "!@#$%Anu,,..0880";
$dbname = "id21899766_birdbet";

// Cria uma conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta para obter o saldo atual do jogador
$sql = "SELECT `saldo-jogador` FROM birdbet WHERE usuario = '{$_SESSION['usuario']}'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtém o saldo atual do jogador
    $row = $result->fetch_assoc();
    $saldo_atual = $row["saldo-jogador"];

    // Verifica se o saldo aumentou desde a última verificação
    if ($saldo_atual > $_SESSION["saldo_anterior"]) {
        // Atualiza a sessão com o novo saldo
        $_SESSION["saldo_anterior"] = $saldo_atual;
        echo "deposito_concluido";
    }
}

$conn->close();
?>
