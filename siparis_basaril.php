<?php
session_start();

if (!isset($_SESSION['kullanici_id'])) {
    // Eğer bir şekilde buraya giriş yapmadan gelinirse ana sayfaya yönlendir.
    header("Location: Anasayfa.php");
    exit;
}


$mesaj = $_SESSION['siparis_basarili_mesaji'] ?? 'Siparişiniz başarıyla işleme alınmıştır!'; // Varsayılan mesaj
$siparis_id_goster = $_SESSION['son_siparis_id'] ?? null; // Eğer varsa sipariş ID'si


unset($_SESSION['siparis_basarili_mesaji']);
unset($_SESSION['son_siparis_id']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Alındı! - Koreli Çeyiz</title>
    <link rel="stylesheet" href="styles.css"> <!-- Genel CSS dosyanız -->
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon">
    <style>

        html { height: 100%; }
        body {
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
            display: flex; 
            justify-content: center;
            align-items: center;
            padding: 2em;
            text-align: center; 
        }


        .basari-container {
            max-width: 650px; 
            margin: 0 auto; 
            padding: 2.5em; 
            background-color: #ffffff;
            border-radius: 10px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            text-align: center;
            border-top: 5px solid #4CAF50; 
        }
        .basari-container .icon {
            font-size: 4em; 
            color: #4CAF50; 
            margin-bottom: 0.3em;
        }
        .basari-container h2 {
            color: #333; 
            margin-top: 0;
            margin-bottom: 0.8em;
            font-size: 2.2em;
            font-weight: 600;
        }
        .basari-container p {
            font-size: 1.15em; 
            line-height: 1.7;
            color: #555; 
            margin-bottom: 1em;
        }
        .basari-container .siparis-no-etiket {
            font-weight: normal;
            color: #666;
        }
        .basari-container .siparis-no-deger {
            font-weight: bold;
            color: #007bff; /* Sipariş ID'si için mavi tonu */
            font-size: 1.25em; /* Sipariş ID'si daha belirgin */
            background-color: #e7f3ff; /* Hafif mavi arka plan */
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
        }
        .basari-container .buton-grup {
            margin-top: 2em;
            display: flex;
            justify-content: center;
            gap: 15px; /* Butonlar arası boşluk */
        }
        .basari-container a.btn { /* Genel buton sınıfı */
            display: inline-block;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 1em;
            transition: background-color 0.25s ease, transform 0.15s ease;
        }
        .basari-container a.btn-anasayfa {
            background-color: #cb9869; /* Sitenizin ana rengi */
            color: white;
        }
        .basari-container a.btn-anasayfa:hover {
            background-color: #b08456;
            transform: translateY(-2px);
        }
        .basari-container a.btn-siparislerim {
            background-color: #007bff; /* Mavi tonu */
            color: white;
        }
        .basari-container a.btn-siparislerim:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; // Navbar'ı dahil et ?>

    <main>
        <div class="basari-container">
            <div class="icon">✓</div> <!-- Basit bir checkmark ikonu, SVG veya FontAwesome da kullanılabilir -->
            <h2>Siparişiniz Başarıyla Alındı!</h2>
            <p><?= htmlspecialchars($mesaj) ?></p>

            <?php if ($siparis_id_goster): ?>
                <p class="siparis-no-etiket">Sipariş Numaranız:</p>
                <p><span class="siparis-no-deger"><?= htmlspecialchars($siparis_id_goster) ?></span></p>
            <?php endif; ?>

            <p>Sipariş detaylarınız ve sonraki adımlar hakkında bilgi e-posta adresinize gönderilecektir. (Bu özellik gelecekte eklenecektir.)</p>
            <p>Siparişinizi "Hesabım > Siparişlerim" bölümünden takip edebilirsiniz. (Bu bölüm henüz aktif değildir.)</p>

            <div class="buton-grup">
                <a href="Anasayfa.php" class="btn btn-anasayfa">Alışverişe Devam Et</a>
               
            </div>
        </div>
    </main>

    <?php include 'footer.php'; // Footer'ı dahil et ?>
</body>
</html>