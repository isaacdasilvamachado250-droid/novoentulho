<?php
ob_start();
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    return true;
}

function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) return false;

    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) return false;

    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = $_POST['nome'];
    $perfil      = $_POST['perfil_usuario'];
    $tipo_pessoa = $_POST['tipo_pessoa'];
    $cpf      = $_POST['cpf'];
    $email    = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $cep = $_POST['cep'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se os campos essenciais estão vazios
    if (empty($nome) || empty($perfil) || empty($tipo_pessoa) || empty($cpf) || empty($email) || empty($telefone) || empty($endereco) || empty($senha) || empty($confirmar_senha)) {
        die("Erro:Todos os campos são obrigatórios!");
    }

    // Lógica de validação para CPF e CNPJ
    if ($tipo_pessoa === 'PF') {
        if (!validarCPF($cpf)) {
            die("Erro: O CPF informado é inválido.");
        }
    } else if ($tipo_pessoa === 'PJ') {
        if (!validarCNPJ($cpf)) {
            die("Erro: O CNPJ informado é inválido.");
        }
    } else {
        die("Erro: Tipo de pessoa não selecionado.");
    }

    //Confirma se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        die("Erro: As senhas não coincidem!");
    }

    //Criptografa a senha antes de salvar
    $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    //Checa se CPF ou Email já existem
    $check = $conn->prepare("SELECT id FROM usuarios WHERE cpf_cnpj = ? OR email = ?");
    $check->bind_param("ss", $cpf, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("Erro: Este CPF ou E-mail já está cadastrado!");
    }

    //INSERÇÃO
    $sql = "INSERT INTO usuarios (nome, perfil_usuario, tipo_pessoa, cpf_cnpj, email, telefone, endereco, cep, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nome, $perfil, $tipo_pessoa, $cpf, $email, $telefone, $endereco, $cep, $senha_hash);

if($stmt->execute()){
    session_start();
    $_SESSION['usuario_id'] = $conn->insert_id;
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_perfil'] = $perfil;
    $_SESSION['usuario_email'] = $email;

    ob_clean();
    echo "sucesso";
    exit;
    }else{
        echo "Erro ao cadastrar: " . $conn->error;
    }
}
?>