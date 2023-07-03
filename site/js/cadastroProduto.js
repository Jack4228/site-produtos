document.getElementById('productForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var product = {
        name: document.getElementById('name').value,
        price: parseFloat(document.getElementById('price').value),
        description: document.getElementById('description').value
    };

    var token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwibm9tZSI6IkpvXHUwMGUzbyIsImVtYWlsIjoiam9hb0BleGFtcGxlLmNvbSIsIm5hc2NpbWVudG8iOiIxOTkwLTAxLTAxIiwiYWRtaW4iOjF9.Ldgx-ud3bStSd6HAAnjsUVNZFsovbnAj25VasnUKWTw'; 

    fetch('http://localhost/api/produto/create.php', {
  method: 'POST',
  headers: {
    'Content-type': 'application/json',
    'Authorization': 'Bearer ' + token
  },
  body: JSON.stringify(product)
})
  .then(response => {
    if (response.ok) {
      return response.json();
    } else {
      throw new Error('Erro ao cadastrar produto');
    }
  })
  .then(data => {
    alert('Produto cadastrado com sucesso!');
    // Faça o que for necessário com a resposta (data) aqui
  })
  .catch(error => {
    alert('Erro ao cadastrar produto: ' + error.message);
  });
});