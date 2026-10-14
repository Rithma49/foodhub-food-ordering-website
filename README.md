# Food Hub — Restaurant Food Ordering System

A full restaurant food ordering website built with PHP and MySQL. Customers 
can browse the menu, add items to cart, and place orders. Admins can manage 
dishes, orders, and reviews — all from one dashboard.

Runs on XAMPP. No internet needed.

---

## About the System

Food Hub is a web-based ordering platform for restaurants. It handles the 
whole cycle from browsing to delivery — customer sees the menu, picks dishes, 
checks out, and the admin fulfills the order.

The main idea is simple: **let anyone look at the menu without forcing them 
to sign up first**. Login only becomes necessary when they're ready to place 
an order. This makes the experience smoother and gets more people to actually 
order.

On the other side, the admin gets everything in one place — menu management, 
order tracking, revenue stats, and customer feedback. No more paper registers 
or spreadsheets.

The whole thing runs locally on XAMPP, which means a small restaurant can run 
it on any office computer without needing a server or hosting.

---

## Features

### For Visitors (no login needed)

- Browse the full menu with photos and prices
- Search for dishes by name in real-time
- Filter by category (Breakfast, Lunch, Dinner, Desserts, Beverages)
- Add items to cart — saved in browser, survives refresh
- View cart with live totals (subtotal, delivery fee, tax)
- See customer reviews and ratings
- Toggle dark mode
- Fully responsive — works on phone, tablet, desktop

### For Customers (after login)

- Everything visitors can do, plus:
- Place orders with delivery address and phone number
- View order history with all past orders
- Track order status (pending → processing → completed)
- See order details with a visual progress tracker
- Submit reviews with interactive star ratings (1–5)
- Edit profile and manage account

### For Admin

**Dashboard:**
- Total orders, revenue, customers, and pending orders at a glance
- Recent orders table with quick access
- Quick action buttons for common tasks

**Menu Management:**
- Add new dishes with image upload
- Edit existing items (name, price, description, category, image)
- Mark items as available or unavailable
- Feature items to highlight them on the homepage
- Delete items safely (order history stays intact)

**Order Management:**
- View all orders from all customers
- Filter and search orders
- Update order status in one click
- View full order details — items, customer info, delivery address

**Feedback Management:**
- Read all customer reviews
- See average rating and distribution
- Delete inappropriate reviews

**Reports:**
- Filter sales by date range
- View top-selling items
- Track revenue and completed orders

### Auth System

- Single "Sign In" button opens a popup modal
- Two tabs — Login and Signup — no separate pages
- AJAX-based (no page reload)
- Password show/hide toggle
- Inline validation and error messages
- Auto-redirect admins to the admin panel after login

### Cart System

- Uses browser localStorage — no database needed
- Add, update quantity, remove items
- Sliding cart drawer accessible from any page
- Auto-calculates subtotal, delivery fee (Rs. 250), and tax (8%)
- Persists across sessions until cleared or checked out

---

## Tech Stack

### Backend

| Technology | Purpose |
|-----------|---------|
| **PHP 8.0+** | Server-side logic, routing, business rules |
| **MySQL 5.7+ / MariaDB** | Database (InnoDB tables, foreign keys) |
| **MySQLi** | Database driver with prepared statements |

### Frontend

| Technology | Purpose |
|-----------|---------|
| **HTML5** | Page structure |
| **CSS3** | Custom design system (CSS variables, flexbox, grid) |
| **JavaScript (ES6+)** | Cart logic, AJAX, UI interactions — no frameworks |
| **Font Awesome 6.5** | All icons (no emojis) |
| **Google Fonts** | Inter (body) + Playfair Display (headings) |

### Server

| Technology | Purpose |
|-----------|---------|
| **Apache** | Web server (via XAMPP) |
| **XAMPP** | Bundle for Windows/Mac/Linux (Apache + MySQL + PHP) |

### Architecture

- **No frameworks** — plain PHP and vanilla JavaScript
- **No Composer** — no external PHP packages
- **No npm** — no build tools or bundlers
- **No CDN dependencies** except Font Awesome and Google Fonts

### Database Schema

Six tables, all with proper foreign keys:

| Table | Purpose |
|-------|---------|
| `users` | Admin and customer accounts |
| `categories` | Menu categories (Breakfast, Lunch, etc.) |
| `menu_items` | All dishes with prices and images |
| `orders` | Order headers (customer, total, status) |
| `order_items` | Individual items in each order |
| `feedback` | Customer reviews with star ratings |

### Security Features

| Layer | Implementation |
|-------|---------------|
| **Password Storage** | `password_hash()` with bcrypt |
| **Password Verification** | `password_verify()` |
| **SQL Injection** | Prepared statements (MySQLi) |
| **XSS Protection** | `htmlspecialchars()` on all output |
| **Session Management** | PHP sessions with server-side validation |
| **Access Control** | Role-based checks (admin vs customer) |
| **Open Redirect** | URL validation before redirects |
| **CSRF** | Same-origin AJAX with session checks |

### File Structure
foodhub/
├── index.php Homepage
├── menu.php Menu with search + filters
├── about.php, contact.php Static pages
├── feedback.php Reviews
├── cart.php Shopping cart
├── order.php Order history
├── order_details.php Order details with tracker
├── process_order.php Order API
├── logout.php
│
├── includes/
│ ├── config.php Database + constants
│ ├── functions.php Helpers
│ ├── auth.php Login/register
│ ├── header.php Navbar + auth modal
│ └── footer.php Footer + scripts
│
├── api/auth.php AJAX login/signup
│
├── admin/
│ ├── index.php Dashboard
│ ├── add_item.php Add dish
│ ├── edit_item.php Manage menu
│ ├── update_item.php Edit dish
│ ├── manage_orders.php Orders
│ ├── view_order.php Order details
│ ├── view_feedback.php Reviews
│ └── reports.php Sales reports
│
├── assets/
│ ├── css/style.css Design system
│ └── js/app.js Client logic
│
├── images/ Uploaded photos
└── foodhub.sql Database schema


### Design System

- **Colors:** Warm palette — primary `#E85D04`, accent `#F59E0B`
- **Spacing:** 8px-based grid system
- **Radii:** Consistent rounded corners (6px to 24px)
- **Shadows:** Four levels (xs, sm, md, lg) for depth
- **Dark Mode:** Full theme with localStorage persistence
- **Animations:** Micro-interactions, reveal-on-scroll, transitions
- **Accessibility:** ESC key, focus rings, ARIA labels, keyboard nav

---

## What You Need

- XAMPP (Apache + MySQL + PHP) — from https://www.apachefriends.org
- PHP 8.0 or newer
- Any modern browser

No frameworks, no Composer, no npm.

---

## Quick Setup

1. Drop the project folder into `C:\xampp\htdocs\foodhub\`
2. Start Apache and MySQL from XAMPP
3. Import `foodhub.sql` via phpMyAdmin
4. Add a `default-food.jpg` to the `images` folder
5. Open `http://localhost/foodhub/`

---

## Login

**Admin:**
- Email: `admin@foodhub.com`
- Password: `admin123`

**Customer:**
- Email: `john@example.com`
- Password: `admin123`

Change these if you're using this for anything real.

---

## Version

Version 1.0.0. Free to use for learning or in a real restaurant. MIT licensed.
