<?php
session_start();
include 'baglanti.php';

if (isset($_POST['urun_id']) && isset($_SESSION['kullanici_id'])) {
    $urun_id = intval($_POST['urun_id']);
    $kullanici_id = $_SESSION['kullanici_id'];

    // Sepette var mı kontrol et
    $sorgu = $conn->prepare("SELECT * FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
    $sorgu->bind_param("ii", $kullanici_id, $urun_id);
    $sorgu->execute();
    $sonuc = $sorgu->get_result();

    if ($sonuc->num_rows > 0) {
        // Varsa adet artır
        $guncelle = $conn->prepare("UPDATE sepet SET adet = adet + 1 WHERE kullanici_id = ? AND urun_id = ?");
        $guncelle->bind_param("ii", $kullanici_id, $urun_id);
        $guncelle->execute();
        $guncelle->close();
    } else {
        // Yoksa ekle
        $ekle = $conn->prepare("INSERT INTO sepet (kullanici_id, urun_id, adet) VALUES (?, ?, 1)");
        $ekle->bind_param("ii", $kullanici_id, $urun_id);
        $ekle->execute();
        $ekle->close();
    }
    $sorgu->close();
    $conn->close();

    
    $referer = $_SERVER['HTTP_REFERER'] ?? 'Anasayfa.php'; // Eğer referer yoksa anasayfaya dön
    if (strpos($referer, '?') !== false) {
        header("Location: " . $referer . "&sepet_islem=eklendi");
    } else {
        header("Location: " . $referer . "?sepet_islem=eklendi");
    }
    exit;

} else {
    // Geçersiz veri veya giriş yapılmamış
    $referer = $_SERVER['HTTP_REFERER'] ?? 'Anasayfa.php';
    if (strpos($referer, '?') !== false) {
        header("Location: " . $referer . "&sepet_islem=hata");
    } else {
        header("Location: " . $referer . "?sepet_islem=hata");
    }
    exit;
}
?>