<?php
namespace App\Utils;
use PHPMailer\PHPMailer\PHPMailer;

class Utils {

    public static function checkNumberOperator($number) {

        
        // Remove leading '+' or '0237' from the number
        $cleanedNumber = preg_replace('/^(\+237|0237)/', '', $number);
        
        // Check the cleaned number's length
        if (strlen($cleanedNumber) !== 9) {
            return [
                'hasError' => true,
                'message' => 'Le numero de telophone doit faire 9 caracteres'
            ];
        }
        
        // Extract the first three digits of the number
        $prefix = substr($cleanedNumber, 0, 3);
        $prefix2 = substr($cleanedNumber, 0, 2);

        
        // Define the prefix values for CM_OM and CM_MOMO
        $cmOmPrefixes = ['69', '655', '656', '657', '658', '659'];
        $cmMomoPrefixes = ['67', '651', '652', '653', '654'];
        
        // Check the prefix and return the appropriate value
        if (in_array($prefix, $cmOmPrefixes)||$prefix2 =='69') {
            return [
                'hasError' => false,
                'message' => null,
                'code' => 'CM_OM'
            ];
           
        } elseif (in_array($prefix, $cmMomoPrefixes)||$prefix2 =='67'||$prefix2 =='68') {
            return [
                'hasError' => false,
                'message' => null,
                'code' => 'CM_MOMO'
            ];
        } else {
            return [
                'hasError' => true,
                'message' => 'Operateur non reconnu'
            ];
        }
    }

    public function emailSender($cacert,$receiver,$content,$subject)
    {
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
                'cafile' => $cacert, 
            ),
        );
        $mail->Host = 'vps96969.serveur-vps.net'; // Replace with your SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@kulmapeck.com'; // Replace with your SMTP username
        $mail->Password = 'tZ5$1DcmSUXXYUY'; // Replace with your SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` for SSL
        $mail->Port = 587; // TCP port to connect to
        $mail->SMTPDebug = 0;

        // Sender and Recipient
        $mail->setFrom('no-reply@kulmapeck.com', 'no-reply-Kulmapeck'); // Replace with your sender's email and name
        $mail->addAddress($receiver, 'Recipient Benito'); // Replace with the recipient's email and name

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->AltBody = $content;

        // Send the email
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    }
}
