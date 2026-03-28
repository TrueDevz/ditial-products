// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DigitalMarket JS Initialized');

    // Wishlist Toggle Logic
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const icon = this.querySelector('i');
            
            fetch('/digitalProducts/handlers/wishlist_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                        icon.style.color = '#ef4444';
                    } else {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                        icon.style.color = 'inherit';
                    }
                } else if (data.status === 'unauthorized') {
                    window.location.href = '/digitalProducts/login.php';
                }
            });
        });
    });

    // Coupon Application Logic
    const couponForm = document.getElementById('coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const code = document.getElementById('coupon-code').value;
            const msgArea = document.getElementById('coupon-message');
            
            fetch('/digitalProducts/handlers/apply_coupon.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `code=${code}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    msgArea.style.color = '#16a34a';
                    msgArea.textContent = `Coupon applied! ${data.discount_percent}% off.`;
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    msgArea.style.color = '#ef4444';
                    msgArea.textContent = data.message;
                }
            });
        });
    }
});
