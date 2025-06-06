<?php
require 'conecta.php';

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE token = ? AND token_expira > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token inválido ou expirado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaNova = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $pdo->prepare("UPDATE usuarios SET senha = ?, token = NULL, token_expira = NULL WHERE id = ?")
        ->execute([$senhaNova, $user['id']]);

    echo "Senha alterada com sucesso!";
      header("Refresh: 1; url=login.php");
    exit;
}
?>

<form method="POST">
    <input type="password" name="senha" required placeholder="Nova senha">
    <button>Alterar senha</button>
</form>
