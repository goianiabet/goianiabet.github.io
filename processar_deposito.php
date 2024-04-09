<?php
session_start();

// Verifica se a solicitação é uma solicitação POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os parâmetros esperados foram enviados
    if (isset($_POST["id-unico"], $_POST["valor-deposito"])) {
        $idUnicoRecebido = $_POST["id-unico"];
        $valorDepositoRecebido = $_POST["valor-deposito"];

        // Conexão com o banco de dados
        $servername = "localhost";
        $username = "id21899766_birdbet";
        $password = "!@#$%Anu,,..0880";
        $database = "id21899766_birdbet";

        $conn = new mysqli($servername, $username, $password, $database);

        // Verifica se a conexão foi bem sucedida
        if ($conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Query SQL para atualizar o saldo do jogador
        $sqlAtualizarSaldo = "UPDATE birdbet SET `saldo-jogador` = `saldo-jogador` + $valorDepositoRecebido WHERE `id-unico` = '$idUnicoRecebido'";

        if ($conn->query($sqlAtualizarSaldo) === TRUE) {
            echo "Saldo do jogador atualizado com sucesso.";
        } else {
            echo "Erro ao atualizar saldo do jogador: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Erro: Parâmetros esperados não foram enviados.";
    }
} else {
    echo "Erro: Requisição deve ser feita via POST.";
}
?>
