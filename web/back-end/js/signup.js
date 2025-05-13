async function criarID(tamanho) {

  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let resultado = "";

  for (let i = 0; i < tamanho; i++) 
  {

    resultado += chars.charAt(Math.floor(Math.random() * chars.length));

    if (resultado.length == tamanho - 3) 
    {

      resultado += "-";

    }

  }

  if (resultado.length = tamanho + 1) 
  {

    document.write(resultado)

  }

  id = resultado;

  fetch('../../html/login1.php',{

    method: POST,
    headers: {
      "Content-Type": "application/json",
    }

  })

  


}