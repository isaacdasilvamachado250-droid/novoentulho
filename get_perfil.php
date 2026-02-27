<?php
session_start();
include 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Não logado']);
    exit;
}

$id = $_SESSION['usuario_id'];

//Pega dados do usuário
$res = $conn->query("SELECT nome, email, telefone, perfil_usuario FROM usuarios WHERE id = $id");
$user = $res->fetch_assoc();

//Pega os anúncios desse usuário (Vendas)
// Se você quiser compras, precisaria daquela tabela extra, mas vamos focar no que já tem:
$resAnuncios = $conn->query("SELECT titulo, valor, imagem_path FROM anuncio WHERE id_usuario = $id ORDER BY create_date DESC");
$historico = [];
while($row = $resAnuncios->fetch_assoc()) {
    $historico[] = $row;
}

//Junta tudo e entrega pro AJAX
echo json_encode([
    'nome' => $user['nome'],
    'email' => $user['email'],
    'telefone' => $user['telefone'],
    'perfil' => $user['perfil_usuario'],
    'historico' => $historico
]);
?>