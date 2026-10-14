<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'About';
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Our Journey</span>
        <h2>About Food Hub</h2>
        <p>Our Story, Our Passion, Our Promise</p>
    </div>

    <div class="panel mb-3">
        <div class="about-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:34px;align-items:center">
            <div>
                <h3 style="color:var(--primary);margin-bottom:14px;font-family:'Playfair Display',serif;font-size:1.5rem">Our Story</h3>
                <p style="color:var(--ink-600);margin-bottom:14px;line-height:1.8">
                    Founded in 2010, Food Hub began as a small family-owned restaurant with a simple mission: to serve delicious, high-quality food in a warm and welcoming atmosphere. What started as a humble eatery has grown into one of the city's most beloved restaurants.
                </p>
                <p style="color:var(--ink-600);line-height:1.8">
                    Our founders, Chef Ananda and his wife Maya, combined traditional recipes with modern culinary techniques to create a unique dining experience that celebrates both local flavors and international cuisine.
                </p>
            </div>
            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&q=80" alt="Restaurant Interior" style="border-radius:18px;width:100%">
        </div>
    </div>

    <div class="panel mb-3">
        <h3 style="color:var(--primary);margin-bottom:14px;font-family:'Playfair Display',serif;font-size:1.5rem">Our Mission</h3>
        <p style="color:var(--ink-600);line-height:1.8;margin-bottom:14px">
            At Food Hub, our mission is simple: to provide exceptional dining experiences through outstanding food, impeccable service, and a welcoming atmosphere. We believe that great food has the power to bring people together and create lasting memories.
        </p>
        <p style="color:var(--ink-600);line-height:1.8">
            We are committed to using only the freshest, locally sourced ingredients, supporting our community's farmers and producers while ensuring the highest quality in every dish we serve.
        </p>
    </div>

    <div class="feature-grid">
        <div class="feature"><div class="feature-ic green"><i class="fa-solid fa-leaf"></i></div><h4>Fresh Ingredients</h4><p>Locally sourced, always seasonal</p></div>
        <div class="feature"><div class="feature-ic"><i class="fa-solid fa-heart"></i></div><h4>Passionate Chefs</h4><p>Years of culinary expertise</p></div>
        <div class="feature"><div class="feature-ic blue"><i class="fa-solid fa-users"></i></div><h4>Community Focus</h4><p>Supporting local farmers</p></div>
        <div class="feature"><div class="feature-ic amber"><i class="fa-solid fa-award"></i></div><h4>Award Winning</h4><p>Recognized for excellence</p></div>
    </div>
</div>
<style>@media(max-width:800px){.about-grid{grid-template-columns:1fr!important}}</style>
<?php include 'includes/footer.php'; ?>