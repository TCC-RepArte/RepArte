// Não adicionar o listener de evento aqui, pois já temos um personalizado no cadastro.php
// O código abaixo será usado pela função em cadastro.php

async function criarID(tamanho) {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let resultado = "";

  for (let i = 0; i < tamanho; i++) {
    resultado += chars.charAt(Math.floor(Math.random() * chars.length));
    if (resultado.length == tamanho - 3) resultado += "-";
  }

  const id = resultado;
  console.log("ID gerado:", id);

  try {
    const url = '../back-end/php/signup.php';
    console.log("Tentando acessar:", url);

    const response = await fetch(url, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id })
    });

    console.log("Status da resposta:", response.status);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    // Verificar o tipo de conteúdo retornado
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      console.error("Resposta não é JSON:", await response.text());
      throw new Error('Resposta do servidor não é JSON válido');
    }

    const data = await response.json();
    console.log("Resposta do servidor:", data);

    if (data.duplicate) {
      console.warn("ID duplicado. Tentando novamente...");
      return await criarID(tamanho); // Recursivo até gerar único
    }

    return data;

  } catch (error) {
    console.error("Erro ao gerar ID:", error);
    return { success: false, message: "Erro ao comunicar com o servidor" };
  }
}
