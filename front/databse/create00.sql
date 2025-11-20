-- Create database
CREATE DATABASE IF NOT EXISTS admin_dashboard;
USE admin_dashboard;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Activities table
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) NOT NULL,
    color VARCHAR(20) NOT NULL,
    parent_id INT DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    product_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);


-- Create pages table
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    meta_description TEXT,
    template VARCHAR(50) DEFAULT 'default',
    status ENUM('published', 'draft') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    created_by INT,
    seo_title VARCHAR(255),
    seo_keywords TEXT
);

-- Create banners table
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    target_url VARCHAR(500),
    position VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive', 'scheduled') DEFAULT 'inactive',
    start_date TIMESTAMP NULL,
    end_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    sort_order INT DEFAULT 0,
    button_text VARCHAR(100),
    button_color VARCHAR(20)
);

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    avatar_color VARCHAR(20) DEFAULT '#4361ee'
);

-- Create addresses table
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    type ENUM('shipping', 'billing') DEFAULT 'shipping',
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipping_address_id INT,
    billing_address_id INT,
    notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id)
);


-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    category_id INT,
    supplier_id INT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    current_stock INT NOT NULL DEFAULT 0,
    min_stock_level INT NOT NULL DEFAULT 5,
    max_stock_level INT NOT NULL DEFAULT 50,
    image_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Create stock_movements table
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    previous_stock INT NOT NULL,
    new_stock INT NOT NULL,
    reason VARCHAR(255),
    reference VARCHAR(100),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create low_stock_alerts table
CREATE TABLE IF NOT EXISTS low_stock_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    current_stock INT NOT NULL,
    min_stock_level INT NOT NULL,
    alert_level ENUM('low', 'critical') NOT NULL,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    customer_email VARCHAR(100),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_date DATE,
    status VARCHAR(20) DEFAULT 'pending'
);

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100),
    price DECIMAL(10,2)
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    category_id INT,
    stock_quantity INT DEFAULT 0,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(500) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE product_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    reviewer_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT,
    order_total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    card_last_four VARCHAR(4),
    status VARCHAR(20) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_date DATE
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT,
    order_total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id)
);

CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE user_addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    order_total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    payment_method VARCHAR(20),
    shipping_address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id)
);

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (user_id, product_id)
);

CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(500) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




-- Sample product
INSERT INTO products (product_name, price, description) VALUES 
('Classic T-Shirt', 29.99, 'A comfortable and stylish classic t-shirt made from 100% cotton.');

-- Sample images
INSERT INTO product_images (product_id, image_url, is_primary) VALUES 
(1, '../images/tshirt-red.jpg', TRUE),
(1, '../images/tshirt-blue.jpg', FALSE),
(1, '../images/tshirt-green.jpg', FALSE);

-- Sample reviews
INSERT INTO product_reviews (product_id, reviewer_name, rating, comment) VALUES 
(1, 'John Doe', 5, 'Great quality and perfect fit!'),
(1, 'Jane Smith', 4, 'Good product, but the color is slightly different from the picture.');

-- This extends the existing toybox_website database
-- Make sure the categories and products tables exist from previous setup

-- Add sample data if not already inserted
INSERT IGNORE INTO categories (name, slug, description) VALUES 
('Action Figures', 'action-figures', 'Collectible action figures from popular movies and TV shows'),
('Building Blocks', 'building-blocks', 'Creative building sets and construction toys'),
('Educational Toys', 'educational-toys', 'Learning toys that develop skills and knowledge'),
('Outdoor Toys', 'outdoor-toys', 'Toys for outdoor play and activities'),
('Board Games', 'board-games', 'Family board games and puzzles'),
('Dolls & Accessories', 'dolls-accessories', 'Dolls, dollhouses, and accessories'),
('Remote Control', 'remote-control', 'RC cars, drones, and remote-controlled toys'),
('Arts & Crafts', 'arts-crafts', 'Creative arts and crafts supplies');

