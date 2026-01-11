// Arama fonksiyonu
document.getElementById('search').addEventListener('input', function(e) {
  const query = e.target.value.toLowerCase();
  const products = document.querySelectorAll('.product-item');
  products.forEach(product => {
      const productName = product.querySelector('h4').textContent.toLowerCase();
      const productDescription = product.querySelector('p')?.textContent.toLowerCase() || ''; // 'p' etiketi yoksa boş string döner
      if (productName.includes(query) || productDescription.includes(query)) {
          product.style.display = 'block';
      } else {
          product.style.display = 'none';
      }
  });
});



//sepet fonk
$(document).ready(function() {
    let count = 0;

    function updateValue() {
      const valueDiv = $('#value');
      valueDiv.text(count);

      if (count >= 0) {
        valueDiv.css('background-color', 'yellow');
        valueDiv.css('color', 'black');
      } 
      else if (count < 0) {
        valueDiv.css('background-color', 'red');
        valueDiv.css('color', 'white');
      } 
    }

    $('#increment').click(function() {
      count++;
      updateValue();
    });

    $('#decrement').click(function() {
      count--;
      updateValue();
    });
  });



  function increaseQuantity(button) {
    const input = button.previousElementSibling;
    let value = parseInt(input.value);
    input.value = value + 1;
}

function decreaseQuantity(button) {
    const input = button.nextElementSibling;
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

// Tüm ürünler için miktar kontrolünü aktif et
document.querySelectorAll('.quantity-control').forEach(function(control) {
  const decreaseBtn = control.querySelector('.decrease-btn');
  const increaseBtn = control.querySelector('.increase-btn');
  const quantityInput = control.querySelector('.quantity-input');

  decreaseBtn.addEventListener('click', function() {
      let currentValue = parseInt(quantityInput.value);
      if (currentValue > 0) { // Miktar 0'den az olamaz
          quantityInput.value = currentValue - 1;
      }
  });

  increaseBtn.addEventListener('click', function() {
      let currentValue = parseInt(quantityInput.value);
      quantityInput.value = currentValue + 1;
  });
});
// animasyon
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;

function showSlide() {
    slides.forEach((slide, index) => {
        slide.style.opacity = (index === currentSlide) ? 1 : 0;
    });
    currentSlide = (currentSlide + 1) % totalSlides;
}

setInterval(showSlide, 2000);
showSlide(); 
