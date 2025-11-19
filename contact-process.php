<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $text = "Hi ET TAAJ RENT CARS!\n\n";
    $text .= "Name: $name\n";
    $text .= "Email: $email\n";
    $text .= "Message:\n$message";

    $encoded = urlencode($text);
    header("Location: https://wa.me/212772331080?text=$encoded");
    exit();
}
header("Location: contact.php");
exit();