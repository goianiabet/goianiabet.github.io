<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtém o usuário da sessão
$usuario = $_SESSION['usuario'];

// Verifica se o formulário foi submetido e se o valor foi definido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["valor"])) {
    // Obtém o valor e a ação (adicionar, subtrair ou ganhar)
    $valor = $_POST["valor"];
    $acao = $_POST["acao"];

        // Configurações do banco de dados
    $servername = "localhost";
    $username = "id21899766_birdbet";
    $password = "!@#$%Anu,,..0880";
    $dbname = "id21899766_birdbet"; // Corrigindo o nome do banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se a conexão foi bem-sucedida
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Atualiza o saldo com base na ação selecionada
    if ($acao == "adicionar") {
        // Se for uma adição, atualiza apenas o saldo do jogador
        $sql = "UPDATE birdbet SET `saldo-jogador` = `saldo-jogador` + $valor WHERE usuario = '$usuario'";
    } elseif ($acao == "subtrair") {
        // Se for uma subtração, atualiza o saldo do jogador e subtrai o valor do saldo acumulado
        $sql = "UPDATE birdbet SET `saldo-jogador` = `saldo-jogador` - $valor WHERE usuario = '$usuario'";
        
        // Adiciona metade do valor apostado ao saldo acumulado
        $valor_para_premio = $valor / 2;
        $sql_adicionar_acumulado = "UPDATE birdbet SET `saldo-acumulado` = `saldo-acumulado` + $valor_para_premio WHERE usuario = '$usuario'";
        $conn->query($sql_adicionar_acumulado);
    } elseif ($acao == "ganhar") {
        // Consulta SQL para obter a soma de todos os saldos acumulados dos jogadores
        $sql_soma_acumulados = "SELECT SUM(`saldo-acumulado`) AS total_acumulado FROM birdbet";
        $result_soma_acumulados = $conn->query($sql_soma_acumulados);
        $row_soma_acumulados = $result_soma_acumulados->fetch_assoc();
        $premio_acumulado = $row_soma_acumulados['total_acumulado'];

        // Transfere o saldo acumulado para o saldo do jogador vencedor
        $sql_transferir_premio = "UPDATE birdbet SET `saldo-jogador` = `saldo-jogador` + $premio_acumulado WHERE usuario = '$usuario'";
        $conn->query($sql_transferir_premio);

        // Zera o saldo acumulado de todos os jogadores
        $sql_zerar_acumulados = "UPDATE birdbet SET `saldo-acumulado` = 0";
        $conn->query($sql_zerar_acumulados);

        // Notifica os jogadores sobre o vencedor
        $mensagem = "Parabéns! O jogador $usuario ganhou o prêmio acumulado de R$ $premio_acumulado!";
        // Exemplo de como enviar notificação por e-mail ou outro método de comunicação
        
       


        // Redireciona de volta para a página de perfil
        //header("Location: perfil.php");
        exit();
    }

    // Executa a consulta SQL
    if ($conn->query($sql) === TRUE) {
        // Redireciona de volta para a página de perfil
       // header("Location: perfil.php");
        exit();
    } else {
        echo "Erro ao processar a atualização do saldo: " . $conn->error;
    }

    $conn->close();
}
?>
