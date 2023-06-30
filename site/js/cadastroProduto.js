document.getElementById('productForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var product = {
        name: document.getElementById('name').value,
        price: parseFloat(document.getElementById('price').value),
        description: document.getElementById('description').value
    };

    var token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6Miwibm9tZSI6Ik1hcmlhIiwiZW1haWwiOiJtYXJpYUBleGFtcGxlLmNvbSIsIm5hc2NpbWVudG8iOiIxOTkyLTA1LTE1IiwiYWRtaW4iOjB9.biimT5uZ-6DSAvrngnoaibH3KCf9U1Dd60JszTWy2UU'; // Substitua pelo seu token válido

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://localhost/api/produto/create.php', true);
    xhr.setRequestHeader('Content-type', 'application/json');
    xhr.setRequestHeader('Authorization'," Bearer:"+ token);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                alert('Produto cadastrado com sucesso!');
               
            } else {
                var response = JSON.parse(xhr.responseText);
                alert('Erro ao cadastrar produto: ' + response.message);
            }
        }
    };

    xhr.send(JSON.stringify(product));
});