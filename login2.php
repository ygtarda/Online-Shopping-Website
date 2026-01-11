<?php
    session_start();
    
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="login2.css">
</head>
<body>
    <div class="login-container">
        <h2>Giriş Yap</h2>
        <form action="giris2.php" method="POST" >
            <div class="input-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="login.php">Üye Olun</a></p>
    </div>
    <script>
        
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            
            if(username === 'admin' && password === '1234') {
                window.location.href = "adminpaneli.php"; // Başarılı giriş sonrası yönlendirme
                return false; // Sayfanın yenilenmesini engeller
            } else {
                alert("Hfre.");
                return false;
            }
        }
    </script>
</body>
</html>