-- Add more sample products if needed
INSERT IGNORE INTO products (name, slug, description, price, original_price, category_id, image_url, stock_quantity, is_featured, rating, review_count) VALUES 
('Super Robot Action Figure', 'super-robot-action-figure', 'Advanced robot action figure with light and sound effects', 29.99, 39.99, 1, 'https://via.placeholder.com/300x300/4361ee/ffffff?text=Robot+Figure', 25, TRUE, 4.5, 34),
('Building Blocks Set - 500 Pieces', 'building-blocks-set-500', 'Creative building blocks set with 500 colorful pieces', 49.99, 59.99, 2, 'https://via.placeholder.com/300x300/f72585/ffffff?text=Building+Blocks', 15, TRUE, 4.8, 28),
('Educational Science Kit', 'educational-science-kit', 'Complete science experiment kit for young explorers', 34.99, NULL, 3, 'https://via.placeholder.com/300x300/4cc9f0/ffffff?text=Science+Kit', 30, TRUE, 4.7, 42),
('Outdoor Play Tent', 'outdoor-play-tent', 'Colorful play tent for backyard adventures', 79.99, 99.99, 4, 'https://via.placeholder.com/300x300/f8961e/ffffff?text=Play+Tent', 8, FALSE, 4.3, 19),
('Family Board Game Collection', 'family-board-game-collection', 'Set of 5 popular family board games', 59.99, 79.99, 5, 'https://via.placeholder.com/300x300/4895ef/ffffff?text=Board+Games', 12, FALSE, 4.6, 31),
('Fashion Doll with Accessories', 'fashion-doll-with-accessories', 'Beautiful fashion doll with clothing and accessory set', 24.99, 29.99, 6, 'https://via.placeholder.com/300x300/7209b7/ffffff?text=Fashion+Doll', 20, TRUE, 4.4, 27),
('RC Racing Car', 'rc-racing-car', 'High-speed remote control racing car', 89.99, 119.99, 7, 'https://via.placeholder.com/300x300/f77f00/ffffff?text=RC+Car', 5, TRUE, 4.9, 38),
('Arts and Crafts Kit', 'arts-and-crafts-kit', 'Complete arts and crafts set with various materials', 19.99, 24.99, 8, 'https://via.placeholder.com/300x300/2ec4b6/ffffff?text=Arts+Crafts', 35, FALSE, 4.2, 23),
('Puzzle Set - 100 Pieces', 'puzzle-set-100-pieces', 'Educational puzzle set with 100 colorful pieces', 14.99, 19.99, 5, 'https://via.placeholder.com/300x300/e71d36/ffffff?text=Puzzle+Set', 18, FALSE, 4.5, 16),
('Dinosaur Action Figures Set', 'dinosaur-action-figures-set', 'Set of 5 realistic dinosaur action figures', 39.99, 49.99, 1, 'https://via.placeholder.com/300x300/3a0ca3/ffffff?text=Dinosaur+Set', 22, TRUE, 4.7, 29),
('LEGO Classic Set', 'lego-classic-set', 'Classic LEGO building set with 650 pieces', 49.99, 59.99, 2, 'https://via.placeholder.com/300x300/4361ee/ffffff?text=LEGO+Set', 10, TRUE, 4.9, 45),
('Drone with Camera', 'drone-with-camera', 'Remote control drone with HD camera', 129.99, 159.99, 7, 'https://via.placeholder.com/300x300/f72585/ffffff?text=Drone', 3, FALSE, 4.6, 22),
('Barbie Dreamhouse', 'barbie-dreamhouse', '3-story dreamhouse for Barbie dolls', 149.99, 199.99, 6, 'https://via.placeholder.com/300x300/4cc9f0/ffffff?text=Dreamhouse', 6, TRUE, 4.8, 51),
('Water Slide Park', 'water-slide-park', 'Inflatable water slide for backyard fun', 89.99, 119.99, 4, 'https://via.placeholder.com/300x300/f8961e/ffffff?text=Water+Slide', 4, FALSE, 4.4, 18),
('Chess Board Set', 'chess-board-set', 'Classic wooden chess board with pieces', 34.99, 44.99, 5, 'https://via.placeholder.com/300x300/4895ef/ffffff?text=Chess+Set', 15, FALSE, 4.7, 26);

-- Insert sample categories
INSERT INTO categories (name, slug) VALUES 
('Electronics', 'electronics'),
('Clothing', 'clothing'),
('Home & Kitchen', 'home-kitchen'),
('Books', 'books'),
('Beauty', 'beauty');

-- Insert sample suppliers
INSERT INTO suppliers (name, email, phone) VALUES 
('Supplier A', 'contact@suppliera.com', '(555) 111-2222'),
('Supplier B', 'sales@supplierb.com', '(555) 333-4444'),
('Supplier C', 'info@supplierc.com', '(555) 555-6666');

