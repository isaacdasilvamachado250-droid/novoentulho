<?php
$conn = new mysqli("localhost", "root", "", "novoentulho");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = $_POST['nome'];
    $cpf      = $_POST['cpf'];
    $email    = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se os campos essenciais estão vazios
    if (empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($endereco) || empty($senha) || empty($confirmar_senha)) {
        die("Erro:Todos os campos são obrigatórios!");
    }

    if(strlen($cpf) < 14){
        die("CPF inválido. Digite o CPF completo.");
    }

    if(strlen($telefone) < 15){
        die("Telefone inválido. Digite o telefone completo.");
    }

    //Confirma se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        die("Erro: As senhas não coincidem!");
    }

    //Criptografa a senha antes de salvar
    $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    //Checa se CPF ou Email já existem
    $check = $conn->prepare("SELECT id FROM usuarios WHERE cpf = ? OR email = ?");
    $check->bind_param("ss", $cpf, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("Erro: Este CPF ou E-mail já está cadastrado!");
    }

    //INSERÇÃO
    $sql = "INSERT INTO usuarios (nome, cpf, email, telefone, endereco, senha) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $cpf, $email, $telefone, $endereco, $senha_hash);

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
}
?>