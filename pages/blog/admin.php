<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login/loginadm.php");
    exit;
}

include 'conecta.php';

// Adicionar novo post com imagem
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        // Cria pasta uploads se não existir
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        // Validação básica de extensão
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            die("Formato de imagem inválido. Apenas JPG, PNG, GIF são permitidos.");
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            die("Erro ao enviar a imagem.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $imageName);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}

// Excluir post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Antes de deletar o post, deletar a imagem do servidor (se existir)
    $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($post = $res->fetch_assoc()) {
        if (!empty($post['image']) && file_exists("uploads/" . $post['image'])) {
            unlink("uploads/" . $post['image']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}
?>

<h2>Área do Administrador</h2>
<p>Bem-vindo, admin!| <a href="logout.php">Sair</a></p>

<h3>Adicionar Novo Post</h3>
<form method="post" enctype="multipart/form-data">
    Título: <input type="text" name="title" required><br>
    Conteúdo:<br>
    <textarea name="content" required></textarea><br>
    Imagem: <input type="file" name="image" accept="image/*"><br><br>
    <button type="submit" name="add">Adicionar</button>
</form>

<h3>Posts Existentes</h3>

<?php
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");

while ($post = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:15px;'>";
    echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";

    if (!empty($post['image'])) {
        echo "<img src='uploads/" . htmlspecialchars($post['image']) . "' alt='Imagem do post' style='max-width:300px; display:block; margin-bottom:10px;'>";
    }

    echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
    echo "<a href='edit_post.php?id={$post['id']}'>Editar</a> | ";
    echo "<a href='admin.php?delete={$post['id']}' onclick=\"return confirm('Tem certeza que deseja excluir este post?');\">Excluir</a>";
    echo "</div>";
}
?>