-- Insert sample products
INSERT INTO products (name, sku, description, category_id, supplier_id, price, cost_price, current_stock, min_stock_level, max_stock_level, image_url) VALUES 
('Wireless Headphones', 'WH-1000XM4', 'High-quality wireless headphones with noise cancellation', 1, 1, 299.99, 150.00, 42, 5, 50, 'https://via.placeholder.com/50'),
('Smart Watch', 'SW-GT3', 'Advanced smartwatch with health monitoring', 1, 1, 199.99, 100.00, 18, 5, 40, 'https://via.placeholder.com/50'),
('Cotton T-Shirt', 'CT-SM01', 'Comfortable cotton t-shirt in various sizes', 2, 2, 24.99, 8.00, 2, 5, 20, 'https://via.placeholder.com/50'),
('Stainless Steel Water Bottle', 'SSWB-32', 'Durable stainless steel water bottle, 32oz', 3, 3, 29.99, 12.00, 56, 10, 80, 'https://via.placeholder.com/50'),
('Programming Book', 'PB-JS01', 'Complete guide to JavaScript programming', 4, 2, 39.99, 15.00, 1, 3, 20, 'https://via.placeholder.com/50'),
('Wireless Earbuds', 'WE-BD05', 'Compact wireless earbuds with charging case', 1, 1, 129.99, 60.00, 3, 5, 30, 'https://via.placeholder.com/50'),
('Yoga Pants', 'YP-WM02', 'Comfortable yoga pants for women', 2, 2, 49.99, 18.00, 25, 5, 40, 'https://via.placeholder.com/50'),
('Coffee Maker', 'CM-DB01', 'Programmable drip coffee maker', 3, 3, 89.99, 35.00, 12, 3, 25, 'https://via.placeholder.com/50');

-- Insert sample stock movements
INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, reason, reference) VALUES 
(1, 'in', 50, 0, 50, 'Initial stock', 'PO-001'),
(2, 'in', 40, 0, 40, 'Initial stock', 'PO-002'),
(3, 'in', 20, 0, 20, 'Initial stock', 'PO-003'),
(4, 'in', 80, 0, 80, 'Initial stock', 'PO-004'),
(5, 'in', 20, 0, 20, 'Initial stock', 'PO-005'),
(6, 'in', 30, 0, 30, 'Initial stock', 'PO-006'),
(1, 'out', 8, 50, 42, 'Sales', 'SALE-001'),
(2, 'out', 22, 40, 18, 'Sales', 'SALE-002'),
(3, 'out', 18, 20, 2, 'Sales', 'SALE-003'),
(5, 'out', 19, 20, 1, 'Sales', 'SALE-004'),
(6, 'out', 27, 30, 3, 'Sales', 'SALE-005');

-- Insert low stock alerts
INSERT INTO low_stock_alerts (product_id, current_stock, min_stock_level, alert_level) VALUES 
(3, 2, 5, 'low'),
(5, 1, 3, 'critical'),
(6, 3, 5, 'low');

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    subtotal_amount DECIMAL(10,2) NOT NULL,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(100),
    transaction_id VARCHAR(100),
    shipping_address_id INT,
    billing_address_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create order_status_history table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    category_id INT,
    supplier_id INT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    current_stock INT NOT NULL DEFAULT 0,
    min_stock_level INT NOT NULL DEFAULT 5,
    max_stock_level INT NOT NULL DEFAULT 50,
    image_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Create categories table (if not exists from previous setup)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create suppliers table (if not exists from previous setup)
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create promotions table
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(100) NOT NULL UNIQUE,
    type ENUM('discount-code', 'special-offer', 'flash-sale') NOT NULL,
    discount_type ENUM('percentage', 'fixed', 'shipping') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    max_discount_amount DECIMAL(10,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    status ENUM('active', 'inactive', 'expired', 'upcoming') DEFAULT 'active',
    applicable_categories TEXT,
    applicable_products TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create promotion_usage table to track code usage
CREATE TABLE IF NOT EXISTS promotion_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promotion_id INT NOT NULL,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE
);

-- Create reports table to store generated reports
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_type ENUM('sales', 'customers', 'products', 'financial') NOT NULL,
    report_name VARCHAR(255) NOT NULL,
    date_range_start DATE NOT NULL,
    date_range_end DATE NOT NULL,
    file_format ENUM('csv', 'excel', 'pdf') NOT NULL,
    file_path VARCHAR(500),
    generated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES admins(id)
);

-- Insert sample data for reports (this would normally be generated from existing order data)
-- Note: These are sample queries that would run against your existing orders, customers, products tables

-- Sample view for sales report data
CREATE OR REPLACE VIEW sales_report_data AS
SELECT 
    DATE(o.created_at) as sale_date,
    COUNT(o.id) as order_count,
    SUM(o.total_amount) as total_sales,
    AVG(o.total_amount) as avg_order_value,
    COUNT(DISTINCT o.customer_id) as unique_customers
