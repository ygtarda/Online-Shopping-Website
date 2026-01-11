<?php
session_start();
include "baglanti.php"; // Veya baglanti.php, dosya adınız neyse

// Hata raporlamayı aç (geliştirme için)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Giriş ve Parametre Kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    
    $return_url = isset($_POST['return_url']) ? $_POST['return_url'] : 'Anasayfa.php';
    if (strpos($return_url, '?') !== false) {
        header("Location: " . $return_url . "&fav_durum=giris_gerekli");
    } else {
        header("Location: " . $return_url . "?fav_durum=giris_gerekli");
    }
    exit;
}

if (!isset($_POST['urun_id']) || !is_numeric($_POST['urun_id'])) {
    $return_url = isset($_POST['return_url']) ? $_POST['return_url'] : 'Anasayfa.php';
    if (strpos($return_url, '?') !== false) {
        header("Location: " . $return_url . "&fav_durum=hatali_urunid");
    } else {
        header("Location: " . $return_url . "?fav_durum=hatali_urunid");
    }
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$urun_id = intval($_POST['urun_id']);
$return_url = isset($_POST['return_url']) ? htmlspecialchars($_POST['return_url']) : 'nevresim.php'; // Güvenlik için htmlspecialchars ve varsayılan sayfa



// 2. Veritabanı İşlemleri
$conn->begin_transaction(); // Daha güvenli işlem için transaction başlat

try {
    // Favoride mi kontrol et
    $stmt_check = $conn->prepare("SELECT id FROM favoriler WHERE kullanici_id = ? AND urun_id = ?");
    if (!$stmt_check) throw new Exception("Favori kontrol sorgusu hazırlanamadı: " . $conn->error);
    $stmt_check->bind_param("ii", $kullanici_id, $urun_id);
    if (!$stmt_check->execute()) throw new Exception("Favori kontrol sorgusu çalıştırılamadı: " . $stmt_check->error);
    $result_check = $stmt_check->get_result();
    $is_favorited = $result_check->num_rows > 0;
    $stmt_check->close();


    $message_param = "";

    if ($is_favorited) {
        // Favorideyse sil
        $stmt_delete = $conn->prepare("DELETE FROM favoriler WHERE kullanici_id = ? AND urun_id = ?");
        if (!$stmt_delete) throw new Exception("Favori silme sorgusu hazırlanamadı: " . $conn->error);
        $stmt_delete->bind_param("ii", $kullanici_id, $urun_id);
        if (!$stmt_delete->execute()) throw new Exception("Favori silme sorgusu çalıştırılamadı: " . $stmt_delete->error);
        
        if ($stmt_delete->affected_rows > 0) {
            $message_param = "kaldirildi";
        } else {
            $message_param = "hata_silme_etkilenmedi"; // Daha spesifik hata
        }
        $stmt_delete->close();
    } else {
        // Favoride değilse ekle
        $stmt_insert = $conn->prepare("INSERT INTO favoriler (kullanici_id, urun_id) VALUES (?, ?)");
        if (!$stmt_insert) throw new Exception("Favori ekleme sorgusu hazırlanamadı: " . $conn->error);
        $stmt_insert->bind_param("ii", $kullanici_id, $urun_id);
        if (!$stmt_insert->execute()) throw new Exception("Favori ekleme sorgusu çalıştırılamadı: " . $stmt_insert->error);

        if ($stmt_insert->affected_rows > 0) {
            $message_param = "eklendi";
        } else {
            $message_param = "hata_ekleme_etkilenmedi"; // Daha spesifik hata
        }
        $stmt_insert->close();
    }

    $conn->commit(); // İşlem başarılıysa onayla

} catch (Exception $e) {
    $conn->rollback(); // Hata oluşursa geri al
    error_log("Favori toggle hatası: " . $e->getMessage()); // Sunucu loguna hatayı yaz
    $message_param = "hata_genel_veritabani";

}

$conn->close();

// 3. Kullanıcıyı Yönlendir

if (strpos($return_url, '?') !== false) {
    header("Location: " . $return_url . "&fav_islem_sonucu=" . $message_param); 
} else {
    header("Location: " . $return_url . "?fav_islem_sonucu=" . $message_param);
}
exit;
?>