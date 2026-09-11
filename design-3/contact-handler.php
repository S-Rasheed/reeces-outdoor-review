<?php
// ── UPDATE THIS to Reece's real email ──
define('TO_EMAIL', 'ksrasheed22@gmail.com');
define('SITE_NAME', "Reece's Outdoor Solutions");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact');
    exit;
}

function clean($v) {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name']    ?? '');
$phone   = clean($_POST['phone']   ?? '');
$email   = clean($_POST['email']   ?? '');
$method  = clean($_POST['contact_method'] ?? '');
$address = clean($_POST['address'] ?? '');
$desc    = clean($_POST['description'] ?? '');
$timeline = clean($_POST['timeline'] ?? '');
$other   = clean($_POST['service_other'] ?? '');

$services = [];
if (!empty($_POST['services']) && is_array($_POST['services'])) {
    foreach ($_POST['services'] as $s) {
        $sv = clean($s);
        if ($sv === 'Other' && $other !== '') $sv = 'Other: ' . $other;
        $services[] = $sv;
    }
}

if (!$name || !$phone || !$desc || !$address) {
    header('Location: /contact?sent=0');
    exit;
}

$serviceList = $services ? implode(', ', $services) : 'Not specified';

$body = "NEW QUOTE REQUEST — " . SITE_NAME . "\n";
$body .= str_repeat('─', 50) . "\n\n";
$body .= "NAME:             $name\n";
$body .= "PHONE:            $phone\n";
$body .= "EMAIL:            " . ($email ?: 'Not provided') . "\n";
$body .= "PREFERRED METHOD: " . ($method ?: 'Not specified') . "\n\n";
$body .= str_repeat('─', 50) . "\n";
$body .= "SERVICES:         $serviceList\n\n";
$body .= "PROJECT:\n$desc\n\n";
$body .= "ADDRESS:          $address\n";
$body .= "TIMELINE:         " . ($timeline ?: 'Not specified') . "\n\n";
$body .= str_repeat('─', 50) . "\n";
$body .= "Sent from reaceoutdoorsolutions.com\n";

$subject = "New Quote Request from $name — " . SITE_NAME;
$headers  = "From: noreply@reaceoutdoorsolutions.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$sent = mail(TO_EMAIL, $subject, $body, $headers);

header('Location: /contact?sent=' . ($sent ? '1' : '0'));
exit;
