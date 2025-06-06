<?php
session_start();

define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('BASE', 'recita');

// Criar conexão
$conn = new mysqli(HOST, USER, PASS, BASE);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$msg = "";

// Login
if (isset($_POST['login'])) {
    if (!empty($_POST['email']) && !empty($_POST['senha'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($senha, $user['senha'])) {
                // Verifica se é admin
                if ($email === 'admin@gmail.com') {
                    $_SESSION['admin'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $email;
                    header("Location: ../blog/admin.php"); // redireciona para área admin
                    exit;
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $email;
                    header("Location: ../index.php"); // área do usuário normal
                    exit;
                }
            } else {
                $msg = "Senha incorreta!";
            }
        } else {
            $msg = "Usuário não encontrado!";
        }
        $stmt->close();
    } else {
        $msg = "Preencha todos os campos para entrar!";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
</head>
<body>

    <p style="color: blue;"><?php echo htmlspecialchars($msg); ?></p>

    <form method="POST" action="">

        <label>
            E-mail:<br />
            <input type="email" name="email" required />
        </label>

        <br/><br/>

        <label>
            Senha:<br />
            <input type="password" name="senha" required />
        </label>

        <br/><br/>

        <button type="submit" name="login">Entrar</button>
        <br>
        <a href="esqueci.php">Esqueci minha senha</a>
    </form>

</body>
</html>
