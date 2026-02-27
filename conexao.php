<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "novoentulho";
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se deu erro
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Garante que os acentos funcionem
$conn->set_charset("utf8mb4");
?>