<?php
session_start();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreli Çeyiz Online Satış Sitesi</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="logo.jpg" type="image/x-icon" >
    <script src="scripts.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <main>
        <section id="carousel">
            <div class="carousel">
                <img src="Nevresim Takımları/brezza145tc-57-tel-pamuk-ranforcetek-kisilik-nevresim-takimi-66228-1.webp" alt="Resim 1">
                <img src="Nevresim Takımları/fiji-pink-ranforce-tek-kisilik-nevresim-takimi-45747124.webp" alt="Resim 2">
                <img src="HavluBornoz/dantela3.webp" alt="Resim 3">
                <img src="Nevresim Takımları/brezza145tc-57-tel-pamuk-ranforcetek-kisilik-nevresim-takimi-66228-1.webp" alt="Resim 1">
                <img src="Nevresim Takımları/fiji-pink-ranforce-tek-kisilik-nevresim-takimi-45747124.webp" alt="Resim 2">
                <img src="HavluBornoz/dantela3.webp" alt="Resim 3">
            </div>
        </section>

        <section id="products">
            <h2>Kategoriler</h2>
            <div class="product-grid">
                <div class="product-item">
                    <a href="nevresim.php"><img src="Nevresim Takımları/issimo17.webp" alt="Ürün 3"></a>
                    <div class="product-info">
                        <h4><a href="nevresim.php" class="no-link-style">Nevresimler</a></h4>
                    </div>
                </div>
                <div class="product-item">
                    <a href="battaniye.php"><img src="Battaniyeler/simply-battaniye-150-200-gul-kurusu-pembe-67446683.webp" alt="Ürün 2"></a>
                    <div class="product-info">
                        <h4><a href="battaniye.php" class="no-link-style">Battaniyeler</a></h4>
                    </div>
                </div>
                <div class="product-item">
                    <a href="havluBornoz.php"><img src="HavluBornoz/dantela1.webp" alt="Ürün 2"></a>
                    <div class="product-info">
                        <h4><a href="havluBornoz.php" class="no-link-style">Havlu & Bornozlar</a></h4>
                    </div>
                </div>
                <div class="product-item">
                    <a href="uykuSeti.php"><img src="UykuSeti/8680108078839-1100x1000.jpg" alt="Ürün 2"></a>
                    <div class="product-info">
                        <h4><a href="uykuSeti.php" class="no-link-style">Uyku Setleri</a></h4>
                    </div>
                </div>
                
                <div class="product-item">
                    <a href="yastıkCarsaf.php"><img src="YastikCarsaf/simply-cherry-rot-pamuk-saten-fitted-set-100-200-50-70-44611276-sw920sh1380.webp" alt="Ürün 4"></a>
                    <div class="product-info">
                        <h4><a href="yastıkCarsaf.php" class="no-link-style">Yastık Kılıfı & Çarşaflar</a></h4>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>