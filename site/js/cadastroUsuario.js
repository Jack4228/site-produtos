document.getElementById('userForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var name = document.getElementById('nome').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('senha').value;
    var date = document.getElementById('nascimento').value;

    var user = {
        nome: name,
        email: email,
        senha: password,
        nascimento: date
    };
console.log(user)
    var jsonData = JSON.stringify(user);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/usuario/create.php', true);
    xhr.setRequestHeader('Content-type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            // Lógica para lidar com a resposta do PHP (se necessário)
            console.log(response);

            alert("Usuário adicionado com sucesso!")
            window.location.href = '/site/index.html';
        } else {
            // Lógica para lidar com erros (se necessário)
            console.error('Erro: ' + xhr.status);
            alert("Erro ao adicionar usuário")
        }
    };
    xhr.send(jsonData);
});