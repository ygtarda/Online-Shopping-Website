<?php
session_start();
include "baglanti.php";

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];

$sorgu_str = "SELECT u.id, u.marka, u.aciklama, u.fiyat, u.resim_yolu 
              FROM favoriler f 
              JOIN urunler u ON f.urun_id = u.id 
              WHERE f.kullanici_id = ?";
$sorgu = $conn->prepare($sorgu_str);

if (!$sorgu) {
    error_log("Favorilerim sorgusu hazırlanamadı: " . $conn->error);
    die("Veritabanı hatası: Favori ürünler alınamadı.");
}
$sorgu->bind_param("i", $kullanici_id);
if (!$sorgu->execute()) {
    error_log("Favorilerim sorgusu çalıştırılamadı: " . $sorgu->error);
    die("Veritabanı hatası: Favori ürünler yüklenemedi.");
}
$sonuc = $sorgu->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreli Çeyiz Online Satış Sitesi - Favorilerim</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon">
    <style>
        .fav-btn {
            font-size: 1.5em; background: none; border: none; cursor: pointer; padding: 0 5px;
            vertical-align: middle; color: red !important; /* Favorilerim'de hep kırmızı */
            transition: transform 0.2s ease;
        }
        .fav-btn:hover { transform: scale(1.1); }
        .product-actions { display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 10px; }
        .product-actions form { margin: 0; }
        .bildirim-mesaji { /* ... styles.css'ten gelen stil ... */ }
        .bildirim-basarili { /* ... styles.css'ten gelen stil ... */ }
        .bildirim-hata { /* ... styles.css'ten gelen stil ... */ }
        .bildirim-uyari { /* ... styles.css'ten gelen stil ... */ }
        .sepet-bos-mesaji { text-align: center; padding: 20px; width: 100%; font-size: 1.1em; color: #555; }
        .btn-alisverise-basla { /* .icerik-bos-mesaji içindeki buton için stil */
            padding:10px 20px; background-color:#cb9869; color:white; text-decoration:none; 
            border-radius:5px; display: inline-block; font-weight:bold;
        }

    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <main>
        <section id="products">
            <h2>Favori Ürünlerim</h2>

            <?php
            // Bildirimleri gösterme
            $bildirim_mesaji = "";
            $bildirim_class = "";

            // 1. Sepet işlemi bildirimi (sepeteEkle.php'den gelirse)
            if (isset($_GET['sepet_islem'])) {
                if ($_GET['sepet_islem'] === 'eklendi') {
                    $bildirim_mesaji = '✅ Ürün başarıyla sepetinize eklendi!';
                    $bildirim_class = 'bildirim-basarili';
                } elseif ($_GET['sepet_islem'] === 'hata') {
                    $bildirim_mesaji = '❌ Ürün sepete eklenirken bir sorun oluştu.';
                    $bildirim_class = 'bildirim-hata';
                }
                // ID'yi JS'nin kolay bulması için benzersiz yapalım
                if (!empty($bildirim_mesaji)) {
                    echo '<div id="genel-bildirimi" class="bildirim-mesaji ' . $bildirim_class . '">' . $bildirim_mesaji . '</div>';
                }
            }
            // 2. Favori işlemi bildirimi 
            elseif (isset($_GET['fav_islem_sonucu'])) {
                switch ($_GET['fav_islem_sonucu']) {
                    case 'kaldirildi':
                        $bildirim_mesaji = '💔 Ürün favorilerden başarıyla kaldırıldı.';
                        $bildirim_class = 'bildirim-uyari';
                        break;
                    case 'eklendi': // Normalde bu sayfadan bir ürün favoriye eklenmez, sadece kaldırılır
                        $bildirim_mesaji = '❤️ Ürün favorilere eklendi.';
                        $bildirim_class = 'bildirim-basarili';
                        break;
                    case 'giris_gerekli':
                        $bildirim_mesaji = '⚠️ Bu işlem için giriş yapmalısınız.';
                        $bildirim_class = 'bildirim-uyari';
                        break;
                    default:
                        $bildirim_mesaji = '❌ Favori işleminde bir sorun oluştu (' . htmlspecialchars($_GET['fav_islem_sonucu']) . ')';
                        $bildirim_class = 'bildirim-hata';
                        break;
                }
                if (!empty($bildirim_mesaji)) {
                    echo '<div id="genel-bildirimi" class="bildirim-mesaji ' . $bildirim_class . '">' . $bildirim_mesaji . '</div>';
                }
            }
            ?>

            <div class="product-grid">
                <?php if ($sonuc->num_rows === 0): ?>
                    <div class="icerik-bos-mesaji"> <!-- .sepet-bos-mesaji yerine .icerik-bos-mesaji daha genel -->
                        <p>Henüz favorilerinize eklenmiş bir ürün bulunmamaktadır.</p>
                        <p><a href="Anasayfa.php" class="btn-alisverise-basla">Alışverişe Başla</a></p>
                    </div>
                <?php else: ?>
                    <?php while ($urun = $sonuc->fetch_assoc()): ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($urun["resim_yolu"]) ?>" alt="<?= htmlspecialchars($urun["marka"]) ?>">
                            <div class="product-info">
                                <h4><?= htmlspecialchars($urun["marka"]) ?></h4>
                                <p><?= htmlspecialchars($urun["aciklama"]) ?></p>
                                <p class="fiyat"><strong>₺<?= number_format($urun["fiyat"], 2, ',', '.') ?></strong></p>
                                
                                <div class="product-actions">
                                    <form method="post" action="favori_toggle_action.php" class="fav-form">
                                        <input type="hidden" name="urun_id" value="<?= htmlspecialchars($urun['id']); ?>">
                                        <?php 
                                        $current_params = $_GET;
                                        unset($current_params['sepet_islem']); // Eğer varsa, tekrar eklenmesin
                                        unset($current_params['fav_islem_sonucu']); // Eğer varsa, tekrar eklenmesin
                                        $return_url_fav = basename($_SERVER['PHP_SELF']);
                                        if (!empty($current_params)) {
                                            $return_url_fav .= '?' . http_build_query($current_params);
                                        }
                                        ?>
                                        <input type="hidden" name="return_url" value="<?= htmlspecialchars($return_url_fav); ?>">
                                        <button type="submit" class="fav-btn favorited" title="Favorilerden Kaldır">❤️</button>
                                    </form>

                                    <form action="sepeteEkle.php" method="POST">
                                        <input type="hidden" name="urun_id" value="<?= htmlspecialchars($urun["id"]) ?>">
                                        <button class="add-to-cart" type="submit">Sepete Ekle</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php $sorgu->close(); ?>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script>
    function hideNotification(elementId) {
        var mesaj = document.getElementById(elementId);
        if (mesaj) {
            mesaj.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            mesaj.style.opacity = 0;
            setTimeout(function() {
                if (mesaj.parentNode) {
                    mesaj.parentNode.removeChild(mesaj);
                }
            }, 500);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Sadece bir bildirim mesajı alanı olduğu için genel bir ID kullandık
        if (document.getElementById('genel-bildirimi')) {
            setTimeout(function() { hideNotification('genel-bildirimi'); }, 3000);
        }
    });
    </script>
</body>
</html>