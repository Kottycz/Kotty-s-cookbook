<?php

declare(strict_types=1);

/**
 * Odesílač e-mailů přes Gmail SMTP (PHPMailer).
 *
 * NASTAVENÍ:
 *  1. Stáhni 3 soubory z https://github.com/PHPMailer/PHPMailer/tree/master/src
 *     (Exception.php, PHPMailer.php, SMTP.php) a vlož je do php/src/phpmailer/
 *  2. Vytvoř Google App Password:
 *     Google účet → Zabezpečení → Dvoufázové ověření → Hesla aplikací
 *  3. Vlož App Password do konstanty SMTP_PASS níže.
 */
final class Mailer
{
    private const SMTP_HOST = 'smtp.gmail.com';
    private const SMTP_PORT = 587;
    private const SMTP_USER = 'kottyho.kucharka@gmail.com';
    private const SMTP_PASS = 'xvki bswd wnum sgvl';   // <-- SEM VLOŽ GMAIL APP PASSWORD (16 znaků)
    private const FROM_NAME = 'Kottyho kuchařka';
    private const ADMIN_TO  = 'kottyho.kucharka@gmail.com';

    /**
     * Pošle e-mail s žádostí o schválení receptu.
     */
    public static function sendApproval(
        string $recipeName,
        string $userEmail,
        string $approvalUrl,
    ): bool {
        $subject = 'Nový recept ke schválení: ' . $recipeName;
        $body    = "Uživatel {$userEmail} přidal nový recept ke schválení.\n\n"
            . "Název receptu: {$recipeName}\n\n"
            . "Pro schválení a zveřejnění klikni na tento odkaz:\n"
            . $approvalUrl . "\n\n"
            . "Recept se zobrazí návštěvníkům ihned po schválení.";

        return self::send(self::ADMIN_TO, $subject, $body, $userEmail);
    }

    /**
     * Pošle e-mail uživateli o zamítnutí receptu.
     */
    public static function sendRejection(
        string $recipeName,
        string $userEmail,
        string $reason = '',
    ): bool {
        $subject = 'Recept nebyl schválen: ' . $recipeName;
        $body    = "Tvůj recept \"{$recipeName}\" nebyl schválen administrátorem.\n\n";
        if ($reason !== '') {
            $body .= "Důvod: {$reason}\n\n";
        }
        $body .= "Recept zůstává uložen v tvé knihovně (Moje recepty).\n"
               . "Můžeš ho upravit a znovu navrhnout ke zveřejnění.";

        return self::send($userEmail, $subject, $body);
    }

    /**
     * Obecná metoda pro odeslání e-mailu.
     * Použije PHPMailer (SMTP) pokud jsou soubory k dispozici,
     * jinak PHP mail() jako záložní řešení.
     */
    public static function send(
        string $to,
        string $subject,
        string $body,
        string $replyTo = '',
    ): bool {
        $phpmailerPath = __DIR__ . '/phpmailer/PHPMailer.php';

        // PHPMailer – SMTP (spolehlivé)
        if (file_exists($phpmailerPath) && self::SMTP_PASS !== '') {
            return self::sendViaSMTP($to, $subject, $body, $replyTo);
        }

        // Záložní řešení – PHP mail() (méně spolehlivé, může jít do spamu)
        return self::sendViaPhpMail($to, $subject, $body, $replyTo);
    }

    private static function sendViaSMTP(
        string $to,
        string $subject,
        string $body,
        string $replyTo,
    ): bool {
        require_once __DIR__ . '/phpmailer/Exception.php';
        require_once __DIR__ . '/phpmailer/PHPMailer.php';
        require_once __DIR__ . '/phpmailer/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USER;
            $mail->Password   = self::SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = self::SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(self::SMTP_USER, self::FROM_NAME);
            if ($replyTo !== '') {
                $mail->addReplyTo($replyTo);
            }
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('Mailer SMTP error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private static function sendViaPhpMail(
        string $to,
        string $subject,
        string $body,
        string $replyTo,
    ): bool {
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers        = "MIME-Version: 1.0\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        if ($replyTo !== '') {
            $headers .= "Reply-To: {$replyTo}\r\n";
        }
        return mail($to, $encodedSubject, $body, $headers);
    }
}