FROM orders o
WHERE o.status IN ('processing', 'shipped', 'delivered')
GROUP BY DATE(o.created_at);

-- Sample view for product performance
CREATE OR REPLACE VIEW product_performance_data AS
SELECT 
    p.id,
    p.name,
    p.sku,
    c.name as category,
    SUM(oi.quantity) as units_sold,
    SUM(oi.total_price) as revenue,
    COUNT(DISTINCT oi.order_id) as order_count
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE o.status IN ('processing', 'shipped', 'delivered')
GROUP BY p.id, p.name, p.sku, c.name;

-- Sample view for customer activity
CREATE OR REPLACE VIEW customer_activity_data AS
SELECT 
    c.id,
    CONCAT(c.first_name, ' ', c.last_name) as customer_name,
    c.email,
    COUNT(o.id) as total_orders,
    SUM(o.total_amount) as total_spent,
    MAX(o.created_at) as last_order_date,
    AVG(o.total_amount) as avg_order_value
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.status IN ('processing', 'shipped', 'delivered') OR o.id IS NULL
GROUP BY c.id, c.first_name, c.last_name, c.email;

-- Sample view for category sales
CREATE OR REPLACE VIEW category_sales_data AS
SELECT 
    c.id,
    c.name as category_name,
    COUNT(DISTINCT o.id) as order_count,
    SUM(oi.total_price) as total_sales,
    SUM(oi.quantity) as units_sold
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id
WHERE o.status IN ('processing', 'shipped', 'delivered')
GROUP BY c.id, c.name;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Create banners table
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    button_text VARCHAR(100),
    button_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (name, slug, description) VALUES 
('Action Figures', 'action-figures', 'Collectible action figures from popular movies and TV shows'),
('Building Blocks', 'building-blocks', 'Creative building sets and construction toys'),
('Educational Toys', 'educational-toys', 'Learning toys that develop skills and knowledge'),
('Outdoor Toys', 'outdoor-toys', 'Toys for outdoor play and activities'),
('Board Games', 'board-games', 'Family board games and puzzles'),
('Dolls & Accessories', 'dolls-accessories', 'Dolls, dollhouses, and accessories'),
('Remote Control', 'remote-control', 'RC cars, drones, and remote-controlled toys'),
('Arts & Crafts', 'arts-crafts', 'Creative arts and crafts supplies');

-- Insert sample banners
INSERT INTO banners (title, description, image_url, button_text, button_url, display_order) VALUES 
('Summer Sale - Up to 50% Off!', 'Get ready for summer with amazing discounts on all outdoor toys', 'https://via.placeholder.com/800x400/4361ee/ffffff?text=Summer+Sale', 'Shop Now', '../productlist/productlist.html', 1),
('New Arrivals', 'Discover the latest and greatest toys for all ages', 'https://via.placeholder.com/800x400/f72585/ffffff?text=New+Arrivals', 'Explore', '../productlist/productlist.html', 2),
('Educational Toys Collection', 'Learning made fun with our educational toys', 'https://via.placeholder.com/800x400/4cc9f0/ffffff?text=Educational+Toys', 'Learn More', '../productlist/productlist.html?category=educational-toys', 3);

