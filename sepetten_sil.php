<?php
session_start();
include 'baglanti.php';

// Kullanıcı girişi yapılmamışsa veya urun_id gelmemişse işlem yapma
if (!isset($_SESSION['kullanici_id']) || !isset($_POST['urun_id'])) {
    header("Location: sepet.php?silme=basarisiz");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$urun_id_to_delete = intval($_POST['urun_id']); // Formdan gelen ürün ID'si

// Sepetteki ürünün adetini kontrol et
$sorgu_adet = $conn->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
$sorgu_adet->bind_param("ii", $kullanici_id, $urun_id_to_delete);
$sorgu_adet->execute();
$sonuc_adet = $sorgu_adet->get_result();

if ($sonuc_adet->num_rows > 0) {
    $urun_sepet_bilgisi = $sonuc_adet->fetch_assoc();
    $mevcut_adet = $urun_sepet_bilgisi['adet'];

    if ($mevcut_adet > 1) {
        // Adet 1'den fazlaysa, adeti bir azalt
        $guncelle = $conn->prepare("UPDATE sepet SET adet = adet - 1 WHERE kullanici_id = ? AND urun_id = ?");
        $guncelle->bind_param("ii", $kullanici_id, $urun_id_to_delete);
        if ($guncelle->execute()) {
            header("Location: sepet.php?silme=azaltildi");
        } else {
            header("Location: sepet.php?silme=hata");
        }
        $guncelle->close();
    } else {
        // Adet 1 ise, ürünü sepetten tamamen sil
        $sil = $conn->prepare("DELETE FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $sil->bind_param("ii", $kullanici_id, $urun_id_to_delete);
        if ($sil->execute()) {
            header("Location: sepet.php?silme=tamamlandi");
        } else {
            header("Location: sepet.php?silme=hata");
        }
        $sil->close();
    }
} else {
    // Ürün sepette bulunamadı (normalde olmaması gereken bir durum)
    header("Location: sepet.php?silme=bulunamadi");
}

$sorgu_adet->close();
$conn->close();
exit;
?>