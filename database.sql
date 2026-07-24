-- Gaming Store Database-- Database: gaming_store

CREATE DATABASE IF NOT EXISTS gaming_store;
USE gaming_store;

-- Table: users
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: admins
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password, email, full_name) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gamingstore.com', 'Admin User');

-- Table: categories
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) UNIQUE NOT NULL,
    category_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (category_name, category_description) VALUES 
('Mouse', 'Gaming mice with high precision sensors'),
('Keyboard', 'Mechanical and RGB gaming keyboards'),
('Headsets', 'Gaming headsets with surround sound'),
('Controllers', 'Game controllers and gamepads'),
('Chairs', 'Ergonomic gaming chairs'),
('RGB Accessories', 'RGB lighting and accessories'),
('Mouse Pads', 'Gaming mouse pads and mats'),
('Webcams', 'HD webcams for streaming');

-- Table: products
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    category_id INT,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    product_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Table: wishlist
CREATE TABLE wishlist (
    wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Table: cart
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id)
);

-- Table: orders
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table: order_items
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Sample Products
INSERT INTO products (product_name, category_id, description, price, quantity) VALUES
('Gaming Mouse Pro', 1, 'High precision gaming mouse with RGB lighting and 16000 DPI sensor', 49.99, 25),
('Mechanical Keyboard RGB', 2, 'RGB mechanical keyboard with blue switches and anti-ghosting', 89.99, 15),
('Wireless Gaming Headset', 3, '7.1 surround sound wireless headset with noise-canceling mic', 79.99, 20),
('Pro Gaming Controller', 4, 'Wireless gaming controller for PC with vibration feedback', 59.99, 18),
('Ergonomic Gaming Chair', 5, 'Adjustable gaming chair with lumbar support and headrest', 199.99, 8),
('RGB Strip Kit', 6, 'Addressable RGB LED strip kit with remote control', 29.99, 30),
('Extended Mouse Pad', 7, 'Large RGB gaming mouse pad with smooth surface', 24.99, 22),
('HD Streaming Webcam', 8, '1080p HD webcam with built-in microphone', 69.99, 12),
('Gaming Mouse Xtreme', 1, 'Ultra-light gaming mouse with 20000 DPI optical sensor', 79.99, 10),
('Compact Mechanical Keyboard', 2, '60% mechanical keyboard with RGB backlight', 65.99, 14);       
-- Table: banners
CREATE TABLE banners (
    banner_id INT PRIMARY KEY AUTO_INCREMENT,
    banner_title VARCHAR(255) NOT NULL,
    banner_subtitle VARCHAR(255),
    banner_image VARCHAR(255) NOT NULL,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample banners
INSERT INTO banners (banner_title, banner_subtitle, banner_image, button_text, button_link, is_active, display_order) VALUES
('Level Up Your Gaming', 'Premium gaming accessories for the ultimate gaming experience', 'banner1.jpg', 'Shop Now', 'products.php', 1, 1),
('Summer Sale', 'Get up to 50% off on selected gaming accessories', 'banner2.jpg', 'View Offers', 'products.php', 1, 2);

-- Table: settings (for general site settings)
CREATE TABLE settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Gaming Store'),
('site_logo', 'logo.png'),
('contact_email', 'info@gamingstore.com'),
('contact_phone', '+92 300 1234567'),
('contact_address', 'Gaming City, Pakistan');