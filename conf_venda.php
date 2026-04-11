<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    die("erro_sessao");
}

$id_anuncio = intval($_POST['id_anuncio']);
$id_pedido  = intval($_POST['id_pedido']);
$id_vendedor = $_SESSION['usuario_id'];

//Verifica se o anúncio pertence ao vendedor
$check = $conn->prepare("SELECT id FROM anuncio WHERE id = ? AND id_usuario = ?");
$check->bind_param("ii", $id_anuncio, $id_vendedor);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die("erro_permissao");
}

//Marca o pedido como ACEITO
$updatePedido = $conn->prepare("UPDATE pedidos SET status = 'aceito' WHERE id = ?");
$updatePedido->bind_param("i", $id_pedido);
$updatePedido->execute();

//Recusa todos os outros pedidos do mesmo anúncio
$recusarOutros = $conn->prepare("
    UPDATE pedidos 
    SET status = 'recusado' 
    WHERE id_anuncio = ? AND id != ?
");
$recusarOutros->bind_param("ii", $id_anuncio, $id_pedido);
$recusarOutros->execute();

//Atualiza o anúncio para vendido
$updateAnuncio = $conn->prepare("UPDATE anuncio SET status = 'vendido' WHERE id = ?");
$updateAnuncio->bind_param("i", $id_anuncio);
$updateAnuncio->execute();

echo "sucesso";
?>