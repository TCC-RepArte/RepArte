async function criarID(tamanho) {

  //todos os carcteres que podem ser selecnados no ID
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  // vase contru pela varávelresultado
  let resultado = "";

  for (let i = 0; i < tamanho; i++) {

    resultado += chars.charAt(Math.floor(Math.random() * chars.length));

    if (resultado.length == tamanho - 3) {
      resultado += "-";
    }

  }

  let id = resultado;

  try {

    const response = await fetch('/web/back-end/php/signup.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: id })
    });

    const data = await response.json();
    console.log('Resposta do servidor:', data);
    return data;

  } catch (error) {
    console.error('Erro ao enviar ID:', error);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  criarID(10); // Ou qualquer tamanho desejado
});