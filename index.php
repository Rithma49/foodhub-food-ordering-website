<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'Home';
include 'includes/header.php';
?>
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge"><i class="fa-solid fa-crown"></i> Voted #1 Restaurant in Colombo</div>
        <h1>Delicious food, <span class="grad">delivered fresh</span> to your door</h1>
        <p class="lede">Experience the finest culinary delights prepared by our expert chefs using fresh, locally sourced ingredients — right from our kitchen to your table.</p>
        <div class="hero-cta">
            <a href="<?= url('menu.php') ?>" class="btn btn-primary"><i class="fa-solid fa-utensils"></i> Explore Menu</a>
            <?php if (!isLoggedIn()): ?>
                <button class="btn btn-dark" data-open-auth="signup"><i class="fa-solid fa-user-plus"></i> Create Account</button>
            <?php else: ?>
                <a href="<?= url('cart.php') ?>" class="btn btn-dark"><i class="fa-solid fa-bag-shopping"></i> View Cart</a>
            <?php endif; ?>
        </div>
        <div class="hero-stats">
            <div class="hero-stat"><div class="num">12k+</div><div class="lbl">Happy Customers</div></div>
            <div class="hero-stat"><div class="num">50+</div><div class="lbl">Signature Dishes</div></div>
            <div class="hero-stat"><div class="num">4.9</div><div class="lbl">Average Rating</div></div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="sec-title">
            <span class="eyebrow">Chef's Selection</span>
            <h2>Featured Dishes</h2>
            <p>Discover our most popular and loved dishes</p>
        </div>
        <div class="menu-grid">
        <?php
        $res = $conn->query("SELECT m.*, c.name AS cat FROM menu_items m LEFT JOIN categories c ON m.category_id=c.id WHERE m.is_available=1 AND m.is_featured=1 ORDER BY RAND() LIMIT 6");
        while ($it = $res->fetch_assoc()):
            $img = !empty($it['image_url']) ? 'images/' . $it['image_url'] : 'images/default-food.jpg';
        ?>
            <div class="card reveal">
                <div class="card-img">
                    <span class="badge"><?= e($it['cat'] ?? 'Other') ?></span>
                    <span class="badge-feat"><i class="fa-solid fa-crown"></i> Featured</span>
                    <img src="<?= e(url($img)) ?>" alt="<?= e($it['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80'">
                </div>
                <div class="card-body">
                    <h3><?= e($it['name']) ?></h3>
                    <p><?= e($it['description']) ?></p>
                    <div class="card-foot">
                        <span class="price"><small>Rs.</small> <?= number_format($it['price'], 2) ?></span>
                        <button class="btn-add btn-add-to-cart" data-id="<?= $it['id'] ?>" data-name="<?= e($it['name']) ?>" data-price="<?= $it['price'] ?>">
                            <i class="fa-solid fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <div class="center mt-4">
            <a href="<?= url('menu.php') ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-right"></i> View Full Menu</a>
        </div>
    </div>
</section>

<section style="background:var(--surface-2)">
    <div class="container">
        <div class="sec-title">
            <span class="eyebrow">Why Food Hub</span>
            <h2>What Makes Us Special</h2>
            <p>We deliver more than just food — we deliver experiences</p>
        </div>
        <div class="feature-grid">
            <div class="feature reveal"><div class="feature-ic green"><i class="fa-solid fa-leaf"></i></div><h4>Fresh Ingredients</h4><p>Only the freshest locally sourced produce in every dish.</p></div>
            <div class="feature reveal"><div class="feature-ic"><i class="fa-solid fa-heart"></i></div><h4>Passionate Chefs</h4><p>Crafted by chefs with years of culinary expertise.</p></div>
            <div class="feature reveal"><div class="feature-ic blue"><i class="fa-solid fa-truck-fast"></i></div><h4>Fast Delivery</h4><p>Hot and fresh food delivered to your door in minutes.</p></div>
            <div class="feature reveal"><div class="feature-ic amber"><i class="fa-solid fa-award"></i></div><h4>Award Winning</h4><p>Multiple awards for culinary excellence and service.</p></div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="sec-title">
            <span class="eyebrow">Testimonials</span>
            <h2>What Our Customers Say</h2>
            <p>Real reviews from real food lovers</p>
        </div>
        <div class="fb-grid">
        <?php
        $fb = $conn->query("SELECT f.*, u.username, DATE_FORMAT(f.created_at,'%M %d, %Y') AS d FROM feedback f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC LIMIT 3");
        if ($fb && $fb->num_rows):
            while ($r = $fb->fetch_assoc()):
        ?>
            <div class="fb-card reveal">
                <div class="fb-head">
                    <div class="avatar"><?= avatarInitial($r['username']) ?></div>
                    <div style="flex:1">
                        <div class="fb-name"><?= e($r['username']) ?></div>
                        <?= renderStars((int)$r['rating']) ?>
                    </div>
                </div>
                <p class="fb-comment">"<?= e($r['comment']) ?>"</p>
                <div class="fb-date mt-2"><?= e($r['d']) ?></div>
            </div>
        <?php endwhile; else: ?>
            <p class="center muted" style="grid-column:1/-1">No reviews yet.</p>
        <?php endif; ?>
        </div>
        <div class="center mt-4">
            <a href="<?= url('feedback.php') ?>" class="btn btn-primary"><i class="fa-solid fa-comment-dots"></i> Share Your Feedback</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>