<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtém as informações do usuário da sessão
$usuario = $_SESSION['usuario'];

// Conexão com o banco de dados
$servername = "localhost";
$username = "id21899766_birdbet";
$password = "!@#$%Anu,,..0880";
$database = "id21899766_birdbet";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta SQL para obter as informações do jogador
$sqlInfoJogador = "SELECT * FROM birdbet WHERE usuario = '$usuario'";
$resultInfoJogador = $conn->query($sqlInfoJogador);

if ($resultInfoJogador->num_rows > 0) {
    $row = $resultInfoJogador->fetch_assoc();
    $idUnico = $row['id-unico']; // ID único associado ao depósito
    $chavePix = $row['chave-pix']; // Chave PIX do jogador
    $nomeBanco = $row['nome-banco']; // Nome do banco do jogador
    $saldoJogador = $row['saldo-jogador']; // Saldo do jogador
   
} else {
    echo "Erro: Usuário não encontrado no banco de dados.";
    exit();
}

// Mensagem a ser enviada para o bot do Telegram
$message = "Novo ganhador!!!:\n";
$message .= "Usuário: $usuario\n";
$message .= "ID único: $idUnico\n";
$message .= "Chave PIX: $chavePix\n";
$message .= "Nome do Banco: $nomeBanco\n";
$message .= "Saldo do jogador: R$ " . number_format($saldoJogador, 2) . "\n";

// Função para enviar mensagem para o bot do Telegram
function enviarMensagemParaBot($message) {
    // Substitua 'SEU_TOKEN' pelo seu token do Telegram
    $token = "6682171731:AAFAwM70FFTvq8V2WYk6xtPTm84qzRC_hH0";
    $chat_id = "1435700139"; // Seu chat ID

    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($message);
    return file_get_contents($url);
}

// Envia as informações para o bot do Telegram
enviarMensagemParaBot($message);

// Zera o saldo do jogador
$sqlZerarSaldo = "UPDATE birdbet SET `saldo-jogador` = 0 WHERE usuario = '$usuario'";
if ($conn->query($sqlZerarSaldo) === TRUE) {
    // Redireciona para a página de perfil
    header("Location: perfil.php");
    exit();
} else {
    echo "Erro ao zerar o saldo do jogador: " . $conn->error;
}

$conn->close();
?>
