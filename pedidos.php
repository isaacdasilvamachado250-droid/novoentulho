<?php
session_start();
include 'conexao.php';
//Confirma se o usuárioestá logado
if (!isset($_SESSION['usuario_id'])) {
    die("erro_login");
}

$id_anuncio = $_POST['id_anuncio'];
$id_comprador = $_SESSION['usuario_id'];

//Evita que o dono compre o próprio anúncio
$check_dono = $conn->prepare("SELECT id_usuario FROM anuncio WHERE id = ?");
$check_dono->bind_param("i", $id_anuncio);
$check_dono->execute();
$res = $check_dono->get_result()->fetch_assoc();

if (!$res) {
    die("Erro: Anúncio não encontrado.");
}

if ($res['id_usuario'] == $id_comprador) {
    die("Você não pode solicitar seu próprio material!");
}

//Registra a compra
$sql = "INSERT INTO pedidos (id_anuncio, id_comprador,status) VALUES (?, ?,'pendente')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_anuncio, $id_comprador);

if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "Erro ao processar pedido: " . $conn->error;
}
?>