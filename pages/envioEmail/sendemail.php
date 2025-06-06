<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Certifique-se de que o PHPMailer está instalado

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['assunto']) && !empty($_POST['msg'])) {


$mail = new PHPMailer(true);

try {
    // Configuração do servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'carreiraleonardo94@gmail.com'; // Seu e-mail
    $mail->Password = 'luzr dgqq hvuk qnay'; // Sua senha ou senha de aplicativo
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

   // $mail->SMTPDebug = 2; // 2 para mensagens detalhadas
   // $mail->Debugoutput = 'html';

    
    // Remetente e destinatário
    $mail->setFrom('carreiraleonardo94@gmail.com' ,'Recita'); // Sempre seu e-mail
    $mail->addReplyTo($_POST['email'], $_POST['nome']); // Responder para o e-mail do formulário
    $mail->addAddress('carreiraleonardo94@gmail.com');
    


    // Conteúdo do e-mail
    $mail->isHTML(true);
    $assunto = ($_POST['assunto']);
    $mail->Subject = $assunto;

    $body = "Nome: " . $_POST['nome'] . "<br>" .
            "E-mail: " . $_POST['email'] . "<br>" .
            "Assunto: " . $_POST['assunto'] . "<br>" .
            "Mensagem:<br>" . $_POST['msg'];


    $mail->Body = $body;

    // Enviar e-mail
    $mail->send();
    echo 'E-mail enviado com sucesso!';

    header("Refresh: 1; URL=../index.html");
    exit;

} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
}else {
    echo "Erro ao enviar e-mail, acesso negado!";
}
}else{
    // Acesso direto ao script, sem vir do formulário
    echo " Acesso inválido! Este script só pode ser executado pelo formulário.";
}
?>
