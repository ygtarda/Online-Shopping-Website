<?php
session_start(); // Oturumu başlat

// Tüm oturum değişkenlerini temizle
$_SESSION = array();

// Oturumu yok et
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Kullanıcıyı ana sayfaya veya giriş sayfasına yönlendir
header("Location: login2.php"); 
exit;
?>