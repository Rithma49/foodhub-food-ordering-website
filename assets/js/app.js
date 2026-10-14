/* ============================================================
   FOOD HUB — Client App
   ============================================================ */
(function () {
  'use strict';

  const APP = window.APP || {
    baseUrl: '/', loggedIn: false, isAdmin: false,
    deliveryFee: 250, taxRate: 0.08, currentPage: ''
  };
  const CART_KEY = 'foodhub_cart_v1';
  const THEME_KEY = 'foodhub_theme';

  /* -------- Theme -------- */
  const Theme = {
    init() {
      const saved = localStorage.getItem(THEME_KEY) || 'light';
      document.documentElement.setAttribute('data-theme', saved);
      this.updateIcon(saved);
    },
    toggle() {
      const cur = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', cur);
      localStorage.setItem(THEME_KEY, cur);
      this.updateIcon(cur);
    },
    updateIcon(t) {
      const btn = document.getElementById('themeToggle');
      if (!btn) return;
      btn.innerHTML = t === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
  };

  /* -------- Toast -------- */
  const Toast = {
    stack: null,
    init() {
      if (this.stack) return;
      this.stack = document.createElement('div');
      this.stack.className = 'toast-stack';
      document.body.appendChild(this.stack);
    },
    show(msg, type = 'success', duration = 3000) {
      this.init();
      const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
      const el = document.createElement('div');
      el.className = `toast ${type}`;
      el.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><span>${escapeHtml(msg)}</span>`;
      this.stack.appendChild(el);
      setTimeout(() => { el.classList.add('hide'); setTimeout(() => el.remove(), 320); }, duration);
    }
  };
  window.Toast = Toast;

  /* -------- Cart -------- */
  const Cart = {
    get() { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; } },
    save(items) { localStorage.setItem(CART_KEY, JSON.stringify(items)); this.sync(); document.dispatchEvent(new CustomEvent('cart:updated')); },
    add(item) {
      const cart = this.get();
      const ex = cart.find(i => String(i.id) === String(item.id));
      if (ex) ex.quantity += 1;
      else cart.push({ ...item, quantity: 1 });
      this.save(cart);
      Toast.show(`${item.name} added to cart`);
    },
    remove(id) { this.save(this.get().filter(i => String(i.id) !== String(id))); },
    qty(id, delta) {
      const cart = this.get();
      const item = cart.find(i => String(i.id) === String(id));
      if (!item) return;
      item.quantity += delta;
      if (item.quantity < 1) return this.remove(id);
      this.save(cart);
    },
    clear() { localStorage.removeItem(CART_KEY); this.sync(); document.dispatchEvent(new CustomEvent('cart:updated')); },
    subtotal() { return this.get().reduce((s, i) => s + i.price * i.quantity, 0); },
    count() { return this.get().reduce((s, i) => s + i.quantity, 0); },
    totals() {
      const sub = this.subtotal();
      return { sub, fee: APP.deliveryFee, tax: sub * APP.taxRate, total: sub + APP.deliveryFee + sub * APP.taxRate };
    },
    sync() {
      const c = this.count();
      document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = c;
        el.classList.remove('bump');
        void el.offsetWidth;
        el.classList.add('bump');
      });
    }
  };
  window.Cart = Cart;

  /* -------- Auth Modal -------- */
  function initAuthModal() {
    const modal = document.getElementById('authModal');
    if (!modal) return;

    const openBtn = document.getElementById('openAuth');
    const closeBtn = document.getElementById('authClose');
    const tabs = modal.querySelectorAll('.auth-tab');
    const forms = modal.querySelectorAll('.auth-form');
    const indicator = document.getElementById('authIndicator');
    const alertBox = document.getElementById('authAlert');
    const title = document.getElementById('authTitle');
    const subtitle = document.getElementById('authSubtitle');

    const TITLES = {
      login: ['Welcome Back', 'Sign in to continue to your account'],
      signup: ['Create Account', 'Join Food Hub and start ordering']
    };

    const show = (tab = 'login') => {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
      switchTab(tab);
      setTimeout(() => {
        const f = modal.querySelector('.auth-form.active input');
        f && f.focus();
      }, 150);
    };
    const hide = () => {
      modal.classList.remove('show');
      document.body.style.overflow = '';
      clearAlert();
    };
    const clearAlert = () => { alertBox.className = 'auth-alert'; alertBox.innerHTML = ''; };
    const showAlert = (msg, type = 'error') => {
      alertBox.className = `auth-alert show ${type}`;
      const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
      alertBox.innerHTML = `<i class="fa-solid ${icon}"></i><span>${escapeHtml(msg)}</span>`;
    };
    const switchTab = (tab) => {
      tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
      indicator.classList.toggle('right', tab === 'signup');
      forms.forEach(f => f.classList.toggle('active', f.dataset.form === tab));
      if (TITLES[tab]) {
        title.textContent = TITLES[tab][0];
        subtitle.textContent = TITLES[tab][1];
      }
      clearAlert();
    };

    openBtn && openBtn.addEventListener('click', () => show('login'));
    closeBtn && closeBtn.addEventListener('click', hide);
    modal.addEventListener('click', e => { if (e.target === modal) hide(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal.classList.contains('show')) hide(); });
    tabs.forEach(t => t.addEventListener('click', () => switchTab(t.dataset.tab)));
    modal.querySelectorAll('[data-switch]').forEach(a => a.addEventListener('click', e => {
      e.preventDefault();
      switchTab(a.dataset.switch);
    }));

    modal.querySelectorAll('.pw-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;
        const isPw = input.type === 'password';
        input.type = isPw ? 'text' : 'password';
        btn.innerHTML = isPw ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
      });
    });

    window.AuthModal = { show, hide, switchTab };

    modal.querySelectorAll('.auth-form').forEach(form => {
      form.addEventListener('submit', async e => {
        e.preventDefault();
        clearAlert();
        const submitBtn = form.querySelector('button[type="submit"]');
        const orig = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Please wait…';

        const payload = Object.fromEntries(new FormData(form).entries());
        payload.action = form.dataset.form;

        try {
          const res = await fetch(APP.baseUrl + 'api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!data.success) {
            showAlert(data.message || 'Something went wrong', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = orig;
            return;
          }
          showAlert(data.message + ' Redirecting…', 'success');
          Toast.show(data.message, 'success');
          setTimeout(() => {
            if (data.redirect) window.location.href = data.redirect;
            else window.location.reload();
          }, 800);
        } catch (err) {
          console.error(err);
          showAlert('Network error. Please try again.', 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = orig;
        }
      });
    });

    document.querySelectorAll('[data-open-auth]').forEach(b => {
      b.addEventListener('click', e => {
        e.preventDefault();
        show(b.dataset.openAuth || 'login');
      });
    });
  }

  /* -------- Mobile Nav -------- */
  function initMobileNav() {
    const t = document.getElementById('menuToggle');
    const m = document.getElementById('navMenu');
    if (!t || !m) return;
    t.addEventListener('click', () => {
      t.classList.toggle('open');
      m.classList.toggle('open');
      document.body.style.overflow = m.classList.contains('open') ? 'hidden' : '';
    });
    m.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      if (innerWidth <= 900) {
        t.classList.remove('open');
        m.classList.remove('open');
        document.body.style.overflow = '';
      }
    }));
  }

  /* -------- Nav scroll -------- */
  function initNavScroll() {
    const n = document.getElementById('nav');
    if (!n) return;
    const fn = () => n.classList.toggle('scrolled', scrollY > 10);
    addEventListener('scroll', fn, { passive: true });
    fn();
  }

  /* -------- Back-to-top -------- */
  function initFabTop() {
    const fab = document.getElementById('fabTop');
    if (!fab) return;
    addEventListener('scroll', () => fab.classList.toggle('show', scrollY > 500), { passive: true });
    fab.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* -------- Add to cart -------- */
  function initAddButtons() {
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', e => {
        e.preventDefault();
        Cart.add({
          id: btn.dataset.id,
          name: btn.dataset.name,
          price: parseFloat(btn.dataset.price)
        });
      });
    });
  }

  /* -------- Cart drawer -------- */
  function initCartDrawer() {
    const drawer = document.getElementById('cartDrawer');
    const overlay = document.getElementById('overlay');
    const toggle = document.getElementById('cartToggle');
    const close = document.getElementById('cartClose');
    const body = document.getElementById('cartBody');
    const foot = document.getElementById('cartFoot');
    if (!drawer) return;

    const open = () => { drawer.classList.add('open'); overlay.classList.add('show'); render(); };
    const hide = () => { drawer.classList.remove('open'); overlay.classList.remove('show'); };

    toggle && toggle.addEventListener('click', open);
    close && close.addEventListener('click', hide);
    overlay && overlay.addEventListener('click', hide);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });

    function render() {
      const items = Cart.get();
      const { sub, fee, tax, total } = Cart.totals();
      if (!items.length) {
        body.innerHTML = `<div class="empty"><i class="fa-solid fa-bag-shopping"></i><h3>Your cart is empty</h3><p>Add some delicious items to get started.</p></div>`;
        foot.innerHTML = `<a href="${APP.baseUrl}menu.php" class="btn btn-primary btn-block"><i class="fa-solid fa-utensils"></i> Browse Menu</a>`;
        return;
      }
      body.innerHTML = items.map(it => `
        <div class="cart-line">
          <div>
            <h4>${escapeHtml(it.name)}</h4>
            <div class="unit">Rs. ${it.price.toFixed(2)} each</div>
          </div>
          <div class="qty">
            <button class="qty-btn" data-act="dec" data-id="${it.id}"><i class="fa-solid fa-minus"></i></button>
            <span class="qty-val">${it.quantity}</span>
            <button class="qty-btn" data-act="inc" data-id="${it.id}"><i class="fa-solid fa-plus"></i></button>
          </div>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="line-total">Rs. ${(it.price * it.quantity).toFixed(2)}</div>
            <button class="btn-remove" data-act="rm" data-id="${it.id}"><i class="fa-solid fa-trash-can"></i></button>
          </div>
        </div>
      `).join('');
      body.querySelectorAll('[data-act]').forEach(b => {
        b.addEventListener('click', () => {
          const { act, id } = b.dataset;
          if (act === 'inc') Cart.qty(id, 1);
          else if (act === 'dec') Cart.qty(id, -1);
          else if (act === 'rm') { Cart.remove(id); Toast.show('Item removed', 'info'); }
          render();
        });
      });
      foot.innerHTML = `
        <div class="sum-row"><span>Subtotal</span><span>Rs. ${sub.toFixed(2)}</span></div>
        <div class="sum-row"><span>Delivery</span><span>Rs. ${fee.toFixed(2)}</span></div>
        <div class="sum-row"><span>Tax (8%)</span><span>Rs. ${tax.toFixed(2)}</span></div>
        <div class="sum-total"><span>Total</span><span>Rs. ${total.toFixed(2)}</span></div>
        <a href="${APP.baseUrl}cart.php" class="btn btn-primary btn-block mt-3">
          <i class="fa-solid fa-credit-card"></i> ${APP.loggedIn ? 'Go to Checkout' : 'View Cart'}
        </a>`;
    }
    document.addEventListener('cart:updated', () => { if (drawer.classList.contains('open')) render(); });
  }

  /* -------- Menu filters -------- */
  function initMenuFilters() {
    const grid = document.getElementById('menuItems');
    if (!grid) return;
    const items = Array.from(grid.querySelectorAll('.card'));
    const chips = document.querySelectorAll('.chip');
    const search = document.getElementById('menuSearch');
    const clear = document.getElementById('searchClear');
    const empty = document.getElementById('menuEmpty');
    let cat = 'all', q = '', tId;

    const apply = () => {
      let vis = 0;
      items.forEach(it => {
        const catOk = cat === 'all' || it.dataset.category === cat;
        const textOk = !q || (it.dataset.name || '').includes(q);
        const ok = catOk && textOk;
        it.style.display = ok ? '' : 'none';
        if (ok) vis++;
      });
      if (empty) empty.style.display = vis ? 'none' : 'block';
    };
    chips.forEach(c => c.addEventListener('click', () => {
      chips.forEach(x => x.classList.remove('on'));
      c.classList.add('on');
      cat = c.dataset.category;
      apply();
    }));
    search && search.addEventListener('input', e => {
      clearTimeout(tId);
      tId = setTimeout(() => {
        q = e.target.value.trim().toLowerCase();
        clear && clear.classList.toggle('show', q.length > 0);
        apply();
      }, 100);
    });
    clear && clear.addEventListener('click', () => {
      search.value = ''; q = '';
      clear.classList.remove('show');
      apply();
    });
  }

  /* -------- Reveal -------- */
  function initReveal() {
    const els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
    const io = new IntersectionObserver(entries => {
      entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    els.forEach(el => io.observe(el));
  }

  /* -------- Star picker -------- */
  function initStarPicker() {
    document.querySelectorAll('.star-picker').forEach(picker => {
      const labels = Array.from(picker.querySelectorAll('label'));
      const radios = Array.from(picker.querySelectorAll('input[type="radio"]'));
      if (!labels.length) return;
      const paint = n => labels.forEach((lbl, i) => {
        const ic = lbl.querySelector('i'); if (!ic) return;
        const on = i <= n;
        ic.classList.toggle('fa-solid', on);
        ic.classList.toggle('fa-regular', !on);
        ic.style.color = on ? '#F59E0B' : '';
      });
      const cur = () => radios.findIndex(r => r.checked);
      labels.forEach((lbl, i) => {
        lbl.addEventListener('mouseenter', () => { if (!radios[i].disabled) paint(i); });
        lbl.addEventListener('click', () => { if (!radios[i].disabled) { radios[i].checked = true; paint(i); } });
      });
      picker.addEventListener('mouseleave', () => paint(cur()));
      paint(cur());
    });
  }

  /* -------- Checkout -------- */
  function initCheckout() {
    const open = document.getElementById('openCheckout');
    const modal = document.getElementById('checkoutModal');
    const form = document.getElementById('checkoutForm');
    if (!modal || !form) return;
    const close = () => modal.classList.remove('show');
    modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', close));
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

    open && open.addEventListener('click', () => {
      if (!APP.loggedIn) { window.AuthModal && window.AuthModal.show('login'); return; }
      if (!Cart.get().length) { Toast.show('Your cart is empty', 'error'); return; }
      modal.classList.add('show');
    });

    form.addEventListener('submit', async e => {
      e.preventDefault();
      if (!APP.loggedIn) { window.AuthModal && window.AuthModal.show('login'); return; }
      const btn = form.querySelector('button[type="submit"]');
      const orig = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Placing…';
      const payload = {
        cart: Cart.get(),
        delivery_address: form.delivery_address.value.trim(),
        phone: form.phone.value.trim(),
        notes: (form.notes?.value || '').trim()
      };
      try {
        const res = await fetch(APP.baseUrl + 'process_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          Toast.show('Order placed successfully!');
          Cart.clear();
          setTimeout(() => window.location.href = APP.baseUrl + 'order.php', 800);
        } else {
          Toast.show(data.message || 'Could not place order', 'error', 4000);
          if (data.require_login) window.AuthModal && window.AuthModal.show('login');
        }
      } catch (err) {
        console.error(err);
        Toast.show('Network error. Please try again.', 'error');
      } finally {
        btn.disabled = false;
        btn.innerHTML = orig;
      }
    });
  }

  /* -------- Cart page -------- */
  function renderCartPage() {
    const c = document.getElementById('cartPageItems');
    if (!c) return;
    const subEl = document.getElementById('cartSubtotal');
    const feeEl = document.getElementById('cartFee');
    const taxEl = document.getElementById('cartTax');
    const totEl = document.getElementById('cartTotal');

    const render = () => {
      const items = Cart.get();
      const { sub, fee, tax, total } = Cart.totals();
      if (!items.length) {
        c.innerHTML = `<div class="empty"><i class="fa-solid fa-bag-shopping"></i><h3>Your cart is empty</h3><p class="mb-3">Add some delicious items from our menu.</p><a href="${APP.baseUrl}menu.php" class="btn btn-primary"><i class="fa-solid fa-utensils"></i> Browse Menu</a></div>`;
      } else {
        c.innerHTML = items.map(it => `
          <div class="cart-line">
            <div>
              <h4>${escapeHtml(it.name)}</h4>
              <div class="unit">Rs. ${it.price.toFixed(2)} each</div>
            </div>
            <div class="qty">
              <button class="qty-btn" data-act="dec" data-id="${it.id}"><i class="fa-solid fa-minus"></i></button>
              <span class="qty-val">${it.quantity}</span>
              <button class="qty-btn" data-act="inc" data-id="${it.id}"><i class="fa-solid fa-plus"></i></button>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="line-total">Rs. ${(it.price * it.quantity).toFixed(2)}</div>
              <button class="btn-remove" data-act="rm" data-id="${it.id}"><i class="fa-solid fa-trash-can"></i></button>
            </div>
          </div>
        `).join('');
        c.querySelectorAll('[data-act]').forEach(b => {
          b.addEventListener('click', () => {
            const { act, id } = b.dataset;
            if (act === 'inc') Cart.qty(id, 1);
            else if (act === 'dec') Cart.qty(id, -1);
            else if (act === 'rm') { Cart.remove(id); Toast.show('Item removed', 'info'); }
            render();
          });
        });
      }
      subEl && (subEl.textContent = 'Rs. ' + sub.toFixed(2));
      feeEl && (feeEl.textContent = 'Rs. ' + fee.toFixed(2));
      taxEl && (taxEl.textContent = 'Rs. ' + tax.toFixed(2));
      totEl && (totEl.textContent = 'Rs. ' + total.toFixed(2));
    };
    render();
    document.addEventListener('cart:updated', render);
  }

  /* -------- Utils -------- */
  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
  }

  /* -------- Boot -------- */
  document.addEventListener('DOMContentLoaded', () => {
    Theme.init();
    const tt = document.getElementById('themeToggle');
    tt && tt.addEventListener('click', () => Theme.toggle());

    initAuthModal();
    initMobileNav();
    initNavScroll();
    initFabTop();
    initAddButtons();
    initCartDrawer();
    initMenuFilters();
    initReveal();
    initStarPicker();
    initCheckout();
    renderCartPage();
    Cart.sync();

    if (window.location.hash === '#login' || /[?&]auth=login/.test(location.search)) {
      window.AuthModal && window.AuthModal.show('login');
    }
    if (window.location.hash === '#signup' || /[?&]auth=signup/.test(location.search)) {
      window.AuthModal && window.AuthModal.show('signup');
    }
  });
})();