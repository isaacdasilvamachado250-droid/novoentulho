<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    //Busca o usuário pelo email
    $stmt = $conn->prepare("SELECT id, nome, senha, perfil_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($user = $resultado->fetch_assoc()) {
        //Verifica se a senha bate com o Hash do banco
        if (password_verify($senha, $user['senha'])) {
            //Cria a sessão
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION['usuario_perfil'] = $user['perfil_usuario'];

            echo "sucesso";
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "E-mail não encontrado!";
    }
}
?>