-- Insert sample products
INSERT INTO products (name, slug, description, price, original_price, category_id, image_url, stock_quantity, is_featured, rating, review_count) VALUES 
('Super Robot Action Figure', 'super-robot-action-figure', 'Advanced robot action figure with light and sound effects', 29.99, 39.99, 1, 'https://via.placeholder.com/300x300/4361ee/ffffff?text=Robot+Figure', 25, TRUE, 4.5, 34),
('Building Blocks Set - 500 Pieces', 'building-blocks-set-500', 'Creative building blocks set with 500 colorful pieces', 49.99, 59.99, 2, 'https://via.placeholder.com/300x300/f72585/ffffff?text=Building+Blocks', 15, TRUE, 4.8, 28),
('Educational Science Kit', 'educational-science-kit', 'Complete science experiment kit for young explorers', 34.99, NULL, 3, 'https://via.placeholder.com/300x300/4cc9f0/ffffff?text=Science+Kit', 30, TRUE, 4.7, 42),
('Outdoor Play Tent', 'outdoor-play-tent', 'Colorful play tent for backyard adventures', 79.99, 99.99, 4, 'https://via.placeholder.com/300x300/f8961e/ffffff?text=Play+Tent', 8, FALSE, 4.3, 19),
('Family Board Game Collection', 'family-board-game-collection', 'Set of 5 popular family board games', 59.99, 79.99, 5, 'https://via.placeholder.com/300x300/4895ef/ffffff?text=Board+Games', 12, FALSE, 4.6, 31),
('Fashion Doll with Accessories', 'fashion-doll-with-accessories', 'Beautiful fashion doll with clothing and accessory set', 24.99, 29.99, 6, 'https://via.placeholder.com/300x300/7209b7/ffffff?text=Fashion+Doll', 20, TRUE, 4.4, 27),
('RC Racing Car', 'rc-racing-car', 'High-speed remote control racing car', 89.99, 119.99, 7, 'https://via.placeholder.com/300x300/f77f00/ffffff?text=RC+Car', 5, TRUE, 4.9, 38),
('Arts and Crafts Kit', 'arts-and-crafts-kit', 'Complete arts and crafts set with various materials', 19.99, 24.99, 8, 'https://via.placeholder.com/300x300/2ec4b6/ffffff?text=Arts+Crafts', 35, FALSE, 4.2, 23),
('Puzzle Set - 100 Pieces', 'puzzle-set-100-pieces', 'Educational puzzle set with 100 colorful pieces', 14.99, 19.99, 5, 'https://via.placeholder.com/300x300/e71d36/ffffff?text=Puzzle+Set', 18, FALSE, 4.5, 16),
('Dinosaur Action Figures Set', 'dinosaur-action-figures-set', 'Set of 5 realistic dinosaur action figures', 39.99, 49.99, 1, 'https://via.placeholder.com/300x300/3a0ca3/ffffff?text=Dinosaur+Set', 22, TRUE, 4.7, 29);

-- Insert sample promotions
INSERT INTO promotions (name, code, type, discount_type, discount_value, usage_limit, start_date, end_date, status, applicable_categories) VALUES 
('Summer Sale', 'SUMMER25', 'discount-code', 'percentage', 25.00, 500, '2023-06-01 00:00:00', '2023-08-31 23:59:59', 'active', 'electronics,clothing,home'),
('Welcome Discount', 'WELCOME10', 'discount-code', 'percentage', 10.00, NULL, '2023-01-01 00:00:00', '2023-12-31 23:59:59', 'active', 'all'),
('Free Shipping', 'FREESHIP', 'discount-code', 'shipping', 0.00, 100, '2023-07-01 00:00:00', '2023-07-31 23:59:59', 'active', 'all'),
('Back to School', 'BACK2SCHOOL', 'discount-code', 'fixed', 15.00, 200, '2023-08-15 00:00:00', '2023-09-15 23:59:59', 'upcoming', 'electronics,books'),
('Spring Sale', 'SPRING30', 'discount-code', 'percentage', 30.00, 300, '2023-03-01 00:00:00', '2023-05-31 23:59:59', 'expired', 'all'),
('Summer Flash Sale', 'SUMMERFLASH', 'flash-sale', 'percentage', 30.00, NULL, '2023-06-01 00:00:00', '2023-08-31 23:59:59', 'active', 'electronics,clothing'),
('Back to School Bundle', 'SCHOOLBUNDLE', 'special-offer', 'percentage', 20.00, NULL, '2023-08-15 00:00:00', '2023-09-15 23:59:59', 'upcoming', 'electronics,books'),
('Buy One Get One', 'BOGO50', 'special-offer', 'percentage', 50.00, NULL, '2023-07-01 00:00:00', '2023-07-31 23:59:59', 'active', 'clothing');

-- Insert sample promotion usage
INSERT INTO promotion_usage (promotion_id, order_id, customer_id, discount_amount) VALUES 
(1, 1001, 1, 62.50),
(1, 1002, 2, 45.00),
(1, 1003, 3, 28.75),
(2, 1004, 4, 12.00),
(2, 1005, 5, 8.50),
(3, 1006, 1, 15.00),
(3, 1007, 2, 12.50),
(5, 1008, 3, 45.00),
(5, 1009, 4, 32.00),
(5, 1010, 5, 28.50);

-- Insert sample categories
INSERT IGNORE INTO categories (name, slug) VALUES 
('Electronics', 'electronics'),
('Clothing', 'clothing'),
('Home & Kitchen', 'home-kitchen'),
('Books', 'books'),
('Beauty', 'beauty');

-- Insert sample suppliers
INSERT IGNORE INTO suppliers (name, email, phone) VALUES 
('Supplier A', 'contact@suppliera.com', '(555) 111-2222'),
('Supplier B', 'sales@supplierb.com', '(555) 333-4444'),
('Supplier C', 'info@supplierc.com', '(555) 555-6666');

