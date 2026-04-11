<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    die("erro_login");
}

$id_comprador = $_SESSION['usuario_id'];

$sql = "
SELECT 
    p.id,
    p.status,
    a.titulo
    a.id_usuario AS id_vendedor
FROM pedidos p
JOIN anuncio a ON p.id_anuncio = a.id
WHERE p.id_comprador = ?
ORDER BY p.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_comprador);
$stmt->execute();

$result = $stmt->get_result();

$pedidos = [];

while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}

echo json_encode($pedidos);
?>