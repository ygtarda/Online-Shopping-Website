<?php
session_start();
include 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isim = $_POST['username'];
    $sifre = $_POST['password'];

    // Kullanıcıyı sorgula
    $sql = "SELECT * FROM kullanicilar WHERE isim = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $isim);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 1) {
        $kullanici = $result->fetch_assoc();


        if (password_verify($sifre, $kullanici['sifre'])) {
         
            $_SESSION['kullanici_id'] = $kullanici['id'];  // burada 'id' veritabanındaki kullanıcı ID sütunu
            $_SESSION['kullanici_adi'] = $kullanici['isim'];  // opsiyonel: kullanıcı adını da tut

            // Admin mi kontrol et
            if ($kullanici['rol'] === 'admin') {
                header("Location: adminpaneli.php");
                exit;
            } else {
                header("Location: Anasayfa.php");
                exit;
            }
        } else {
            echo "Hatalı şifre!";
        }
    } else {
        echo "Kullanıcı bulunamadı!";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Geçersiz istek.";
}
