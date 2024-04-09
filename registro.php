<?php
session_start();

// Configurações do banco de dados
$servername = "localhost";
$username = "id21899766_birdbet";
$password = "!@#$%Anu,,..0880";
$database = "id21899766_birdbet";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registroSubmit"])) {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];
    $chavePix = $_POST["chavePix"];
    $nomeBanco = $_POST["nomeBanco"];

    $idUnico = uniqid();

    // Consulta preparada para verificar se o usuário já existe
    $sql_check_user = "SELECT * FROM birdbet WHERE usuario = ?";
    $stmt_check_user = $conn->prepare($sql_check_user);
    $stmt_check_user->bind_param("s", $usuario);
    $stmt_check_user->execute();
    $result_check_user = $stmt_check_user->get_result();

    if ($result_check_user->num_rows > 0) {
        $_SESSION['registration_error'] = 'Usuário já existe. Escolha outro nome de usuário.';
        echo "<script>alert('Usuário já existe. Escolha outro nome de usuário.'); window.location.href = 'index.html';</script>";
    } else {
        // Consulta preparada para inserir o novo usuário
        $sql_insert_user = "INSERT INTO birdbet (usuario, senha, `id-unico`, `chave-pix`, `nome-banco`, `saldo-jogador`, `saldo-acumulado`, `saldo-dono-jogo`) 
                            VALUES (?, ?, ?, ?, ?, 1, 0, 0)";
        $stmt_insert_user = $conn->prepare($sql_insert_user);
        $stmt_insert_user->bind_param("sssss", $usuario, $senha, $idUnico, $chavePix, $nomeBanco);

        if ($stmt_insert_user->execute()) {
            $_SESSION['registration_success'] = 'Registro bem-sucedido!';
            echo "<script>alert('Registro bem-sucedido!'); window.location.href = 'index.html';</script>";
        } else {
            $_SESSION['registration_error'] = 'Erro ao registrar: ' . $conn->error;
            echo "<script>alert('Erro ao registrar: " . $conn->error . "');</script>";
        }
    }
    $stmt_check_user->close();
    $stmt_insert_user->close();
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
