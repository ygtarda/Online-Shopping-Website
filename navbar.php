<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="logo">
        <h1 style="margin-left: 40px;">Koreli Çeyiz</h1>
    </div>
    <nav>
        <ul>
            <li><a href="Anasayfa.php">Ana Sayfa</a></li>
            <li><a href="nevresim.php">Nevresimler</a></li>
            <li><a href="battaniye.php">Battaniyeler</a></li>
            <li><a href="yastıkCarsaf.php">Yastık Kılıfı & Çarşaflar</a></li>
            <li><a href="havluBornoz.php">Havlu & Bornoz</a></li>
            <li><a href="uykuSeti.php">Uyku Setleri</a></li>
            
            <li><a href="sepet.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2-fill" viewBox="0 0 16 16">
                        <path d="M5.929 1.757a.5.5 0 1 0-.858-.514L2.217 6H.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h.623l1.844 6.456A.75.75 0 0 0 3.69 15h8.622a.75.75 0 0 0 .722-.544L14.877 8h.623a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1.717L10.93 1.243a.5.5 0 1 0-.858.514L12.617 6H3.383zM4 10a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm4-1a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1" />
                    </svg></a></li>
            <?php if (isset($_SESSION['kullanici_id'])): // Favorilerim linki sadece giriş yapıldıysa görünsün ?>
                <li><a href="favorilerim.php">❤️ Favorilerim</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['kullanici_id'])): ?>
                <li><a href="cikisyap.php" class="logout-btn">Çıkış Yap</a></li>
            <?php else: ?>
                <li><a href="login.php" class="login-btn">Giriş Yap</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <input type="text" id="search" placeholder="Arama yap...">
</header>