-- Insert sample products
INSERT INTO products (name, sku, description, category_id, supplier_id, price, cost_price, current_stock, min_stock_level, max_stock_level, image_url) VALUES 
('Wireless Headphones', 'WH-1000XM4', 'High-quality wireless headphones with noise cancellation technology, perfect for music lovers and professionals who need focus.', 1, 1, 349.99, 250.00, 42, 5, 50, 'https://via.placeholder.com/50'),
('Smart Watch', 'SW-GT3', 'Advanced smartwatch with health monitoring features and long battery life.', 1, 1, 199.99, 150.00, 18, 5, 40, 'https://via.placeholder.com/50'),
('Cotton T-Shirt', 'CT-SM01', 'Comfortable cotton t-shirt available in various sizes and colors.', 2, 2, 24.99, 8.00, 0, 5, 20, 'https://via.placeholder.com/50'),
('Stainless Steel Water Bottle', 'SSWB-32', 'Durable stainless steel water bottle, 32oz capacity with insulation.', 3, 3, 29.99, 12.00, 56, 10, 80, 'https://via.placeholder.com/50'),
('Programming Book', 'PB-JS01', 'Complete guide to JavaScript programming for beginners and advanced developers.', 4, 2, 39.99, 15.00, 23, 3, 20, 'https://via.placeholder.com/50'),
('Wireless Earbuds', 'WE-BD05', 'Compact wireless earbuds with charging case and premium sound quality.', 1, 1, 129.99, 60.00, 15, 5, 30, 'https://via.placeholder.com/50'),
('Yoga Pants', 'YP-WM02', 'Comfortable yoga pants for women with stretchable fabric.', 2, 2, 49.99, 18.00, 32, 5, 40, 'https://via.placeholder.com/50'),
('Coffee Maker', 'CM-DB01', 'Programmable drip coffee maker with thermal carafe.', 3, 3, 89.99, 35.00, 8, 3, 25, 'https://via.placeholder.com/50');

-- Insert sample orders
INSERT INTO orders (order_number, customer_id, total_amount, subtotal_amount, shipping_amount, tax_amount, status, payment_status, payment_method, transaction_id, created_at) VALUES 
('ORD-7842', 1, 349.99, 329.99, 20.00, 0.00, 'processing', 'completed', 'Credit Card', 'TXN-489216', '2023-06-12 10:30:00'),
('ORD-7841', 2, 199.99, 179.99, 20.00, 0.00, 'pending', 'pending', 'PayPal', NULL, '2023-06-11 14:15:00'),
('ORD-7840', 3, 524.97, 504.97, 20.00, 0.00, 'shipped', 'completed', 'Credit Card', 'TXN-489217', '2023-06-10 09:45:00'),
('ORD-7839', 4, 124.50, 104.50, 20.00, 0.00, 'delivered', 'completed', 'Credit Card', 'TXN-489218', '2023-06-09 16:20:00'),
('ORD-7838', 5, 89.99, 69.99, 20.00, 0.00, 'cancelled', 'refunded', 'Credit Card', 'TXN-489219', '2023-06-08 11:30:00'),
('ORD-7837', 1, 215.75, 195.75, 20.00, 0.00, 'delivered', 'completed', 'Credit Card', 'TXN-489220', '2023-06-07 13:45:00'),
('ORD-7836', 2, 78.90, 58.90, 20.00, 0.00, 'delivered', 'completed', 'PayPal', 'TXN-489221', '2023-06-06 15:20:00');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, product_name, product_sku, product_price, quantity, total_price) VALUES 
(1, 1, 'Wireless Headphones', 'WH-1000XM4', 249.99, 1, 249.99),
(1, 7, 'Phone Case', 'PC-SM01', 29.99, 2, 59.98),
(1, 8, 'Screen Protector', 'SP-GL01', 19.99, 1, 19.99),
(2, 2, 'Smart Watch', 'SW-GT3', 199.99, 1, 199.99),
(3, 1, 'Wireless Headphones', 'WH-1000XM4', 249.99, 2, 499.98),
(4, 3, 'Cotton T-Shirt', 'CT-SM01', 24.99, 3, 74.97),
(4, 6, 'Wireless Earbuds', 'WE-BD05', 129.99, 1, 129.99),
(5, 4, 'Stainless Steel Water Bottle', 'SSWB-32', 29.99, 1, 29.99),
(5, 5, 'Programming Book', 'PB-JS01', 39.99, 1, 39.99);

