<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'Cart';
$isGuest = !isLoggedIn();
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Your Order</span>
        <h2>Shopping Cart</h2>
        <p><?= $isGuest ? 'Browsing as guest — sign in required to checkout' : 'Review your items before checkout' ?></p>
    </div>

    <?php if ($isGuest): ?>
        <div class="guest-notice">
            <div class="guest-notice-icon"><i class="fa-solid fa-circle-info"></i></div>
            <div class="guest-notice-body">
                <h4>You're browsing as a guest</h4>
                <p>You can add items to your cart freely. To place an order, please
                    <a href="#" data-open-auth="login">sign in</a> or
                    <a href="#" data-open-auth="signup">create an account</a>.
                </p>
            </div>
            <div class="guest-notice-actions">
                <button class="btn btn-primary btn-sm" data-open-auth="login"><i class="fa-solid fa-right-to-bracket"></i> Sign In</button>
                <button class="btn btn-outline btn-sm" data-open-auth="signup"><i class="fa-solid fa-user-plus"></i> Sign Up</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="cart-page" style="display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:24px;margin:30px 0 50px;align-items:flex-start">
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-bag-shopping"></i> Cart Items</div>
            <div id="cartPageItems"></div>
        </div>

        <aside class="panel" style="position:sticky;top:100px">
            <div class="panel-title"><i class="fa-solid fa-receipt"></i> Order Summary</div>
            <div class="sum-row"><span>Subtotal</span><span id="cartSubtotal">Rs. 0.00</span></div>
            <div class="sum-row"><span>Delivery Fee</span><span id="cartFee">Rs. 250.00</span></div>
            <div class="sum-row"><span>Tax (8%)</span><span id="cartTax">Rs. 0.00</span></div>
            <div class="sum-total"><span>Total</span><span id="cartTotal">Rs. 0.00</span></div>

            <?php if ($isGuest): ?>
                <button class="btn btn-primary btn-block mt-3" data-open-auth="login"><i class="fa-solid fa-right-to-bracket"></i> Sign In to Checkout</button>
                <button class="btn btn-outline btn-block mt-2" data-open-auth="signup"><i class="fa-solid fa-user-plus"></i> Create Account</button>
                <p class="guest-hint mt-2"><i class="fa-solid fa-lock"></i> Your cart will be saved after sign in</p>
            <?php else: ?>
                <button id="openCheckout" class="btn btn-primary btn-block mt-3"><i class="fa-solid fa-credit-card"></i> Proceed to Checkout</button>
            <?php endif; ?>

            <button onclick="if(confirm('Clear your cart?')){Cart.clear();location.reload();}" class="btn btn-outline btn-block mt-2">
                <i class="fa-solid fa-trash-can"></i> Clear Cart
            </button>
        </aside>
    </div>
</div>

<?php if (!$isGuest): ?>
<div class="modal" id="checkoutModal">
    <div class="modal-box">
        <button class="icon-btn modal-close" data-close><i class="fa-solid fa-xmark"></i></button>
        <h2>Checkout</h2>
        <p class="sub">Enter your delivery details to place the order</p>
        <form id="checkoutForm">
            <div class="field">
                <label for="delivery_address"><i class="fa-solid fa-location-dot"></i> Delivery Address</label>
                <textarea id="delivery_address" name="delivery_address" class="input" rows="3" required></textarea>
            </div>
            <div class="field">
                <label for="phone"><i class="fa-solid fa-phone"></i> Phone Number</label>
                <input type="tel" id="phone" name="phone" class="input" required>
            </div>
            <div class="field">
                <label for="notes"><i class="fa-solid fa-note-sticky"></i> Additional Notes (Optional)</label>
                <textarea id="notes" name="notes" class="input" rows="2"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-close style="flex:1">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-check"></i> Place Order</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<style>@media(max-width:960px){.cart-page{grid-template-columns:1fr!important}}</style>
<?php include 'includes/footer.php'; ?>