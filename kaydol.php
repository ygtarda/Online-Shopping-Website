<?php
session_start();
include 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isim = $_POST['username'];
    $email = $_POST['email'];
    $sifre = $_POST['password'];
    $sifre_tekrar = $_POST['confirm-password'];

    // Şifre tekrar kontrolü
    if ($sifre !== $sifre_tekrar) {
        echo "<script>alert('Hata: Şifreler uyuşmuyor!'); window.history.back();</script>";
        exit;
    }

    // Şifreyi güvenli hale getir
    $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);

    // SQL ile kullanıcıyı ekle
    $sql = "INSERT INTO kullanicilar (isim, email, sifre) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $isim, $email, $sifre_hash);

    if ($stmt->execute()) {
        echo "<script>
                alert('Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...');
                window.location.href = 'login2.php';
              </script>";
    } else {
        echo "<script>alert('Hata: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Geçersiz istek.'); window.history.back();</script>";
}
