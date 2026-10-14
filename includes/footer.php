</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="<?= url('index.php') ?>" class="brand">
                    <span class="brand-mark"><i class="fa-solid fa-utensils"></i></span>
                    <span class="brand-text" style="color:#fff">Food<em>Hub</em></span>
                </a>
                <p class="mt-2">Experience the finest culinary delights prepared by our expert chefs using fresh, locally sourced ingredients.</p>
                <div class="social-row">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div>
                <h4>Explore</h4>
                <a href="<?= url('menu.php') ?>"><i class="fa-solid fa-chevron-right"></i> Menu</a>
                <a href="<?= url('about.php') ?>"><i class="fa-solid fa-chevron-right"></i> About</a>
                <a href="<?= url('feedback.php') ?>"><i class="fa-solid fa-chevron-right"></i> Reviews</a>
                <a href="<?= url('contact.php') ?>"><i class="fa-solid fa-chevron-right"></i> Contact</a>
            </div>
            <div>
                <h4>Contact</h4>
                <p><i class="fa-solid fa-location-dot"></i> 123 Restaurant St, Colombo</p>
                <p><i class="fa-solid fa-phone"></i> +94 77 123 4567</p>
                <p><i class="fa-solid fa-envelope"></i> info@foodhub.com</p>
            </div>
            <div>
                <h4>Hours</h4>
                <p><i class="fa-regular fa-clock"></i> Mon–Fri · 10AM – 10PM</p>
                <p><i class="fa-regular fa-clock"></i> Sat–Sun · 9AM – 11PM</p>
                <p class="mt-2"><i class="fa-solid fa-shield-halved"></i> Secure payments</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Food Hub Restaurant. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<script>
    window.APP = {
        baseUrl: <?= json_encode(BASE_URL) ?>,
        loggedIn: <?= isLoggedIn() ? 'true' : 'false' ?>,
        isAdmin: <?= currentIsAdmin() ? 'true' : 'false' ?>,
        deliveryFee: <?= DELIVERY_FEE ?>,
        taxRate: <?= TAX_RATE ?>,
        currentPage: <?= json_encode(currentPage()) ?>
    };
</script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>