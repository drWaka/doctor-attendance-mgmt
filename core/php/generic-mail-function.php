<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

//   require '../../vendor/PHPMailer/src/Exception.php';
//   require '../../vendor/PHPMailer/src/PHPMailer.php';
//   require '../../vendor/PHPMailer/src/SMTP.php';


  function sendEmail($reciever, $emailContent) {
    // System Mail Configuration
    $sysMailRecQry = 'SELECT * FROM system_mail_serv';
    $sysMailRecRes = $GLOBALS['connection'] -> query($sysMailRecQry);
    $sysMailRecRow = $sysMailRecRes -> fetch_object();

    $mail = new PHPMailer();

    //Server settings
    $mail->SMTPDebug = 3;                                 // Disable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $sysMailRecRow -> serv_host;            // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $sysMailRecRow -> email_addr;       // SMTP username
    $mail->Password = $sysMailRecRow -> email_pwd;        // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $sysMailRecRow -> serv_port;            // TCP port to connect to

    // Sender Info
    $mail->setFrom($sysMailRecRow -> email_addr, 'Our Lady of Lourdes Hospital');
    $mail->addReplyTo($sysMailRecRow -> email_addr, 'Our Lady of Lourdes Hospital');

    // Attachments
    if (!empty($emailContent -> attachments)) {
      for ($index = 0 ; $index < count($emailContent -> attachments) ; $index++) {
        $mail->addAttachment($emailContent -> attachments[$index]['path'], $emailContent -> attachments[$index]['fileName']);
      }
    }

    // Reciever Info - Reciever
    for ($index = 0 ; $index < count($reciever['receiver']) ; $index++) {
      $mail->addAddress($reciever['receiver'][$index] -> email,  $reciever['receiver'][$index] -> fullname);
    }

    // Reciever Info Copy
    // die(var_dump(isset($reciever['copy']) && !empty($reciever['copy'])));
    if (isset($reciever['copy']) && !empty($reciever['copy'])) {
      for ($index = 0 ; $index < count($reciever['copy']) ; $index++) {
        $mail->addCC($reciever['copy'][$index] -> email,  $reciever['copy'][$index] -> fullname);
      }
    }
    

    $mail->addBCC('ylsuarez@ollh.ph', 'Yancy Suarez');


    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $emailContent -> title;
    $mail->Body    = $emailContent -> mainBody;
    $mail->AltBody = $emailContent -> alternateBody;

    return $mail->send();
  }