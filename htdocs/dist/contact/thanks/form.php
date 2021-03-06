<?php
require __DIR__.'/../_function.php';

session_start();

$allowedHost = getAllowedHost();
$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

// Redirect back to contact input page if the method is not POST or referer is not from allowed host.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || substr($host, 0 - strlen($allowedHost)) != $allowedHost || empty($_SESSION['token']) || empty($_SESSION['contact-form'])) {
    header('Location: ../index.php');
    die();
}

// CSRF check.
if (!hash_equals($_SESSION['token'], $_POST['_token'])) {
    $_SESSION['flash'] = ['input' => [], 'errors' => ['_token' => 'ページの期限が過ぎました。ページを再度読み込んで、ご入力ください。']];
    header('Location: ../index.php');
    die();
}

// Send email to admin & user.
sendMail($_SESSION['contact-form']['input']);

// remove csrf token to prevent multiple submits.
unset($_SESSION['token']);

header('Location: ./index.php');
die();
?>