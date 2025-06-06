<?php
session_start();
include 'conecta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        die("Você precisa estar logado para comentar.");
    }
    
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);

    // Verifica se o post existe
    $checkPost = $conn->prepare("SELECT id FROM posts WHERE id = ?");
    $checkPost->bind_param("i", $post_id);
    $checkPost->execute();
    $checkPost->store_result();

    if ($checkPost->num_rows === 0) {
        die("Post não encontrado. Comentário não pode ser adicionado.");
    }
    $checkPost->close();

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);

        if ($stmt->execute()) {
            header("Location: blog.php");
            exit;
        } else {
            echo "Erro ao salvar comentário: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, preencha o campo de comentário.";
    }
} else {
    echo "Método inválido.";
}
