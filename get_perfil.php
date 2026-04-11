<?php
session_start();
include 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Não logado']);
    exit;
}

$id = $_SESSION['usuario_id'];

// Dados do usuário
$res = $conn->query("SELECT nome, email, telefone, perfil_usuario FROM usuarios WHERE id = $id");
$user = $res->fetch_assoc();

// Para Vendedor: busca os anúncios e os interesses pendentes
if ($user['perfil_usuario'] == 'Vendedor') {
    // Anúncios do vendedor
    $resAnuncios = $conn->query("SELECT id, titulo, valor, imagem_path, status FROM anuncio WHERE id_usuario = $id ORDER BY create_date DESC");
    $anuncios = [];
    while($row = $resAnuncios->fetch_assoc()) {
        $anuncios[] = $row;
    }
    
    // Interesses pendentes nos anúncios do vendedor
    $resInteresses = $conn->query("
        SELECT i.*, a.titulo as titulo_anuncio, u.nome as nome_comprador, u.telefone as telefone_comprador
        FROM pedidos i
        JOIN anuncio a ON i.id_anuncio = a.id
        JOIN usuarios u ON i.id_comprador = u.id
        WHERE a.id_usuario = $id AND i.status = 'pendente'
        ORDER BY i.create_date DESC
    ");
    $interesses_pendentes = [];
    while($row = $resInteresses->fetch_assoc()) {
        $interesses_pendentes[] = $row;
    }
    
    echo json_encode([
    'nome' => $user['nome'],
    'email' => $user['email'],
    'telefone' => $user['telefone'],
    'perfil' => $user['perfil_usuario'],
    'historico' => $anuncios,
    'notificacoes' => $interesses_pendentes
    ]);
} 
// Para Comprador: busca os interesses que ele fez
else {
    $resInteresses = $conn->query("
    SELECT 
        i.id, i.status, i.id_anuncio, a.titulo as titulo_anuncio, a.valor, a.imagem_path, a.status as status_anuncio,
        u.nome as nome_vendedor,
        u.telefone as telefone_vendedor

    FROM pedidos i
    JOIN anuncio a ON i.id_anuncio = a.id
    JOIN usuarios u ON a.id_usuario = u.id

    WHERE i.id_comprador = $id
    ORDER BY i.create_date DESC
");
    $interesses = [];
    while($row = $resInteresses->fetch_assoc()) {
        $interesses[] = $row;
    }
    
    echo json_encode([
    'nome' => $user['nome'],
    'email' => $user['email'],
    'telefone' => $user['telefone'],
    'perfil' => $user['perfil_usuario'],
    'historico' => $interesses
    ]);
}
?>