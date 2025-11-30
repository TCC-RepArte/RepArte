<?php
/**
 * Script para adicionar VLibras em todas as páginas principais
 * Este arquivo deve ser incluído no final de cada página, antes do </body>
 */

// Inclui o arquivo de configuração do VLibras
require_once __DIR__ . '/php/vlibras_config.php';

// Renderiza o VLibras se estiver ativo
renderizarVLibras();
?>