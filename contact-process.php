<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Alleen POST requests toestaan
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /contact.html");
    exit;
}

// Data ophalen en schoonmaken
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$email   = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$message = trim(strip_tags($_POST['message'] ?? ''));

// Validatie
$errors = [];

if (strlen($subject) < 3) {
    $errors[] = "Onderwerp ongeldig";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email ongeldig";
}

if (strlen($message) < 5) {
    $errors[] = "Bericht te kort";
}

// Stop bij fouten
if (!empty($errors)) {
    header("Location: /fout.html");
    exit;
}

// PHPMailer laden
require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // SMTP instellingen
    $mail->isSMTP();
	$mail->SMTPDebug  = 1;
    $mail->Host       = 'mail.mijndomein.nl';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@olafveenhuizen.com';
    $mail->Password   = 'DfdajieERGA*429!';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Afzender en ontvanger
    $mail->setFrom('info@olafveenhuizen.com', 'Olaf Veenhuizen');
    $mail->addAddress('o.veenhuizen@gmail.com', 'Olaf Veenhuizen');

    // Reply-to
    $mail->addReplyTo($email);

    // Email inhoud
    $mail->isHTML(true);
    $mail->Subject = 'Nieuw bericht via contactformulier';

    $mail->Body = '
        <h2>Nieuw bericht via website</h2>

        <p><strong>Onderwerp:</strong><br>' . htmlspecialchars($subject) . '</p>

        <p><strong>Email:</strong><br>' . htmlspecialchars($email) . '</p>

        <p><strong>Bericht:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>
    ';

    $mail->AltBody =
        "Nieuw bericht via website\n\n" .
        "Onderwerp: $subject\n\n" .
        "Email: $email\n\n" .
        "Bericht:\n$message";

    $mail->send();

    header("Location: /bedankt.html");
    exit;

} catch (Exception $e) {

    // Tijdelijk voor debug
    echo "Mailer Error: " . $mail->ErrorInfo;

    // Later gebruiken:
    // header("Location: /fout.html");
    exit;
}
?>