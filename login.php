<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Ol - Koreli Çeyiz</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Üye Ol</h2>
        <form action="kaydol.php" method="POST" class="login-form">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" required placeholder="Kullanıcı adınızı girin">

            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" required placeholder="E-posta adresinizi girin">

            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required placeholder="Şifrenizi girin">

            <label for="confirm-password">Şifre Tekrarı</label>
            <input type="password" id="confirm-password" name="confirm-password" required placeholder="Şifrenizi tekrar girin">

            <button type="submit" class="submit-btn">Üye Ol</button>
        </form>
        <p>Hesabınız var mı? <a href="login2.php">Giriş Yapın</a></p>
    </div>
</body>
</html>