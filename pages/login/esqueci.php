<?php
require 'conecta.php';
require '../envioEmail/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];


    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

 
        $pdo->prepare("UPDATE usuarios SET token = ?, token_expira = ? WHERE email = ?")
            ->execute([$token, $expira, $email]);

     $link = "http://localhost/cod/recita/pages/login/resetar.php?token=$token";


     
        $mail = new PHPMailer(true);
        try {
   
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'carreiraleonardo94@gmail.com';
            $mail->Password = 'luzr dgqq hvuk qnay';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('carreiraleonardo94@gmail.com', 'Recita');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Redefinir sua senha';
            $mail->Body = "Olá! Clique no link para redefinir sua senha: <a href='$link'>$link</a>";

            $mail->send();
            echo "E-mail enviado com link para redefinição!";
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail: " . $mail->ErrorInfo;
        }
    } else {
        echo "E-mail não encontrado.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" required placeholder="Digite seu e-mail">
    <button>Enviar link</button>
</form>
