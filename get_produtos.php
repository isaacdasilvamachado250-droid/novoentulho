<?php
// Desativa a exibição de erros de texto que "sujam" o JSON
error_reporting(0);
ini_set('display_errors', 0);

include 'conexao.php';

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if ($categoria != '') {
    //Busca apenas produtos daquela categoria
    $stmt = $conn->prepare("SELECT * FROM anuncio WHERE categoria = ? AND (status IS NULL OR status != 'vendido') ORDER BY id DESC");
    $stmt->bind_param("s", $categoria);
} else {
    //Se não vier categoria, busca tudo (para a Home, por exemplo)
    $stmt = $conn->prepare("SELECT * FROM anuncio ORDER BY id DESC");
}

$stmt->execute();
$resultado = $stmt->get_result();
$produtos = [];

while ($linha = $resultado->fetch_assoc()) {
    $produtos[] = $linha;
}
header('Content-Type: application/json'); // Avisa o navegador que é um JSON
echo json_encode($produtos);
exit;
?>