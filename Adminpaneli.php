<?php
    session_start();
    
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Paneli</title>
  <link rel="stylesheet" href="Adminpaneli.css">
</head>
<body>
  <div class="container">
    <h1>Admin Paneli</h1>
    <form action="urunEkle.php" method="POST" enctype="multipart/form-data">
      <label for="product-name">Ürün Adı:</label>
      <input type="text" id="product-name" name="product-name" placeholder="Ürün adı giriniz">

      <label for="product-description">Ürün Açıklaması:</label>
      <textarea id="product-description" name="product-description" placeholder="Ürün açıklamasını giriniz" rows="4" cols="50"></textarea>


      <label for="product-price">Ürün Fiyatı:</label>
      <input type="number" id="product-price" name="product-price" placeholder="Ürün fiyatı giriniz">

      <label for="product-category">Kategori Seçin:</label>
      <select id="product-category" name="product-category">
        <option value="Nevresim Takımları">Nevresim</option>
        <option value="Battaniyeler">Battaniye</option>
        <option value="HavluBornoz">Havlu & Bornoz</option>
        <option value="UykuSeti">Uyku Seti</option>
        <option value="YastikCarsaf">Yastık Kılıfı & Çarşaf</option>
      </select>

      <label for="product-image" class="file-upload-label">Ürün Fotoğrafı Seçin</label>
      <input type="file" id="product-image" name="product-image" accept="image/*" style="display:none;" onchange="updateFileName()" required>
      
      <span id="file-name">Fotoğraf Seçilmedi</span>

      <button type="submit">Kaydet</button>
    </form>
  </div>

  <script>
    // Dosya seçildiğinde ismini güncelleyen fonksiyon
    function updateFileName() {
      const fileInput = document.getElementById('product-image');
      const fileName = fileInput.files[0] ? fileInput.files[0].name : 'Fotoğraf Seçilmedi';
      document.getElementById('file-name').textContent = fileName;
    }

    // Formu submit ettikten sonra bilgileri konsola yazdırır
document.querySelector('form').addEventListener('submit', function(e) {
  // e.preventDefault(); // Bu satırı Kaldır! 

  const productName = document.getElementById('product-name').value;
  const productPrice = document.getElementById('product-price').value;
  const productQuantity = document.getElementById('product-quantity').value;
  const productImage = document.getElementById('product-image').files[0];

  console.log("Ürün Adı:", productName);
  console.log("Ürün Fiyatı:", productPrice);
  console.log("Ürün Adeti:", productQuantity);
  if (productImage) {
    const imageURL = URL.createObjectURL(productImage); 
    console.log("Ürün Fotoğrafı URL:", imageURL);
  }

  // Formu sıfırlayıp fotoğraf ismini sıfırlar
  document.querySelector('form').reset();
  document.getElementById('file-name').textContent = 'Fotoğraf Seçilmedi';
});

  </script>
</body>
</html>
