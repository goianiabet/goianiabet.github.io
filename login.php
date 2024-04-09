<?php
session_start();

// Verifica se foi submetido um formulário de login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loginSubmit"])) {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    // Configurações do banco de dados
    $servername = "localhost";
    $username = "id21899766_birdbet";
    $password = "!@#$%Anu,,..0880";
    $dbname = "id21899766_birdbet"; // Corrigindo o nome do banco de dados

    // Conecta ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Consulta preparada para verificar as credenciais do usuário
    $sql = "SELECT * FROM birdbet WHERE usuario = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Login bem-sucedido, redireciona para a página do perfil
        $_SESSION['usuario'] = $usuario;
        header("Location: perfil.php");
        exit();
    } else {
        // Login falhou, exibe mensagem de erro com botão "OK"
        echo "<script>alert('Credenciais inválidas'); window.location.href = 'index.html';</script>";
        // Não redireciona imediatamente, permitindo que o usuário veja a mensagem de alerta e clique em "OK"
    }
    
    $stmt->close();
    $conn->close();
}
?>
