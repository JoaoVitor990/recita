<?php
session_start();
include 'conecta.php';

$user_id_logado = $_SESSION['user_id'] ?? null; // id do usuário logado, ou null se não estiver logado
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Meu Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.html">
      <img src="../img/logo-r.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" 
      aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="../index.php">Início</a>
        <a class="nav-link" href="../sobre.php">Sobre</a>
        <a class="nav-link" href="../servico.php">Serviço</a>
        <a class="nav-link" href="../produto.php">Produto</a>
        <a class="nav-link" href="../blog/blog.php">Blog</a>
        <a class="nav-link" href="../contato.php">Contato</a>

         <?php if (!isset($_SESSION['user_id'])): ?>
              <a class="nav-link" href="../login/registro.php">Entrar</a>
            <?php else: ?>
              <a class="nav-link" href="logout.php">Sair</a>
            <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<h1>Recita</h1>

<!-- Scripts e acessibilidade Vlibras -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

<?php
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<h3>Posts do Blog</h3>

<?php while ($post = $result->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; margin-bottom:20px; padding:10px;">
        <h4><?= htmlspecialchars($post['title']) ?></h4>

        <?php if (!empty($post['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Imagem do post" style="max-width:300px; display:block; margin-bottom:10px;">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <small>Publicado em: <?= $post['created_at'] ?></small>

        <!-- Comentários -->
        <div style="margin-top: 20px;">
            <h5>Comentários:</h5>
            <?php
                $post_id = $post['id'];
                
                $stmt = $conn->prepare("
                    SELECT comments.*, usuarios.nome 
                    FROM comments 
                    JOIN usuarios ON comments.user_id = usuarios.id 
                    WHERE comments.post_id = ? 
                    ORDER BY comments.created_at DESC
                ");
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                $comments_result = $stmt->get_result();
            ?>
            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <div style="border-top: 1px solid #ddd; padding: 5px 0;">
                    <strong><?= htmlspecialchars($comment['nome']) ?></strong> disse:<br>
                    <?= nl2br(htmlspecialchars($comment['comment'])) ?><br>
                    <small>Em: <?= $comment['created_at'] ?></small><br>

                    <?php if ($user_id_logado && $comment['user_id'] == $user_id_logado): ?>
                        <a href="editcomment.php?id=<?= $comment['id'] ?>">Editar</a>
                        <a href="excluircomment.php?id=<?= $comment['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este comentário?');">Excluir</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            <?php $stmt->close(); ?>

            <!-- Formulário para novo comentário -->
            <?php if ($user_id_logado): ?>
                <form method="POST" action="addcomment.php" style="margin-top:10px;">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <div class="mb-3">
                        <label for="comment-<?= $post_id ?>" class="form-label">Comentário</label>
                        <textarea name="comment" id="comment-<?= $post_id ?>" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit">Enviar comentário</button>
                </form>
            <?php else: ?>
                <p>Você precisa estar logado para comentar.</p>
            <?php endif; ?>

        </div>
    </div>
<?php endwhile; ?>

</body>
</html>
