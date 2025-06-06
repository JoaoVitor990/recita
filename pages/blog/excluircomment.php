<?php
session_start();
include 'conecta.php';

if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para excluir comentários.");
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verifica se o comentário pertence ao usuário logado
$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($comment_user_id);
if (!$stmt->fetch()) {
    die("Comentário não encontrado.");
}
$stmt->close();

if ($comment_user_id != $user_id) {
    die("Você não pode excluir esse comentário.");
}

// Se passou na checagem, pode excluir
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: blog.php");
    exit;
} else {
    echo "Erro ao excluir comentário: " . $stmt->error;
}
?>