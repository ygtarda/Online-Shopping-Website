<?php
session_start();
include "baglanti.php";

// Kullanıcı girişi yapılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

$sorgu = $conn->prepare("SELECT s.id as sepet_id, u.id as urun_id_veritabani, u.marka, u.aciklama, u.fiyat, u.resim_yolu, s.adet 
             FROM sepet s 
             JOIN urunler u ON s.urun_id = u.id 
             WHERE s.kullanici_id = ?");
if (!$sorgu) {
    error_log("Sepet sorgusu hazırlanamadı: " . $conn->error);
    die("Sepet yüklenirken bir hata oluştu.");
}
$sorgu->bind_param("i", $kullanici_id);
if (!$sorgu->execute()) {
    error_log("Sepet sorgusu çalıştırılamadı: " . $sorgu->error);
    die("Sepet yüklenirken bir sorun oluştu.");
}
$sonuc = $sorgu->get_result();
$sepet_urunleri = $sonuc->fetch_all(MYSQLI_ASSOC);
$sorgu->close(); 
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreli Çeyiz Online Satış Sitesi - Sepetim</title>
    <link rel="stylesheet" href="styles.css">
    <?php if (file_exists("sepet.css")):  ?>
        <link rel="stylesheet" href="sepet.css">
    <?php endif; ?>
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon">
    <script defer src="scripts.js"></script>
    <style>
    
        .icerik-bos-mesaji {
            text-align: center;
            padding: 40px 20px;
            font-size: 1.2em;
            color: #555;
            min-height: 200px; /* Ana içeriğin minimum yüksekliği */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%; /* Genişliği kaplaması için */
        }
        .icerik-bos-mesaji p {
            margin: 0;
        }

        .sepet-grid .product-item {
            /* Belki genişlik ayarı vs. */
        }
        .sepet-grid .products-container {

             display: grid; /* Ürünlerin daha düzenli sıralanması için */
             grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Varsayılan grid ayarı */
             gap: 1em;
        }
        .remove-from-cart {
            background-color: #e74c3c; /* Daha belirgin bir kırmızı */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 10px;
            transition: background-color 0.2s ease;
        }
        .remove-from-cart:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <main>
        <section id="products">
            <h2>Sepetim</h2>

            <?php

            if (isset($_GET['silme'])) {
                $silme_mesaji = "";
                $silme_class = "bildirim-uyari"; 
                switch ($_GET['silme']) {
                    case 'ok':
                    case 'tamamlandi':
                    case 'azaltildi':
                        $silme_mesaji = "✅ Ürün sepetinizden güncellendi/kaldırıldı.";
                        $silme_class = "bildirim-basarili";
                        break;
                    case 'hata':
                    case 'bulunamadi':
                    case 'basarisiz_giris_veya_urunid':
                        $silme_mesaji = "❌ Sepet işleminde bir sorun oluştu.";
                        $silme_class = "bildirim-hata";
                        break;
                }
                if (!empty($silme_mesaji)) {
                    echo '<div id="sepet-silme-bildirimi" class="bildirim-mesaji ' . $silme_class . '">' . $silme_mesaji . '</div>';
                }
            }
            ?>

            <div class="sepet-grid">
                <?php if (count($sepet_urunleri) === 0): ?>
                    <div class="icerik-bos-mesaji">
                        <p>Sepetiniz boş.</p>
                        <p style="margin-top:15px;"><a href="Anasayfa.php" class="btn" style="padding:10px 20px; background-color:#cb9869; color:white; text-decoration:none; border-radius:5px;">Alışverişe Başla</a></p>
                    </div>
                <?php else: ?>
                    <div class="products-container">
                        <?php foreach ($sepet_urunleri as $urun): ?>
                            <div class="product-item" data-name="<?= htmlspecialchars($urun["marka"]) ?>">
                                <img src="<?= htmlspecialchars($urun["resim_yolu"]) ?>" alt="<?= htmlspecialchars($urun["marka"]) ?>">
                                <div class="product-info">
                                    <h4><?= htmlspecialchars($urun["marka"]) ?></h4>
                                    <p><?= htmlspecialchars($urun["aciklama"]) ?></p>
                                    <p><strong>Adet:</strong> <?= htmlspecialchars($urun["adet"]) ?></p>
                                    <p style="color:rosybrown; font-size:x-large;">
                                        <strong>Toplam: ₺<?= number_format($urun["fiyat"] * $urun["adet"], 2, ',', '.') ?></strong>
                                        <?php if ($urun["adet"] > 1): ?>
                                            <span style="font-size:0.7em; color:#777;">(Birim: ₺<?= number_format($urun["fiyat"], 2, ',', '.') ?>)</span>
                                        <?php endif; ?>
                                    </p>
                                    <form method="post" action="sepetten_sil.php">
                                       
                                        <input type="hidden" name="urun_id" value="<?= htmlspecialchars($urun['urun_id_veritabani']); ?>">
                                        <button type="submit" class="add-to-cart">
                                            <?= ($urun['adet'] > 1) ? 'Bir Adet Sil' : 'Sepetten Kaldır' ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                      <div style="text-align: center; margin-top: 20px; width:100%;">
                        <?php
                        $toplam_tutar = 0;
                        if (!empty($sepet_urunleri)) { // Sepet boş değilse toplamı hesapla
                            foreach ($sepet_urunleri as $urun) {
                                $toplam_tutar += $urun['fiyat'] * $urun['adet'];
                            }
                        }
                        ?>
                        <h3>Genel Toplam: ₺<?= number_format($toplam_tutar, 2, ',', '.') ?></h3>
                        <?php if (!empty($sepet_urunleri)): // Sadece sepet doluysa butonu göster ?>
                            <a href="odeme.php" class="add-to-cart">Siparişi Tamamla</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script>
    // Sticky footer için JavaScript gerekmez, CSS ile çözülür.
    // Sadece bildirim mesajını gizlemek için:
    function hideNotification(elementId) {
        var mesaj = document.getElementById(elementId);
        if (mesaj) {
            mesaj.style.transition = "opacity 0.5s ease";
            mesaj.style.opacity = 0;
            setTimeout(function() {
                if (mesaj.parentNode) {
                    mesaj.parentNode.removeChild(mesaj);
                }
            }, 500);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('sepet-silme-bildirimi')) {
            setTimeout(function() { hideNotification('sepet-silme-bildirimi'); }, 3000);
        }
    });
    </script>
</body>
</html>