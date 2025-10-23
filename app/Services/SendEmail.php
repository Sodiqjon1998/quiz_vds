<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class SendEmail
{
    public static function sendEmail($data)
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = "ssl://smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "sirojidinov17@gmail.com";
        $mail->Password = "wumh rhjn yrkr efyz";
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->From = "sirojidinov17@gmail.com";


        $mail->addAddress($data['email'], "Admin");
        $mail->addAddress("sirojiddinovsodiqjon1998@gmail.com", "Admin");
        $mail->Subject = "Andijon Yuksalish maktabi";
        $mail->isHTML(true);
        $mail->Body = '
                <!DOCTYPE html>
                <html lang="en">
                    <body>
                        <h1 style="color: #ee8923">Yuksalish maktabi platformasiga xush kelibsiz!</h1>
                        <strong>Login:</strong> '. $data['name'] .' <br>
                        <strong>Parol:</strong> '. $data['password'] .' <br>
                        <strong>Havola:</strong> '. $data['url'] .'
                    </body>
                </html>
            ';

        if (!$mail->send()) {
            return false;
        }
    }
}