-- Insert sample order status history
INSERT INTO order_status_history (order_id, status, notes, created_at) VALUES 
(1, 'pending', 'Order placed by customer', '2023-06-12 10:30:00'),
(1, 'processing', 'Payment confirmed and order being processed', '2023-06-12 10:35:00'),
(1, 'processing', 'Items picked and packed', '2023-06-12 11:45:00'),
(2, 'pending', 'Order placed by customer', '2023-06-11 14:15:00'),
(3, 'pending', 'Order placed by customer', '2023-06-10 09:45:00'),
(3, 'processing', 'Payment confirmed', '2023-06-10 10:00:00'),
(3, 'shipped', 'Shipped via UPS', '2023-06-10 14:30:00'),
(4, 'pending', 'Order placed by customer', '2023-06-09 16:20:00'),
(4, 'processing', 'Payment confirmed', '2023-06-09 16:35:00'),
(4, 'shipped', 'Shipped via FedEx', '2023-06-09 18:15:00'),
(4, 'delivered', 'Delivered to customer', '2023-06-11 12:00:00'),
(5, 'pending', 'Order placed by customer', '2023-06-08 11:30:00'),
(5, 'processing', 'Payment confirmed', '2023-06-08 11:45:00'),
(5, 'cancelled', 'Cancelled by customer request', '2023-06-08 12:30:00');


-- Insert sample customers
INSERT INTO customers (first_name, last_name, email, phone, status, created_at, last_login, avatar_color) VALUES 
('John', 'Doe', 'john.doe@example.com', '(555) 123-4567', 'active', '2022-01-15 10:30:00', '2023-06-15 14:20:00', '#4361ee'),
('Jane', 'Smith', 'jane.smith@example.com', '(555) 987-6543', 'active', '2022-02-20 11:15:00', '2023-06-14 09:45:00', '#f72585'),
('Robert', 'Johnson', 'robert.j@example.com', '(555) 456-7890', 'active', '2022-03-10 08:45:00', '2023-06-12 16:30:00', '#4cc9f0'),
('Sarah', 'Williams', 'sarahw@example.com', '(555) 234-5678', 'inactive', '2022-04-05 14:20:00', '2023-05-20 11:10:00', '#f8961e'),
('Michael', 'Brown', 'm.brown@example.com', '(555) 876-5432', 'active', '2022-01-25 09:30:00', '2023-06-16 10:15:00', '#4895ef');

-- Insert sample addresses
INSERT INTO addresses (customer_id, type, address_line1, address_line2, city, state, zip_code, country, is_default) VALUES 
(1, 'shipping', '123 Main Street', 'Apt 4B', 'New York', 'NY', '10001', 'United States', TRUE),
(1, 'billing', '123 Main Street', 'Apt 4B', 'New York', 'NY', '10001', 'United States', TRUE),
(2, 'shipping', '456 Oak Avenue', NULL, 'Los Angeles', 'CA', '90210', 'United States', TRUE),
(3, 'shipping', '789 Pine Road', 'Suite 200', 'Chicago', 'IL', '60601', 'United States', TRUE),
(4, 'shipping', '321 Elm Street', NULL, 'Houston', 'TX', '77001', 'United States', TRUE),
(5, 'shipping', '654 Maple Drive', 'Floor 3', 'Phoenix', 'AZ', '85001', 'United States', TRUE);

-- Insert sample orders
INSERT INTO orders (customer_id, order_number, total_amount, status, order_date, shipping_address_id) VALUES 
(1, 'ORD-7842', 349.99, 'processing', '2023-06-12 14:30:00', 1),
(1, 'ORD-7815', 124.50, 'delivered', '2023-05-28 10:15:00', 1),
(1, 'ORD-7793', 89.99, 'delivered', '2023-05-15 16:45:00', 1),
(1, 'ORD-7764', 215.75, 'delivered', '2023-05-02 09:20:00', 1),
(1, 'ORD-7721', 167.80, 'delivered', '2023-04-18 11:30:00', 1),
(2, 'ORD-7835', 245.60, 'delivered', '2023-06-10 13:25:00', 3),
(2, 'ORD-7801', 78.90, 'delivered', '2023-05-25 15:40:00', 3),
(3, 'ORD-7823', 156.75, 'shipped', '2023-06-08 08:50:00', 4),
(4, 'ORD-7789', 299.99, 'delivered', '2023-05-20 12:15:00', 5),
(5, 'ORD-7850', 425.30, 'processing', '2023-06-14 17:20:00', 6);

