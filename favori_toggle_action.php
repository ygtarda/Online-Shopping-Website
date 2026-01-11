<?php
session_start();
include "baglanti.php"; 


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Kullanıcı Giriş Kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    // Giriş yapılmamışsa, geldiği sayfaya bir uyarı mesajıyla yönlendir
    $return_url = isset($_POST['return_url']) ? htmlspecialchars($_POST['return_url']) : 'nevresim.php'; // Varsayılan bir sayfa
    $separator = (strpos($return_url, '?') === false) ? '?' : '&';
    header("Location: " . $return_url . $separator . "fav_islem_sonucu=giris_gerekli");
    exit;
}

// 2. Ürün ID Kontrolü
if (!isset($_POST['urun_id']) || !is_numeric($_POST['urun_id'])) {
    $return_url = isset($_POST['return_url']) ? htmlspecialchars($_POST['return_url']) : 'nevresim.php';
    $separator = (strpos($return_url, '?') === false) ? '?' : '&';
    header("Location: " . $return_url . $separator . "fav_islem_sonucu=hatali_urunid");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$urun_id = intval($_POST['urun_id']);
$return_url = isset($_POST['return_url']) ? htmlspecialchars(basename($_POST['return_url'])) : 'nevresim.php';
$query_string_part = '';
if (isset($_POST['return_url']) && strpos($_POST['return_url'], '?') !== false) {
    $query_string_part = '?' . explode('?', $_POST['return_url'], 2)[1];
    // Güvenlik: Sadece belirli parametreleri geri eklemeyi düşünebilirsiniz.
}
$full_return_url = $return_url . $query_string_part;


// 3. Veritabanı İşlemleri (Transaction ile)
$conn->begin_transaction();
$message_param = "hata_bilinmeyen"; // Varsayılan hata mesajı

try {
    // Favoride mi kontrol et
    $stmt_check = $conn->prepare("SELECT id FROM favoriler WHERE kullanici_id = ? AND urun_id = ?");
    if (!$stmt_check) throw new Exception("Sorgu hazırlama hatası (kontrol): " . $conn->error);
    
    $stmt_check->bind_param("ii", $kullanici_id, $urun_id);
    if (!$stmt_check->execute()) throw new Exception("Sorgu çalıştırma hatası (kontrol): " . $stmt_check->error);
    
    $result_check = $stmt_check->get_result();
    $is_favorited = $result_check->num_rows > 0;
    $stmt_check->close();

    if ($is_favorited) {
        // Favorideyse sil
        $stmt_delete = $conn->prepare("DELETE FROM favoriler WHERE kullanici_id = ? AND urun_id = ?");
        if (!$stmt_delete) throw new Exception("Sorgu hazırlama hatası (silme): " . $conn->error);

        $stmt_delete->bind_param("ii", $kullanici_id, $urun_id);
        if (!$stmt_delete->execute()) throw new Exception("Sorgu çalıştırma hatası (silme): " . $stmt_delete->error);
        
        if ($stmt_delete->affected_rows > 0) {
            $message_param = "kaldirildi";
        } else {
            // Bu durum, veritabanında bir tutarsızlık varsa veya başka bir işlem aynı anda kaydı sildiyse olabilir.
            $message_param = "hata_silme_etkilenmedi"; 
        }
        $stmt_delete->close();
    } else {
        // Favoride değilse ekle
        $stmt_insert = $conn->prepare("INSERT INTO favoriler (kullanici_id, urun_id) VALUES (?, ?)");
        if (!$stmt_insert) throw new Exception("Sorgu hazırlama hatası (ekleme): " . $conn->error);
        
        $stmt_insert->bind_param("ii", $kullanici_id, $urun_id);
        if (!$stmt_insert->execute()) throw new Exception("Sorgu çalıştırma hatası (ekleme): " . $stmt_insert->error);

        if ($stmt_insert->affected_rows > 0) {
            $message_param = "eklendi";
        } else {
            $message_param = "hata_ekleme_etkilenmedi";
        }
        $stmt_insert->close();
    }

    $conn->commit(); // Tüm işlemler başarılıysa veritabanına işle

} catch (Exception $e) {
    $conn->rollback(); // Bir hata oluşursa tüm işlemleri geri al
    error_log("Favori Toggle Hatası (Kullanıcı: $kullanici_id, Ürün: $urun_id): " . $e->getMessage());
    $message_param = "hata_veritabani_genel"; // Genel bir veritabanı hatası olduğunu belirt
  
}

$conn->close();

// 4. Kullanıcıyı Geldiği Sayfaya Yönlendir
$separator_return = (strpos($full_return_url, '?') === false) ? '?' : '&';
$full_return_url = preg_replace('/([?&])fav_islem_sonucu=[^&]+(&|$)/', '$1', $full_return_url);
$full_return_url = rtrim($full_return_url, '&?'); // Sonda kalan ? veya & varsa temizle

$separator_final = (strpos($full_return_url, '?') === false && !empty($message_param)) ? '?' : '&';
if (empty($message_param)) $separator_final = ''; // Eğer mesaj yoksa ayırıcı da ekleme.


header("Location: " . $full_return_url . ($message_param ? $separator_final . "fav_islem_sonucu=" . $message_param : ''));
exit;
?>