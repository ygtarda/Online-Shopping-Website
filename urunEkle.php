<?php
session_start();
$baglanti = new mysqli("localhost", "root", "", "koreliceyiz_db");
if ($baglanti->connect_error) {
    die("Veritabanına bağlanılamadı: " . $baglanti->connect_error);
}

$urun_adi = $_POST['product-name'];
$urun_aciklama = $_POST['product-description'];
$urun_fiyat = $_POST['product-price'];
$kategori = $_POST['product-category'];



$izinli = array("jpg", "jpeg", "png", "webp");
$klasor_yolu = "$kategori/";

if (!file_exists($klasor_yolu)) {
    mkdir($klasor_yolu, 0777, true);
}

$dosya_adi = basename($_FILES["product-image"]["name"]);
$dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));

if (!in_array($dosya_uzantisi, $izinli)) {
    die("Geçersiz dosya türü. Sadece WEBP, JPG, JPEG, PNG izin verilir.");
}

$hedef_dosya = $klasor_yolu . $dosya_adi;

if (move_uploaded_file($_FILES["product-image"]["tmp_name"], $hedef_dosya)) {
    $sql = "INSERT INTO urunler (marka,aciklama, fiyat, resim_yolu, kategori) VALUES (?, ?, ?, ?, ?)";
    $stmt = $baglanti->prepare($sql);
    $stmt->bind_param("ssdss", $urun_adi,$urun_aciklama , $urun_fiyat, $hedef_dosya, $kategori);
    if ($stmt->execute()) {
        echo "Ürün başarıyla eklendi.";
    } else {
        echo "Veritabanı hatası: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Resim yüklenemedi.";
}

$baglanti->close();
?>
