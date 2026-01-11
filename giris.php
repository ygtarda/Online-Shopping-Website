<?php
session_start();
include 'baglanti.php';

$email = $_POST['email'];
$sifre = $_POST['sifre'];

$sql = "SELECT * FROM kullanicilar WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($sifre, $user['sifre'])) {
    $_SESSION['kullanici_id'] = $user['id'];
    $_SESSION['rol'] = $user['rol'];
    echo "Giriş başarılı!";
    // header('Location: admin.php');
} else {
    echo "Hatalı e-posta veya şifre!";
}
?>
