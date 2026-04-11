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

$check = $conn->prepare("SELECT id FROM pedidos WHERE id_anuncio = ? AND id_comprador = ?");
$check->bind_param("ii", $id_anuncio, $id_comprador);
$check->execute();
$existe = $check->get_result();

if ($existe->num_rows > 0) {
    die("Você já demonstrou interesse neste produto.");
}

if (!$res) {
    die("Erro: Anúncio não encontrado.");
}

if ($res['id_usuario'] == $id_comprador) {
    die("Você não pode solicitar seu próprio material!");
}

// Verifica status do anúncio
$check_status = $conn->prepare("SELECT status FROM anuncio WHERE id = ?");
$check_status->bind_param("i", $id_anuncio);
$check_status->execute();
$status_res = $check_status->get_result()->fetch_assoc();

if ($status_res['status'] == 'vendido') {
    die("Este produto já foi vendido.");
}

$id_vendedor = $res['id_usuario'];

//Registra a compra
$sql = "INSERT INTO pedidos (id_anuncio, id_comprador, id_vendedor, status) 
        VALUES (?, ?, ?, 'pendente')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_anuncio, $id_comprador, $id_vendedor);

if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "Erro ao processar pedido: " . $conn->error;
}
?>