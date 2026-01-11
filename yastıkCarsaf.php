<?php
session_start();
include("baglanti.php");

$favori_urun_idler = [];
if (isset($_SESSION['kullanici_id'])) {
    $kullanici_id = $_SESSION['kullanici_id'];
    $fav_sorgu = $conn->prepare("SELECT urun_id FROM favoriler WHERE kullanici_id = ?");
    $fav_sorgu->bind_param("i", $kullanici_id);
    $fav_sorgu->execute();
    $fav_sonuc = $fav_sorgu->get_result();
    while ($fav_row = $fav_sonuc->fetch_assoc()) {
        $favori_urun_idler[] = $fav_row['urun_id'];
    }
    $fav_sorgu->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreli Çeyiz Online Satış Sitesi - Nevresimler</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon">
    <script defer src="scripts.js"></script>
    <style>
        .fav-btn.favorited {
            color: red; /* Favoriye eklenmiş kalp rengi */
        }
        .fav-btn {
            font-size: 1.5em;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0 5px;
            vertical-align: middle;
        }
        .product-actions {
            display: flex;
            align-items: center;
            justify-content: center; /* veya space-around */
            margin-top: 10px;
        }
        .product-actions form {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php
    if (isset($_GET['ekleme']) && $_GET['ekleme'] === 'ok') {
        echo '<div id="sepet-bildirimi" style=" /* ... stiller ... */ ">✅ Ürün sepete eklendi</div>';
    }
    if (isset($_GET['fav_durum'])) {
        if ($_GET['fav_durum'] === 'eklendi') {
             echo '<div id="fav-bildirimi" style="background: #d4edda; color: #155724; padding: 10px; text-align:center; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px auto; width: 50%; font-weight: bold; z-index: 9999;">❤️ Ürün favorilere eklendi.</div>';
        } elseif ($_GET['fav_durum'] === 'kaldirildi') {
             echo '<div id="fav-bildirimi" style="background: #f8d7da; color: #721c24; padding: 10px; text-align:center; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px auto; width: 50%; font-weight: bold; z-index: 9999;">💔 Ürün favorilerden kaldırıldı.</div>';
        }
    }
    ?>
     <?php
    // Sepete ekleme bildirimi
    if (isset($_GET['sepet_islem'])) {
        $sepet_mesaji = "";
        $sepet_mesaj_class = "";
        if ($_GET['sepet_islem'] === 'eklendi') {
            $sepet_mesaji = '✅ Ürün başarıyla sepetinize eklendi!';
            $sepet_mesaj_class = 'bildirim-basarili';
        } elseif ($_GET['sepet_islem'] === 'hata') {
            $sepet_mesaji = '❌ Ürün sepete eklenirken bir sorun oluştu.';
            $sepet_mesaj_class = 'bildirim-hata';
        }
        // Eğer başka sepet işlem türleri olursa buraya eklenebilir (örn: güncellendi, silindi vs.)

        if (!empty($sepet_mesaji)) {
            echo '<div id="sepet-bildirimi" class="bildirim-mesaji ' . $sepet_mesaj_class . '">' . $sepet_mesaji . '</div>';
        }
    }

    // Favori işlem sonucu bildirimi (Bu kısım olduğu gibi kalıyor)
    if (isset($_GET['fav_islem_sonucu'])) {
        // ... (önceki favori bildirim kodunuz) ...
        $fav_sonuc_mesaji = "";
        $fav_mesaj_class = "";
        switch ($_GET['fav_islem_sonucu']) {
            case 'eklendi':
                $fav_sonuc_mesaji = '❤️ Ürün favorilere eklendi.';
                $fav_mesaj_class = 'bildirim-basarili';
                break;
            case 'kaldirildi':
                $fav_sonuc_mesaji = '💔 Ürün favorilerden kaldırıldı.';
                $fav_mesaj_class = 'bildirim-uyari';
                break;
            // ... diğer case'ler ...
            default:
                $fav_sonuc_mesaji = '❌ Favori işleminde bir sorun oluştu.';
                $fav_mesaj_class = 'bildirim-hata';
                break;
        }
        if (!empty($fav_sonuc_mesaji)) {
            echo '<div id="fav-bildirimi" class="bildirim-mesaji ' . $fav_mesaj_class . '">' . $fav_sonuc_mesaji . '</div>';
        }
    }
    ?>
    <main>
        <section id="products">
            <h2>YastikCarsaf</h2>
            <div class="product-grid">
            <?php
            $query = "SELECT * FROM urunler WHERE kategori = 'YastikCarsaf'";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $urun_id = $row["id"];
                $is_favorited = in_array($urun_id, $favori_urun_idler);
                $fav_icon = $is_favorited ? '❤️' : '🤍'; // Dolu kalp / Boş kalp (veya farklı SVG ikonları)
                $fav_class = $is_favorited ? 'favorited' : '';

                echo '<div class="product-item" data-name="' . htmlspecialchars($row["marka"]) . '">';
                echo '<img src="' . htmlspecialchars($row["resim_yolu"]) . '" alt="' . htmlspecialchars($row["marka"]) . '">';
                echo '<div class="product-info">';
                echo '<h4>' . htmlspecialchars($row["marka"]) . '</h4>';
                echo '<p>' . htmlspecialchars($row["aciklama"]) . '</p>';
                echo '<p style="color:rosybrown; font-size:x-large;"><strong>₺' . number_format($row["fiyat"], 2, ',', '.') . '</strong></p>';

                echo '<div class="product-actions">';
                // Favorilere Ekle/Kaldır Butonu (AJAX ile geliştirilebilir, şimdilik form submit)
                echo '<form method="post" action="favori_toggle_action.php" class="fav-form" style="display:inline-block;">
                        <input type="hidden" name="urun_id" value="' . htmlspecialchars($urun_id) . '">
                        <input type="hidden" name="return_url" value="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
                        <button type="submit" class="fav-btn ' . $fav_class . '" title="Favorilere Ekle/Kaldır">' . $fav_icon . '</button>
                      </form>';

                // Sepete Ekle Butonu
                echo '<form action="sepeteEkle.php" method="POST" style="display:inline-block;">
                        <input type="hidden" name="urun_id" value="' . htmlspecialchars($urun_id) . '">
                        <button class="add-to-cart" type="submit">Sepete Ekle</button>
                      </form>';
                echo '</div>'; // product-actions sonu

                echo '</div></div>';
            }
            ?>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script>
    // Sepet bildirimi için
    setTimeout(function() {
        var mesaj = document.getElementById('sepet-bildirimi');
        if (mesaj) {
            mesaj.style.transition = "opacity 0.5s ease";
            mesaj.style.opacity = 0;
            setTimeout(function() { mesaj.remove(); }, 500);
        }
    }, 3000);

    // Favori bildirimi için
    setTimeout(function() {
        var favMesaj = document.getElementById('fav-bildirimi');
        if (favMesaj) {
            favMesaj.style.transition = "opacity 0.5s ease";
            favMesaj.style.opacity = 0;
            setTimeout(function() { favMesaj.remove(); }, 500);
        }
    }, 3000);
    </script>
</body>
</html>