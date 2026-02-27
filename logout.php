<?php
session_start();
session_unset();    // Remove todas as variáveis da sessão
session_destroy();  // Destrói a sessão física no servidor

// Retorna uma confirmação para o AJAX ou redireciona
echo "sucesso";
exit;
?>