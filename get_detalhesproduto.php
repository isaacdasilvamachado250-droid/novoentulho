<?php
include 'conexao.php';
$id = $_GET['id'];
$sql = "SELECT a.*, u.nome as nome_vendedor, u.endereco as endereco, u.cep as cep
        FROM anuncio a 
        JOIN usuarios u ON a.id_usuario = u.id 
        WHERE a.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

echo json_encode($resultado->fetch_assoc());
?>