-- Insert sample pages
INSERT INTO pages (title, slug, content, meta_description, template, status, published_at) VALUES 
('About Us', 'about-us', '<h2>Our Story</h2><p>Founded in 2010, our company began as a small startup with a big vision: to provide high-quality products with exceptional customer service. Over the years, we''ve grown into a trusted brand serving customers worldwide.</p><h2>Our Mission</h2><p>We''re committed to delivering innovative products that enhance our customers'' lives while maintaining sustainable business practices and supporting our local community.</p><h2>Our Values</h2><ul><li><strong>Quality:</strong> We never compromise on the quality of our products.</li><li><strong>Integrity:</strong> We conduct business with honesty and transparency.</li><li><strong>Innovation:</strong> We continuously seek to improve and innovate.</li><li><strong>Community:</strong> We believe in giving back to the communities we serve.</li></ul>', 'Learn more about our company story, mission, and values.', 'default', 'published', NOW()),
('Contact Us', 'contact', '<h2>Get in Touch</h2><p>We''d love to hear from you. Please fill out the form below and we''ll get back to you as soon as possible.</p>', 'Contact us for any inquiries or support.', 'contact', 'published', NOW()),
('Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy explains how we handle your personal information.</p>', 'Read our privacy policy to understand how we protect your data.', 'default', 'draft', NULL);

-- Insert sample banners
INSERT INTO banners (title, description, image_url, target_url, position, status, start_date, end_date, button_text, button_color) VALUES 
('Summer Sale', 'Promotes summer collection with up to 40% discount', 'https://via.placeholder.com/600x300/4361ee/ffffff?text=Summer+Sale', '/summer-sale', 'homepage-top', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'Shop Now', '#4361ee'),
('New Collection', 'Highlights the newly launched product collection', 'https://via.placeholder.com/600x300/f72585/ffffff?text=New+Collection', '/new-arrivals', 'homepage-middle', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'Explore', '#f72585'),
('Free Shipping', 'Announces free shipping promotion for orders over $50', 'https://via.placeholder.com/600x300/f8961e/ffffff?text=Free+Shipping', '/shipping-info', 'all-pages-top', 'scheduled', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 37 DAY), 'Learn More', '#f8961e');

-- Insert sample categories
INSERT INTO categories (name, slug, description, icon, color, parent_id, product_count, created_at) VALUES 
('Electronics', 'electronics', 'Computers, phones, gadgets and electronics', 'laptop', '#4361ee', NULL, 142, '2022-01-12 00:00:00'),
('Clothing', 'clothing', 'Shirts, pants, accessories and fashion items', 'tshirt', '#f72585', NULL, 87, '2022-02-03 00:00:00'),
('Home & Kitchen', 'home-kitchen', 'Furniture, appliances, home decor and kitchen items', 'home', '#4cc9f0', NULL, 65, '2022-03-15 00:00:00'),
('Books', 'books', 'Fiction, non-fiction, educational books and magazines', 'book', '#f8961e', NULL, 43, '2022-04-22 00:00:00'),
('Beauty', 'beauty', 'Skincare, makeup, haircare and personal care products', 'spa', '#4895ef', NULL, 29, '2022-05-10 00:00:00'),
('Smartphones', 'smartphones', 'Mobile phones and smartphones', 'mobile-alt', '#3a0ca3', 1, 56, '2022-06-01 00:00:00'),
('Laptops', 'laptops', 'Laptop computers and accessories', 'laptop', '#7209b7', 1, 42, '2022-06-02 00:00:00');

-- Insert sample admin (password: admin123)
INSERT INTO admins (username, password, email) VALUES 
('admin', MD5('admin123'), 'admin@example.com');

-- Insert sample data
INSERT INTO customers (name, email, phone) VALUES 
('John Doe', 'john@example.com', '123-456-7890'),
('Jane Smith', 'jane@example.com', '123-456-7891'),
('Bob Johnson', 'bob@example.com', '123-456-7892');

INSERT INTO orders (customer_id, total_amount, status) VALUES 
(1, 150.00, 'completed'),
(2, 75.50, 'completed'),
(3, 200.00, 'pending');

INSERT INTO sales (order_id, amount) VALUES 
(1, 150.00),
(2, 75.50);

INSERT INTO activities (type, title, description, icon) VALUES 
('sales', 'New Sale', 'Order #1234 completed', 'fa-dollar-sign'),
('users', 'New Customer', 'John Doe registered', 'fa-user-plus'),
('orders', 'New Order', 'Order #1235 placed', 'fa-shopping-cart'),
('revenue', 'Revenue Update', 'Monthly revenue increased', 'fa-chart-line');





---->
make perfect database,tables and data set 
using previously which i gave to you