<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Depósito</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('background.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        #mensagem {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        #qrcode, #brCodeFrame {
            width: 80%;
            max-width: 300px;
            height: auto;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        #brCodeFrame {
            height: 100px;
        }
    </style>
</head>
<body>
    <h1>Página de Depósito</h1>
    <p id="mensagem">Aguardando depósito...</p>
    
    <!-- Adiciona o elemento img para exibir o código QR -->
    <img id="qrcode" src="" alt="Código QR">

    <!-- Adiciona o elemento iframe para exibir o BR Code -->
    <iframe id="brCodeFrame" src=""></iframe>

    <p style="color: #fff; text-align: center; font-size: 18px; margin-top: 20px;">Para depositar, siga os passos abaixo:</p>
    <ol style="color: #fff; text-align: left; font-size: 16px;">
        <li>Abra o aplicativo do seu banco.</li>
        <li>Selecione a opção de pagamento via Pix.</li>
        <li>Escanee o QR Code abaixo ou copie o BR Code.</li>
        <li>Confirme o pagamento.</li>
    </ol>

    <script>
        // Função para obter o ID único do jogador do banco de dados
        function obterIDUnico() {
            // Realiza uma solicitação AJAX para obter o ID único do jogador
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "obter_id_unico.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var id_unico = xhr.responseText;
                    // Chama a função para gerar o QR Code com o ID único obtido
                    gerarQRCode(id_unico);
                    // Atualiza o src do iframe para exibir o BR Code
                    var brCodeUrl = "https://gerarqrcodepix.com.br/api/v1?nome=Eduardo&cidade=Goiania&chave=84ffeadb-9f1c-4460-8356-6cd5c60b7e00&txid=" + id_unico + "&saida=br";
                    document.getElementById("brCodeFrame").src = brCodeUrl;
                    // Inicia a verificação do depósito
                    verificarDeposito(id_unico);
                }
            };
            xhr.send();
        }

        // Função para gerar o código QR com o ID único
        function gerarQRCode(id_unico) {
            // Define a URL para gerar o código QR com o ID único obtido
            var qrUrl = "https://gerarqrcodepix.com.br/api/v1?nome=Eduardo&cidade=Goiania&descricao=Pagamento%20da%20compra&chave=d9367b67-9648-49c2-b12a-34746c076fa9&saida=qr&txid=" + id_unico;
            // Atualiza a src da imagem para exibir o código QR
            document.getElementById("qrcode").src = qrUrl;
        }

        // Função para verificar se o depósito foi concluído
        function verificarDeposito(id_unico) {
            // Função aninhada para realizar a verificação
            function verificar() {
                // Realiza uma solicitação AJAX para verificar o depósito
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "verificar_saldo.php?id=" + id_unico, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response.trim() == "deposito_concluido") {
                            // Atualiza a mensagem e redireciona para perfil.php
                            document.getElementById("mensagem").innerHTML = "Depósito concluído com sucesso.";
                            clearInterval(interval); // Para a verificação
                            setTimeout(function() {
                                window.location.href = "perfil.php";
                            }, 3000); // Redireciona após 3 segundos
                        }
                    }
                };
                xhr.send();
            }
            
            // Inicia a verificação a cada 5 segundos (5000 milissegundos)
            var interval = setInterval(verificar, 5000);
        }

        // Função para extrair o BR code após o carregamento do iframe e exibir em um alert
        function extrairBRCode() {
            var brCodeContent = document.getElementById("brCodeFrame").contentWindow.document.body.innerText;
            alert("BR Code gerado:\n" + brCodeContent);
        }

        // Chama a função obterIDUnico para iniciar o processo
        obterIDUnico();

        // Adiciona um evento de carga ao iframe para extrair o BR code
        document.getElementById("brCodeFrame").addEventListener("load", extrairBRCode);
    </script>
</body>
</html>
