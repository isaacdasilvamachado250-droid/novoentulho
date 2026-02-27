<?php
session_start();
include 'conexao.php';

// 1. Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Você precisa estar logado para anunciar.");
}

// 2. Verifica se o envio foi bloqueado por ser grande demais
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
    die("Erro: Arquivo muito grande! O servidor não suporta esse tamanho.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Recebe e limpa os dados
    $id_usuario = $_SESSION['usuario_id'];
    $titulo    = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $valor     = $_POST['valor'];
    $frete     = $_POST['frete'];

    // 4. Validação de campos vazios
    if (empty($titulo) || empty($categoria) || empty($valor) || empty($_FILES['imagem']['name'])) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // 5. Processamento da Imagem
    $arquivo = $_FILES['imagem'];
    $limite_bytes = 10 * 1024 * 1024; // 10MB

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        die("Erro ao carregar imagem. Verifique o arquivo.");
    }

    if ($arquivo['size'] > $limite_bytes) {
        die("Erro: A imagem ultrapassa o limite de 10MB.");
    }
 
    $imagem_nome = time() . "_" . basename($arquivo['name']);
    $pasta_destino = "uploads/" . $imagem_nome;

    if (move_uploaded_file($arquivo['tmp_name'], $pasta_destino)) {
        
        $sql = "INSERT INTO anuncio (id_usuario, titulo, categoria, descricao, valor, frete, imagem_path)
                VALUES ('$id_usuario', '$titulo', '$categoria', '$descricao', '$valor', '$frete', '$pasta_destino')";

        if ($conn->query($sql)) {
            echo "Anúncio publicado com sucesso!";
        } else {
            echo "Erro ao salvar no banco: " . $conn->error;
        }
    } else {
        echo "Erro ao mover o arquivo para a pasta uploads. Verifique as permissões da pasta.";
    }
}
?>