<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login/loginadm.php");
    exit;
}

include 'conecta.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
?>

<form method="post">
    <h2>Editar Post</h2>
    Título: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"><br>
    Conteúdo:<br>
    <textarea name="content"><?= htmlspecialchars($post['content']) ?></textarea><br>
    <button type="submit">Salvar</button>
</form>
