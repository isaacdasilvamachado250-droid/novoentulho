<?php
session_start();
include 'conexao.php';

$id_pedido = $_POST['id_pedido'];

// 1. Atualiza o status para confirmado
$sql = "UPDATE pedidos SET status = 'confirmado' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pedido);

if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "erro";
}
?>