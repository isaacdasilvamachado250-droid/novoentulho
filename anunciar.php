<?php
//Realiza a conexão com o banco do Wampserver
$conn = new mysqli("localhost", "root", "", "novoentulho");

    //Verifica se o PHP barrou o envio por ser grande demais
    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        die("<h1>Erro: Arquivo muito pesado!</h1><p>Sua imagem ultrapassa o limite do servidor. Tente uma foto de até 10MB.</p><a href='index.html'>Voltar</a>");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        //Recebe os dados do formulário
        $titulo    = $_POST['titulo'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $valor     = $_POST['valor'];
        $frete     = $_POST['frete'];

    // Captura os dados do arquivo e define o limite de tamanho da imagem
    $arquivo = $_FILES['imagem'];
    $limite_bytes = 10 * 1024 * 1024;

    //Se um arquivo estiver corrompido ou houver problemas no envio, mostra essa mensagem de erro
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        die("<h1>Erro: A imagem excede o limite permitido (10MB).</h1>");
    }

    //Se a imagem for maior que o limite definido, mostra essa mensagem de erro
    if ($arquivo['size'] > $limite_bytes) {
        die("<h1>Erro: A imagem ultrapassa o limite de 10MB permitidos.</h1>");
    }

    //Processa a imagem
    $imagem_nome = $_FILES['imagem']['name'];
    $pasta_destino = "uploads/" . time() . "_" . $imagem_nome;
    /*A função time() gera o número de segundos atuais e renomeia a imagem colocando esse número no início.
    Isso evita que uma imagem sobreponha outra com o mesmo nome*/

    //Move o arquivo para a pasta uploads
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta_destino)){

        //Insere no Banco de Dados
        $sql = "INSERT INTO materiais (titulo, categoria, descricao, valor, frete, imagem_path)
                VALUES ('$titulo', '$categoria', '$descricao', '$valor', '$frete', '$pasta_destino')";

        //Envia o comando sql para o banco. Se o banco aceitar sem erros, ele exibe a mensagem de sucesso.
        if ($conn->query($sql)){
            echo "<h1>Anúncio publicado com sucesso!</h1>";
            echo "<a href='index.html'>Voltar</a>";
        } else{
            echo "Erro ao salvar no banco: " . $conn->error;
        }
    }
}
?>