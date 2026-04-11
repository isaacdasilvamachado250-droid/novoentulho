<?php
session_start();
include 'conexao.php';

// Verifica se o usuário é o dono (vendedor) e se o ID do anúncio foi passado
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    die(json_encode([]));
}

$id_anuncio = intval($_GET['id']);
$id_vendedor = $_SESSION['usuario_id'];

// Buscamos os dados do comprador e o ID do pedido para poder finalizar depois
$sql = "
    SELECT 
        p.id AS id_pedido,
        u.nome,
        u.telefone,
        p.status
    FROM pedidos p
    JOIN usuarios u ON p.id_comprador = u.id
    JOIN anuncio a ON p.id_anuncio = a.id
    WHERE p.id_anuncio = ? AND a.id_usuario = ?
    ORDER BY p.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_anuncio, $id_vendedor);
$stmt->execute();
$result = $stmt->get_result();

$interessados = [];
while ($row = $result->fetch_assoc()) {
    $interessados[] = $row;
}

header('Content-Type: application/json');
echo json_encode($interessados);
?>