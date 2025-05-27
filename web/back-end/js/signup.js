document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector("form");

  form.addEventListener("submit", async (event) => {
    event.preventDefault(); // Evita o envio automático

    try {
      // Gera o ID e aguarda
      const id = await criarID(10);
      
      if (id && id.success) {
        // Cria e adiciona input hidden ao form
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "id";
        hiddenInput.value = id.id;
        form.appendChild(hiddenInput);

        // Agora envia o formulário com o ID incluso
        form.submit();
      } else {
        alert("Erro ao gerar ID. Tente novamente.");
      }
    } catch (error) {
      console.error("Erro ao processar formulário:", error);
      alert("Ocorreu um erro ao processar o formulário. Tente novamente.");
    }
  });
});

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
