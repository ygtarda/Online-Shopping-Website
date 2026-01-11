<?php
session_start();
include "baglanti.php";

ini_set('display_errors', 0); // Hataları ekrana basma (üretim için)
ini_set('log_errors', 1);     // Hataları log dosyasına yaz
error_reporting(E_ALL);       // Tüm hataları raporla

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php?redirect=" . urlencode("odeme.php"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['odeme_formu_hatasi'] = "Geçersiz istek yapıldı.";
    header("Location: odeme.php?odeme_hata=gecersiz_istek");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$ad_soyad = trim($_POST['ad_soyad'] ?? '');
$adres = trim($_POST['adres'] ?? '');
$sehir = trim($_POST['sehir'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$email_form = trim($_POST['email'] ?? '');

if (empty($ad_soyad) || empty($adres) || empty($sehir) || empty($telefon) || empty($email_form)) {
    $_SESSION['odeme_formu_hatasi'] = "Lütfen tüm teslimat bilgilerini eksiksiz doldurun.";
    header("Location: odeme.php?odeme_hata=eksik_bilgi");
    exit;
}

$gercek_toplam_tutar = 0;
$siparis_edilecek_urunler = [];

$sorgu_siparis_urunleri = $conn->prepare("SELECT u.id as urun_id, u.fiyat, s.adet 
                                        FROM sepet s 
                                        JOIN urunler u ON s.urun_id = u.id 
                                        WHERE s.kullanici_id = ?");
if (!$sorgu_siparis_urunleri) {
    error_log("odeme_islemi.php - Sepet sorgusu hazırlanamadı: " . $conn->error . " (Kullanıcı: $kullanici_id)");
    $_SESSION['odeme_formu_hatasi'] = "Siparişiniz işlenirken bir sorun oluştu (Kod OIS1). Lütfen tekrar deneyin.";
    header("Location: odeme.php?odeme_hata=db_err");
    exit;
}

$sorgu_siparis_urunleri->bind_param("i", $kullanici_id);
if (!$sorgu_siparis_urunleri->execute()) {
    error_log("odeme_islemi.php - Sepet sorgusu çalıştırılamadı: " . $sorgu_siparis_urunleri->error . " (Kullanıcı: $kullanici_id)");
    $_SESSION['odeme_formu_hatasi'] = "Siparişiniz işlenirken bir sorun oluştu (Kod OIS2). Lütfen tekrar deneyin.";
    header("Location: odeme.php?odeme_hata=db_err");
    exit;
}

$sonuc_siparis_urunleri = $sorgu_siparis_urunleri->get_result();
if ($sonuc_siparis_urunleri->num_rows === 0) {
    $_SESSION['odeme_formu_hatasi'] = "Sepetinizde ürün bulunamadığı için sipariş oluşturulamadı.";
    header("Location: sepet.php"); // Sepet boşsa direkt sepet sayfasına yönlendir
    exit;
}

while ($item = $sonuc_siparis_urunleri->fetch_assoc()) {
    $gercek_toplam_tutar += $item['fiyat'] * $item['adet'];
    $siparis_edilecek_urunler[] = $item;
}
$sorgu_siparis_urunleri->close();

if ($gercek_toplam_tutar <= 0) {
    $_SESSION['odeme_formu_hatasi'] = "Sipariş tutarı geçersiz.";
    header("Location: sepet.php?odeme_hata=tutar_gecersiz");
    exit;
}

// === ÖDEME AĞ GEÇİDİ ENTEGRASYONU  ===
$odeme_basarili = true; 
$odeme_referans_no = "KORTEST_" . strtoupper(uniqid());

if ($odeme_basarili) {
    $conn->begin_transaction();
    try {
        $stmt_siparis = $conn->prepare("INSERT INTO siparisler (kullanici_id, toplam_tutar, ad_soyad, adres, sehir, telefon, email, odeme_referans, siparis_durumu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Onay Bekliyor')");
        if (!$stmt_siparis) throw new Exception("Sipariş SQL hazırlama hatası: " . $conn->error);
        $stmt_siparis->bind_param("idssssss", $kullanici_id, $gercek_toplam_tutar, $ad_soyad, $adres, $sehir, $telefon, $email_form, $odeme_referans_no);
        if (!$stmt_siparis->execute()) throw new Exception("Sipariş kaydetme hatası: " . $stmt_siparis->error);
        $yeni_siparis_id = $conn->insert_id;
        $stmt_siparis->close();

        $stmt_urun = $conn->prepare("INSERT INTO siparis_urunleri (siparis_id, urun_id, adet, birim_fiyat) VALUES (?, ?, ?, ?)");
        if (!$stmt_urun) throw new Exception("Sipariş ürünleri SQL hazırlama hatası: " . $conn->error);
        foreach ($siparis_edilecek_urunler as $urun_detay) {
            $stmt_urun->bind_param("iiid", $yeni_siparis_id, $urun_detay['urun_id'], $urun_detay['adet'], $urun_detay['fiyat']);
            if (!$stmt_urun->execute()) throw new Exception("Sipariş ürünü (" . $urun_detay['urun_id'] . ") kaydetme hatası: " . $stmt_urun->error);
        }
        $stmt_urun->close();

        $stmt_sepet_temizle = $conn->prepare("DELETE FROM sepet WHERE kullanici_id = ?");
        if (!$stmt_sepet_temizle) throw new Exception("Sepet temizleme SQL hazırlama hatası: " . $conn->error);
        $stmt_sepet_temizle->bind_param("i", $kullanici_id);
        if (!$stmt_sepet_temizle->execute()) throw new Exception("Sepet temizleme hatası: " . $stmt_sepet_temizle->error);
        $stmt_sepet_temizle->close();
        
        $conn->commit();

        $_SESSION['siparis_basarili_mesaji'] = "Siparişiniz başarıyla oluşturuldu!";
        $_SESSION['son_siparis_id'] = $yeni_siparis_id;
        header("Location: siparis_basarili.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Sipariş Oluşturma Exception (Kullanıcı: $kullanici_id): " . $e->getMessage());
        $_SESSION['odeme_formu_hatasi'] = "Siparişiniz oluşturulurken bir sistem hatası oluştu. Lütfen daha sonra tekrar deneyin veya destek ile iletişime geçin. (Hata: SPARS03)";
        header("Location: odeme.php?odeme_hata=sistem_hatasi_db");
        exit;
    }
} else {
    $_SESSION['odeme_formu_hatasi'] = "Ödeme işleminiz onaylanmadı. Lütfen bilgilerinizi kontrol edip tekrar deneyin.";
    header("Location: odeme.php?odeme_hata=odeme_reddedildi");
    exit;
}
$conn->close();
?>