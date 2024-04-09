<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

$usuario = $_SESSION['usuario'];

// Configurações do banco de dados
$servername = "localhost";
$username = "id21899766_birdbet";
$password = "!@#$%Anu,,..0880";
$dbname = "id21899766_birdbet"; // Corrigindo o nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta SQL para obter todas as somas dos saldos acumulados dos usuários
$sql_soma = "SELECT SUM(`saldo-acumulado`) AS soma_total FROM birdbet";
$result_soma = $conn->query($sql_soma);

if ($result_soma->num_rows > 0) {
    $row_soma = $result_soma->fetch_assoc();
    $premioAcumulado = $row_soma['soma_total']; // Obtém a soma total dos saldos acumulados

    // Verifica se a soma total é nula e define como zero se necessário
    if ($premioAcumulado === null) {
        $premioAcumulado = 0;
    }

    $premioAcumuladoFormatado = number_format($premioAcumulado, 2, ',', '.');
} else {
    echo "Erro: Não foi possível obter a soma total dos saldos acumulados.";
}

// Consulta SQL para obter as informações do usuário, incluindo o saldo acumulado
$sql = "SELECT * FROM birdbet WHERE usuario = '$usuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nomeBanco = $row['nome-banco'];
    $chavePix = $row['chave-pix'];
    $idUnico = $row['id-unico'];
    $saldoJogador = $row['saldo-jogador'];
    $saldoAcumulado = $row['saldo-acumulado']; // Obtém o saldo acumulado do banco de dados

    // Verifica se o saldo acumulado é nulo e define como zero se necessário
    if ($saldoAcumulado === null) {
        $saldoAcumulado = 0;
    }

    // Verifica se o saldo do jogador é menor que zero
    if ($saldoJogador < 0) {
        // Atualiza o saldo para zero no banco de dados
        $sqlAtualizarSaldo = "UPDATE birdbet SET `saldo-jogador` = 0 WHERE usuario = ?";
        $stmtAtualizarSaldo = $conn->prepare($sqlAtualizarSaldo);
        $stmtAtualizarSaldo->bind_param("s", $usuario);

        if ($stmtAtualizarSaldo->execute()) {
            // Atualização bem-sucedida
            $saldoJogador = 0; // Atualiza o saldo na variável local
            echo "Seu saldo foi atualizado para zero.";
        } else {
            // Erro na atualização do saldo
            echo "Erro ao atualizar o saldo no banco de dados.";
        }

        $stmtAtualizarSaldo->close();
    }

    // Verifica se o saldo do jogador é menor ou igual a 9 centavos
    if ($saldoJogador <= 0.09) {
        // Desabilita o jogo
        $jogoHabilitado = false;
    } else {
        // Saldo positivo, jogo habilitado
        $jogoHabilitado = true;
    }

    $saldoFormatado = number_format($saldoJogador, 2, ',', '.');
    $saldoAcumuladoFormatado = number_format($premioAcumulado, 2, ',', '.'); // Corrigido para exibir a soma correta de saldos acumulados
} else {
    echo "Erro: Usuário não encontrado no banco de dados.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BirdBet - Chegue no lvl 40 para ganhar o prêmio acumulado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('background.jpg'); /* Adicionando background */
            background-size: cover; /* Ajustando para cobrir toda a tela */
            margin: 0;
            padding: 0;
        }

        .container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.8); /* Adicionando fundo branco com transparência */
    border-radius: 10px; /* Adicionando bordas arredondadas */
    margin-bottom: 50px; /* Adicionando margem inferior para esticar o contêiner para baixo */
}


        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        .popup {
            position: absolute;
            background-color: #fff;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .popup h2 {
            margin-top: 0;
            color: #333;
        }

        .popup p {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #999;
            text-decoration: none;
            font-size: 24px;
        }

        .game-container {
            text-align: center;
            margin-top: 20px;
        }

        canvas {
            border: 1px solid #333;
            border-radius: 5px;
        }

        .info {
            margin-top: 20px;
            text-align: center;
        }

        .buttons {
            margin-top: 20px;
            text-align: center;
        }

        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .logout {
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout:hover {
            background-color: #c82333;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .popup {
                width: 90%;
            }
        }
    /*    
    @media screen and (max-width: 600px) {
    .buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    */
    
    

    .button, .logout {
        margin-bottom: 10px;
    }
}

        
        
    </style>
</head>
<body>
    <div class="container">
       

        <div id="profile-info" class="overlay">
            <div class="popup">
                <h2>Alcance o level 40 para ganhar o prêmio acumulado</h2>
                <p><strong>Usuário:</strong> <?php echo $usuario; ?></p>
                <p><strong>Nome do Banco:</strong> <?php echo $nomeBanco; ?></p>
                <p><strong>Chave PIX:</strong> <?php echo $chavePix; ?></p>
                <p><strong>Saldo do Jogador:</strong> R$ <?php echo $saldoFormatado; ?></p>
                <p><strong>Premio Acumulado:</strong> R$ <?php echo $premioAcumuladoFormatado; ?></p>
                <p><strong>ID-Único:</strong> <?php echo $idUnico; ?></p>
                <a class="close" href="#" onclick="closePopup()">&times;</a>
            </div>
        </div>

        <div class="game-container">
            <?php if ($jogoHabilitado): ?>
                <canvas id="bird" width="320" height="480"></canvas>
                <script src="game.js"></script>
                <script>
                    // Verifica se o dispositivo é um dispositivo móvel
                    if (/Mobi|Android/i.test(navigator.userAgent)) {
                        // Função para colocar o jogo em tela cheia
                        function toggleFullscreen() {
                            var canvas = document.getElementById('bird');
                            if (!document.fullscreenElement) {
                                canvas.requestFullscreen();
                            } else {
                                if (document.exitFullscreen) {
                                    document.exitFullscreen();
                                }
                            }
                        }

                        // Coloca o jogo em tela cheia automaticamente
                        toggleFullscreen();
                    }
                </script>
            <?php else: ?>
                <p style="color: red;">Saldo insuficiente para jogar. Por favor, faça um depósito.</p>
            <?php endif; ?>
        </div>

        <div class="info">
            <p><strong>Saldo do Jogador:</strong> R$ <?php echo $saldoFormatado; ?></p>
            <p><strong>Premio Acumulado:</strong> R$ <?php echo $saldoAcumuladoFormatado; ?></p> <!-- Corrigido para exibir a soma correta de saldos acumulados -->
        </div>

        <div class="buttons">
    <a href="#" class="button" onclick="openPopup()">Ver Perfil</a>
    <a href="deposito.php" class="button">Depositar</a>
    <!--<a href="enviar_infos_telegram.php" class="button">Sacar</a>
    -->
    <a href="logout.php" class="logout">Sair</a>
</div>

    </div>

    <script>
        function openPopup() {
            document.getElementById("profile-info").style.display = "block";
        }

        function closePopup() {
            document.getElementById("profile-info").style.display = "none";
        }
    </script>
</body>
</html>