<?php

session_start();
include 'conecta.php';

if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para editar comentários.");
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verifica se o comentário pertence ao usuário logado
$stmt = $conn->prepare("SELECT comment, user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($comment, $comment_user_id);
if (!$stmt->fetch()) {
    die("Comentário não encontrado.");
}
$stmt->close();

if ($comment_user_id != $user_id) {
    die("Você não pode editar esse comentário.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_comment = trim($_POST['comment']);
    if ($new_comment) {
        $stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $stmt->bind_param("si", $new_comment, $id);
        if ($stmt->execute()) {
            header("Location: blog.php");
            exit;
        } else {
            echo "Erro ao atualizar comentário: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, preencha o campo comentário.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Comentário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>
<div class="container mt-4">
    <h2>Editar Comentário</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="comment" class="form-label">Comentário</label>
            <textarea name="comment" id="comment" class="form-control" rows="5" required><?= htmlspecialchars($comment) ?></textarea>
        </div>
        <button type="submit">Salvar Alterações</button>
        <a href="blog.php">Cancelar</a>
    </form>
</div>
</body>
</html>
