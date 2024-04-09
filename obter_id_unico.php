<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    echo "Usuário não autenticado";
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
    echo "Erro na conexão com o banco de dados: " . $conn->connect_error;
    exit();
}

// Consulta para obter o ID único do jogador
$sql = "SELECT `id-unico` FROM birdbet WHERE usuario = '{$_SESSION['usuario']}'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtém o ID único do jogador
    $row = $result->fetch_assoc();
    $id_unico = $row["id-unico"];
    echo $id_unico;
} else {
    echo "ID único não encontrado";
}

$conn->close();
?>
