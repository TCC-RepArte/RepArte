document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector("#loginf");

  form.addEventListener("submit", async (event) => {
    event.preventDefault(); // Evita o envio automático

    // Gera o ID e aguarda
    const id = await criarID(10);
    
    if (id.success) {
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

  try {
    const response = await fetch('/web/back-end/php/signup.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });

    const data = await response.json();

    if (data.duplicate) {
      console.warn("ID duplicado. Tentando novamente...");
      return await criarID(tamanho); // Recursivo até gerar único
    }

    return data;

  } catch (error) {
    console.error("Erro ao gerar ID:", error);
    return { success: false };
  }
}
