<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database\Database;
use App\Dto\AdvertForMailDto;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class NotificationService
{
    const EMAIL = 'olx@gmail.com';
    const NAME = 'olx.com';

    private function setSMTPConfig(PHPMailer $mail): void
    {
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
    }

    public function sendEmailChangedPrice(AdvertForMailDto $dto, array $emails): bool
    {
        try {
            // SMTP
            $mail = new PHPMailer();
            $mail->isSMTP();
            $this->setSMTPConfig($mail);

            $mail->setFrom(self::EMAIL, self::NAME);

            foreach ($emails as $email) {
                $mail->addAddress($email['email'], 'User');
            }

            $mail->isHTML();
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'The price of the advert has changed!';
            $mail->Body = "<html><head><meta charset='UTF-8'></head><body>";
            $mail->Body .= "Advert <a href='" . $dto->link . "'>" . $dto->title .
                "</a> price changed. New price: " . $dto->currentPrice . " " . $dto->currency .
                " Old price: " . $dto->lastPrice . " " . $dto->currency . ".<br/>
                <img class='fit-picture' src='" . $dto->linkImage . "' 
                style='max-width: 100%; max-height: 400px; object-fit: cover;' /><br/>";
            $mail->Body .= "</body></html>";
            $mail->AltBody = 'Your olx';

            $mail->send();

            return true;
        } catch (Exception $e) {
            $logFile = '/var/log/cron.log';
            $message = date('Y-m-d H:i:s') . "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            file_put_contents($logFile, $message, FILE_APPEND);

            return false;
        }
    }

    public function sendEmailVerify(string $email, int $subscriberId): bool
    {
        $token = $this->generateVerificationToken();

        $verificationUrl = $_ENV['APP_URL'] . $_ENV['BASE_URL'] ."email-verify?token=" . urlencode($token) .
            "&email=" . urlencode($email);

        try {
            // SMTP
            $mail = new PHPMailer();
            $mail->isSMTP();
            $this->setSMTPConfig($mail);
            $mail->setFrom(self::EMAIL, self::NAME);
            $mail->addAddress($email, 'User');

            $mail->isHTML();
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Email Verification on olx!';
            $mail->Body = "<html><head><meta charset='UTF-8'></head><body>";
            $mail->Body .= " <a href='" . $verificationUrl . "'>" .
                'Click the link below to verify your email address:' . $verificationUrl .
                "</a><br/>";
            $mail->Body .= "</body></html>";
            $mail->AltBody = 'Your olx';

            $mail->send();

            (new Database())->doQuery(
                "INSERT INTO email_verification (subscriber_id, token) VALUES (:subscriber_id, :token)",
                [
                    'subscriber_id' => $subscriberId,
                    'token' => $token,
                ]
            );

            return true;
        } catch (Exception $e) {
            $logFile = '/var/log/cron.log';
            $message = date('Y-m-d H:i:s') . "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            file_put_contents($logFile, $message, FILE_APPEND);

            return false;
        }
    }

    public function generateVerificationToken($length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
