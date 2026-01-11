<?php
session_start();
include "baglanti.php";

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php?redirect=" . urlencode("odeme.php"));
    exit;
}

$kullanici_id_odeme = $_SESSION['kullanici_id'];
$sepet_urunleri_odeme = [];
$toplam_tutar_odeme = 0;


$odeme_form_hatasi = $_SESSION['odeme_formu_hatasi'] ?? null;
unset($_SESSION['odeme_formu_hatasi']); // Mesajı gösterdikten sonra sil

$sorgu_odeme = $conn->prepare("SELECT u.marka, u.aciklama, u.fiyat, s.adet 
                            FROM sepet s 
                            JOIN urunler u ON s.urun_id = u.id 
                            WHERE s.kullanici_id = ?");
if ($sorgu_odeme) {
    $sorgu_odeme->bind_param("i", $kullanici_id_odeme);
    $sorgu_odeme->execute();
    $sonuc_odeme = $sorgu_odeme->get_result();
    while ($urun_odeme = $sonuc_odeme->fetch_assoc()) {
        $sepet_urunleri_odeme[] = $urun_odeme;
        $toplam_tutar_odeme += $urun_odeme['fiyat'] * $urun_odeme['adet'];
    }
    $sorgu_odeme->close();
} else {
    error_log("Ödeme sayfası sepet sorgusu hazırlanamadı: " . $conn->error);
    $odeme_form_hatasi = "Sipariş özetiniz yüklenirken bir sorun oluştu. Lütfen daha sonra tekrar deneyin.";
}

if (empty($sepet_urunleri_odeme) && !$odeme_form_hatasi) { // Sepet gerçekten boşsa ve başka bir hata mesajı yoksa
    $_SESSION['odeme_mesaji_sepet_bos'] = "Ödeme yapabilmek için sepetinizde ürün bulunmalıdır."; 
    header("Location: sepet.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Sayfası - Koreli Çeyiz</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon">
    <style>
        .odeme-container { max-width: 800px; margin: 2em auto; padding: 2em; background-color: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .odeme-container h2, .odeme-container h3 { text-align: center; color: #333; margin-bottom: 1em; }
        .siparis-ozeti { border: 1px solid #eee; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .siparis-ozeti ul { list-style: none; padding: 0; }
        .siparis-ozeti ul li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; }
        .siparis-ozeti ul li:last-child { border-bottom: none; font-weight: bold; font-size: 1.1em; }
        .odeme-formu label { display: block; margin-top: 10px; margin-bottom: 5px; font-weight: bold; }
        .odeme-formu input[type="text"], .odeme-formu input[type="email"], .odeme-formu input[type="tel"], .odeme-formu textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .odeme-formu button[type="submit"] { /* Daha spesifik */
            background-color: #28a745; color: white; padding: 12px 25px; border: none;
            border-radius: 5px; cursor: pointer; font-size: 1.1em; width: 100%;
            transition: background-color 0.2s; margin-top:10px;
        }
        .odeme-formu button[type="submit"]:hover { background-color: #218838; }
        .hata-mesaji-odeme { /* odeme_formu_hatasi için */
            color: #D8000C; background-color: #FFD2D2; border: 1px solid #D8000C;
            padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <main>
        <div class="odeme-container">
            <h2>Ödeme Bilgileri</h2>

            <?php if ($odeme_form_hatasi): ?>
                <p class="hata-mesaji-odeme"><?= htmlspecialchars($odeme_form_hatasi); ?></p>
            <?php endif; ?>

            <?php if (!empty($sepet_urunleri_odeme)): ?>
                <div class="siparis-ozeti">
                    <h3>Sipariş Özeti</h3>
                    <ul>
                        <?php foreach ($sepet_urunleri_odeme as $item): ?>
                            <li>
                                <span><?= htmlspecialchars($item['marka']) ?> (<?= htmlspecialchars($item['adet']) ?> x ₺<?= number_format($item['fiyat'], 2, ',', '.') ?>)</span>
                                <span>₺<?= number_format($item['fiyat'] * $item['adet'], 2, ',', '.') ?></span>
                            </li>
                        <?php endforeach; ?>
                        <hr>
                        <li>
                            <span>Genel Toplam:</span>
                            <span>₺<?= number_format($toplam_tutar_odeme, 2, ',', '.') ?></span>
                        </li>
                    </ul>
                </div>

                <h3>Teslimat Adresi</h3>
             
                <form action="odeme_islemi.php" method="POST" class="odeme-formu">
                    <label for="ad_soyad">Ad Soyad:</label>
                    <input type="text" id="ad_soyad" name="ad_soyad" required>

                    <label for="adres">Adres:</label>
                    <textarea id="adres" name="adres" rows="3" required></textarea>

                    <label for="sehir">Şehir:</label>
                    <input type="text" id="sehir" name="sehir" required>
                    
                    <label for="telefon">Telefon:</label>
                    <input type="tel" id="telefon" name="telefon" pattern="[0-9\s+]{10,15}" placeholder="Örn: 05xxxxxxxxx" required>

                    <label for="email">E-posta:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['kullanici_email'] ?? '') ?>" required>

                    <h3 style="margin-top:2em;">Ödeme Yöntemi</h3>
                    <p>Bu kısım temsili olup, gerçek ödeme ağ geçidi entegrasyonu gerektirir.</p>
                    
                    <label for="kart_no">Kart Numarası (Temsili):</label>
                    <input type="text" id="kart_no" name="kart_no" placeholder="XXXX XXXX XXXX XXXX">
                    
                    <input type="hidden" name="toplam_tutar_form" value="<?= $toplam_tutar_odeme ?>">
                    <input type="hidden" name="siparis_token" value="<?= bin2hex(random_bytes(16));  ?>">

                    <button type="submit">Ödemeyi Yap ve Siparişi Tamamla</button>
                </form>
            <?php elseif(!$odeme_form_hatasi):  ?>
                <p class="icerik-bos-mesaji">Ödeme yapılacak ürün bulunmamaktadır. Lütfen önce sepetinize ürün ekleyiniz.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>