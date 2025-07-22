<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
header("Content-Type: application/json");


// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

// Read JSON body
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data["fields"])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$fields = $data["fields"];
$subject = $data["subject"] ?? "Enquiry from Frypan Graphic website";

$htmlContent = "<table border='1' cellspacing='0' cellpadding='10' style='border-collapse: collapse; width: 100%; max-width: 600px; margin: 20px 0; border: 1px solid #ddd;'>";
foreach ($fields as $field) {
    $label = htmlspecialchars($field['label'] ?? '');
    $value = nl2br(htmlspecialchars($field['value'] ?? ''));
    $htmlContent .= "
        <tr>
            <td style='padding: 12px; border: 1px solid #ddd; background-color: #f4f4f4;'>$label</td>
            <td style='padding: 12px; border: 1px solid #ddd;'>$value</td>
        </tr>";
}
$htmlContent .= "</table>";

$mail = new PHPMailer(true);

$SMTP_HOST = "viper.itsoul.com";
$SMTP_PORT = 465;
$SMTP_USER = "mailprocessor@verligte.com";
$SMTP_PASS = "2-aCjR?Urfr1";


try {
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->isHTML(isHtml: true);

    $mail->Host = $SMTP_HOST;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL encryption
    $mail->Port = 465; // Standard port for SMTPS with SSL encryption

    $mail->Username = $SMTP_USER;
    $mail->Password = $SMTP_PASS;

    $mail->setFrom($SMTP_USER, "Frypan Graphics");

    $mail->addAddress("thecoderashok@gmail.com");
    $mail->addAddress("jaydeepnandola01@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $htmlContent;

    $mail->send();

    echo json_encode(["status" => "success", "message" => "Email sent successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
}
