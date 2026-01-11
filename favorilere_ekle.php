<?php
session_start();
include 'baglanti.php';

if (isset($_POST['urun_id']) && isset($_SESSION['kullanici_id'])) {
    $urun_id = intval($_POST['urun_id']);
    $kullanici_id = $_SESSION['kullanici_id'];

    // Favoride var mı kontrolü
    $sorgu = $conn->prepare("SELECT * FROM favoriler WHERE kullanici_id = ? AND urun_id = ?");
    $sorgu->bind_param("ii", $kullanici_id, $urun_id);
    $sorgu->execute();
    $sonuc = $sorgu->get_result();

    if ($sonuc->num_rows === 0) {
        // Yoksa ekle
        $ekle = $conn->prepare("INSERT INTO favoriler (kullanici_id, urun_id) VALUES (?, ?)");
        $ekle->bind_param("ii", $kullanici_id, $urun_id);
        $ekle->execute();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: login.php");
    exit;
}
