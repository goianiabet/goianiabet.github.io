// Função para fazer a solicitação AJAX
function carregarDados() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "https://handersbarol.000webhostapp.com/FlapyBirdBet/api.php", true); // URL da API no 000webhost
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Processa os dados retornados
            var data = JSON.parse(xhr.responseText);
            // Exemplo de processamento dos dados
            for (var i = 0; i < data.length; i++) {
                console.log(data[i]); // Exemplo: exibe os dados no console
            }
        }
    };
    xhr.send();
}

// Chama a função para carregar os dados quando a página é carregada
document.addEventListener("DOMContentLoaded", carregarDados);
