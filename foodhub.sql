DROP DATABASE IF EXISTS foodhub;
CREATE DATABASE foodhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodhub;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fa-utensils',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) DEFAULT 0,
    delivery_fee DECIMAL(10,2) DEFAULT 250,
    tax DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    phone VARCHAR(20),
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    item_name VARCHAR(100),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name, description, icon) VALUES
('Breakfast','Morning delights','fa-mug-saucer'),
('Lunch','Hearty midday meals','fa-bowl-food'),
('Dinner','Evening specials','fa-utensils'),
('Desserts','Sweet treats','fa-ice-cream'),
('Beverages','Drinks & refreshments','fa-mug-hot');

-- Password for both users: admin123
INSERT INTO users (username, email, password, user_type) VALUES
('admin','admin@foodhub.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('john_doe','john@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','customer');

INSERT INTO menu_items (name, description, price, category_id, image_url, is_featured) VALUES
('Cheese Pizza','Delicious pizza with mozzarella cheese and tomato sauce',1200.00,3,'pizza.jpg',1),
('Grilled Chicken','Juicy grilled chicken with herbs and spices',1800.00,2,'chicken.jpg',1),
('Chocolate Cake','Rich chocolate cake with creamy frosting',600.00,4,'cake.jpg',1),
('Fresh Orange Juice','Freshly squeezed orange juice',350.00,5,'juice.jpg',0),
('Vegetable Salad','Fresh garden salad with assorted vegetables',850.00,2,'salad.jpg',0),
('Spaghetti Carbonara','Classic pasta with creamy sauce and bacon',1400.00,3,'pasta.jpg',1),
('Beef Burger','Juicy beef patty with fresh vegetables',1100.00,2,'burger.jpg',1),
('French Fries','Crispy golden fries',400.00,2,'fries.jpg',0),
('Ice Cream Sundae','Vanilla ice cream with chocolate sauce',550.00,4,'icecream.jpg',1),
('Coffee','Freshly brewed coffee',300.00,5,'coffee.jpg',0);

INSERT INTO feedback (user_id, rating, comment) VALUES
(2,5,'Excellent food and service! Will definitely come again.'),
(2,4,'Great taste but delivery was a bit late.');