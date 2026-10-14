<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'Menu';
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Explore</span>
        <h2>Our Menu</h2>
        <p>Handpicked dishes crafted with love</p>
    </div>

    <div class="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="menuSearch" placeholder="Search for a dish…" autocomplete="off">
        <button class="search-clear" id="searchClear"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="filters">
        <button class="chip on" data-category="all"><i class="fa-solid fa-border-all"></i> All</button>
        <?php $cats = $conn->query("SELECT * FROM categories ORDER BY name"); while ($c = $cats->fetch_assoc()): ?>
            <button class="chip" data-category="<?= $c['id'] ?>"><i class="fa-solid <?= e($c['icon']) ?>"></i> <?= e($c['name']) ?></button>
        <?php endwhile; ?>
    </div>

    <div class="menu-grid" id="menuItems">
    <?php
    $res = $conn->query("SELECT m.*, c.name AS cat FROM menu_items m LEFT JOIN categories c ON m.category_id=c.id WHERE m.is_available=1 ORDER BY m.category_id, m.name");
    while ($it = $res->fetch_assoc()):
        $img = !empty($it['image_url']) ? 'images/' . $it['image_url'] : 'images/default-food.jpg';
    ?>
        <div class="card reveal" data-category="<?= (int)$it['category_id'] ?>" data-name="<?= e(strtolower($it['name'])) ?>">
            <div class="card-img">
                <span class="badge"><?= e($it['cat'] ?? 'Other') ?></span>
                <?php if ($it['is_featured']): ?><span class="badge-feat"><i class="fa-solid fa-crown"></i> Featured</span><?php endif; ?>
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

    <div id="menuEmpty" class="empty" style="display:none">
        <i class="fa-solid fa-magnifying-glass"></i>
        <h3>No dishes found</h3>
        <p>Try a different search or category.</p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>