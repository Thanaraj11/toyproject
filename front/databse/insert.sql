-- =============================================================================
-- SAMPLE DATA INSERTION
-- =============================================================================

-- Insert admin user (password: admin123)
INSERT INTO admins (username, password, email, first_name, last_name, role) VALUES 
('admin', MD5('admin123'), 'admin@toybox.com', 'John', 'Admin', 'super_admin');

-- Insert categories
INSERT INTO categories (name, slug, description, icon, color, product_count) VALUES 
('Action Figures', 'action-figures', 'Collectible action figures from popular movies and TV shows', 'user', '#4361ee', 25),
('Building Blocks', 'building-blocks', 'Creative building sets and construction toys', 'cube', '#f72585', 18),
('Educational Toys', 'educational-toys', 'Learning toys that develop skills and knowledge', 'graduation-cap', '#4cc9f0', 32),
('Outdoor Toys', 'outdoor-toys', 'Toys for outdoor play and activities', 'sun', '#f8961e', 15),
('Board Games', 'board-games', 'Family board games and puzzles', 'chess-board', '#4895ef', 22),
('Dolls & Accessories', 'dolls-accessories', 'Dolls, dollhouses, and accessories', 'female', '#7209b7', 28),
('Remote Control', 'remote-control', 'RC cars, drones, and remote-controlled toys', 'gamepad', '#f77f00', 12),
('Arts & Crafts', 'arts-crafts', 'Creative arts and crafts supplies', 'palette', '#2ec4b6', 20);

-- Insert suppliers
INSERT INTO suppliers (name, email, phone, address, contact_person) VALUES 
('ToyMaster Inc.', 'contact@toymaster.com', '(555) 123-4567', '123 Toy Street, New York, NY 10001', 'Sarah Johnson'),
('BuildFun Toys', 'sales@buildfun.com', '(555) 234-5678', '456 Construction Ave, Chicago, IL 60601', 'Mike Chen'),
('EduPlay Solutions', 'info@eduplay.com', '(555) 345-6789', '789 Learning Lane, Boston, MA 02101', 'Dr. Emily Wilson'),
('Outdoor Adventures Co.', 'orders@outdooradventures.com', '(555) 456-7890', '321 Nature Road, Denver, CO 80201', 'Robert Garcia');

-- Insert products
INSERT INTO products (name, slug, sku, description, short_description, category_id, supplier_id, price, cost_price, original_price, current_stock, min_stock_level, max_stock_level, image_url, is_featured, rating, review_count) VALUES 
('Super Robot Action Figure', 'super-robot-action-figure', 'SR-1001', 'Advanced robot action figure with light and sound effects. Features 15 points of articulation and comes with multiple accessories.', 'Light-up robot with sound effects', 1, 1, 29.99, 15.00, 39.99, 25, 5, 50, 'https://via.placeholder.com/400x400/4361ee/ffffff?text=Robot+Figure', TRUE, 4.5, 34),
('Building Blocks Set - 500 Pieces', 'building-blocks-set-500', 'BB-5002', 'Creative building blocks set with 500 colorful pieces. Compatible with major brands and includes storage container.', '500 colorful building pieces', 2, 2, 49.99, 25.00, 59.99, 15, 5, 40, 'https://via.placeholder.com/400x400/f72585/ffffff?text=Building+Blocks', TRUE, 4.8, 28),
('Educational Science Kit', 'educational-science-kit', 'ES-3003', 'Complete science experiment kit for young explorers. Includes 25+ experiments about chemistry, physics, and biology.', '25+ science experiments', 3, 3, 34.99, 18.00, NULL, 30, 5, 60, 'https://via.placeholder.com/400x400/4cc9f0/ffffff?text=Science+Kit', TRUE, 4.7, 42),
('Outdoor Play Tent', 'outdoor-play-tent', 'OT-4004', 'Colorful play tent for backyard adventures. Easy setup with waterproof material and UV protection.', 'Waterproof outdoor play tent', 4, 4, 79.99, 45.00, 99.99, 8, 3, 25, 'https://via.placeholder.com/400x400/f8961e/ffffff?text=Play+Tent', FALSE, 4.3, 19),
('Family Board Game Collection', 'family-board-game-collection', 'BG-5005', 'Set of 5 popular family board games. Includes instructions and all necessary components for hours of fun.', '5 family board games in one', 5, 1, 59.99, 30.00, 79.99, 12, 5, 30, 'https://via.placeholder.com/400x400/4895ef/ffffff?text=Board+Games', FALSE, 4.6, 31),
('Fashion Doll with Accessories', 'fashion-doll-with-accessories', 'FD-6006', 'Beautiful fashion doll with clothing and accessory set. Includes 3 outfits, shoes, and styling tools.', 'Doll with 3 outfits and accessories', 6, 1, 24.99, 12.00, 29.99, 20, 5, 40, 'https://via.placeholder.com/400x400/7209b7/ffffff?text=Fashion+Doll', TRUE, 4.4, 27),
('RC Racing Car', 'rc-racing-car', 'RC-7007', 'High-speed remote control racing car with 2.4GHz controller. Rechargeable battery and LED headlights included.', 'High-speed remote control car', 7, 4, 89.99, 50.00, 119.99, 5, 3, 20, 'https://via.placeholder.com/400x400/f77f00/ffffff?text=RC+Car', TRUE, 4.9, 38),
('Arts and Crafts Kit', 'arts-and-crafts-kit', 'AC-8008', 'Complete arts and crafts set with various materials. Includes paints, brushes, paper, and instruction book.', 'Complete creative arts set', 8, 3, 19.99, 10.00, 24.99, 35, 10, 80, 'https://via.placeholder.com/400x400/2ec4b6/ffffff?text=Arts+Crafts', FALSE, 4.2, 23);

-- Insert product images
INSERT INTO product_images (product_id, image_url, alt_text, is_primary) VALUES 
(1, 'https://via.placeholder.com/400x400/4361ee/ffffff?text=Robot+Front', 'Super Robot Action Figure - Front View', TRUE),
(1, 'https://via.placeholder.com/400x400/4361ee/ffffff?text=Robot+Back', 'Super Robot Action Figure - Back View', FALSE),
(2, 'https://via.placeholder.com/400x400/f72585/ffffff?text=Blocks+Set', 'Building Blocks Set - 500 Pieces', TRUE),
(3, 'https://via.placeholder.com/400x400/4cc9f0/ffffff?text=Science+Kit', 'Educational Science Kit', TRUE),
(4, 'https://via.placeholder.com/400x400/f8961e/ffffff?text=Tent+Open', 'Outdoor Play Tent - Set Up', TRUE),
(5, 'https://via.placeholder.com/400x400/4895ef/ffffff?text=Games+Box', 'Family Board Game Collection', TRUE),
(6, 'https://via.placeholder.com/400x400/7209b7/ffffff?text=Doll+Set', 'Fashion Doll with Accessories', TRUE),
(7, 'https://via.placeholder.com/400x400/f77f00/ffffff?text=RC+Car', 'RC Racing Car', TRUE),
(8, 'https://via.placeholder.com/400x400/2ec4b6/ffffff?text=Arts+Kit', 'Arts and Crafts Kit', TRUE);

-- Insert customers
INSERT INTO customers (first_name, last_name, email, phone, status, avatar_color) VALUES 
('John', 'Doe', 'john.doe@example.com', '(555) 111-2222', 'active', '#4361ee'),
('Jane', 'Smith', 'jane.smith@example.com', '(555) 333-4444', 'active', '#f72585'),
('Michael', 'Johnson', 'michael.j@example.com', '(555) 555-6666', 'active', '#4cc9f0'),
('Sarah', 'Williams', 'sarah.w@example.com', '(555) 777-8888', 'active', '#f8961e'),
('David', 'Brown', 'david.brown@example.com', '(555) 999-0000', 'active', '#4895ef');

-- Insert addresses
INSERT INTO addresses (customer_id, type, full_name, phone, address_line1, city, state, zip_code, country, is_default) VALUES 
(1, 'shipping', 'John Doe', '(555) 111-2222', '123 Main Street', 'New York', 'NY', '10001', 'United States', TRUE),
(1, 'billing', 'John Doe', '(555) 111-2222', '123 Main Street', 'New York', 'NY', '10001', 'United States', TRUE),
(2, 'shipping', 'Jane Smith', '(555) 333-4444', '456 Oak Avenue', 'Los Angeles', 'CA', '90210', 'United States', TRUE),
(3, 'shipping', 'Michael Johnson', '(555) 555-6666', '789 Pine Road', 'Chicago', 'IL', '60601', 'United States', TRUE),
(4, 'shipping', 'Sarah Williams', '(555) 777-8888', '321 Elm Street', 'Houston', 'TX', '77001', 'United States', TRUE),
(5, 'shipping', 'David Brown', '(555) 999-0000', '654 Maple Drive', 'Phoenix', 'AZ', '85001', 'United States', TRUE);

-- Insert orders
INSERT INTO orders (order_number, customer_id, total_amount, subtotal_amount, shipping_amount, tax_amount, status, payment_status, payment_method, transaction_id, shipping_address_id, billing_address_id) VALUES 
('ORD-1001', 1, 149.97, 129.97, 15.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001', 1, 1),
('ORD-1002', 2, 89.99, 79.99, 5.00, 5.00, 'processing', 'paid', 'PayPal', 'TXN-002', 3, 3),
('ORD-1003', 3, 204.98, 184.98, 15.00, 5.00, 'shipped', 'paid', 'Credit Card', 'TXN-003', 4, 4),
('ORD-1004', 1, 34.99, 29.99, 0.00, 5.00, 'pending', 'pending', 'Credit Card', NULL, 1, 1),
('ORD-1005', 4, 119.98, 109.98, 5.00, 5.00, 'confirmed', 'paid', 'Credit Card', 'TXN-005', 5, 5);

-- Insert order items
INSERT INTO order_items (order_id, product_id, product_name, product_sku, product_price, quantity, total_price) VALUES 
(1, 1, 'Super Robot Action Figure', 'SR-1001', 29.99, 2, 59.98),
(1, 2, 'Building Blocks Set - 500 Pieces', 'BB-5002', 49.99, 1, 49.99),
(1, 8, 'Arts and Crafts Kit', 'AC-8008', 19.99, 1, 19.99),
(2, 3, 'Educational Science Kit', 'ES-3003', 34.99, 1, 34.99),
(2, 6, 'Fashion Doll with Accessories', 'FD-6006', 24.99, 1, 24.99),
(2, 8, 'Arts and Crafts Kit', 'AC-8008', 19.99, 1, 19.99),
(3, 2, 'Building Blocks Set - 500 Pieces', 'BB-5002', 49.99, 2, 99.98),
(3, 4, 'Outdoor Play Tent', 'OT-4004', 79.99, 1, 79.99),
(3, 8, 'Arts and Crafts Kit', 'AC-8008', 19.99, 1, 19.99),
(4, 3, 'Educational Science Kit', 'ES-3003', 34.99, 1, 34.99),
(5, 1, 'Super Robot Action Figure', 'SR-1001', 29.99, 2, 59.98),
(5, 7, 'RC Racing Car', 'RC-7007', 89.99, 1, 89.99);

-- Insert order status history
INSERT INTO order_status_history (order_id, status, notes) VALUES 
(1, 'pending', 'Order placed by customer'),
(1, 'confirmed', 'Payment received and order confirmed'),
(1, 'processing', 'Items picked and packed'),
(1, 'shipped', 'Shipped via UPS - Tracking: 1Z123456789'),
(1, 'delivered', 'Delivered to customer'),
(2, 'pending', 'Order placed by customer'),
(2, 'confirmed', 'Payment received via PayPal'),
(2, 'processing', 'Processing order'),
(3, 'pending', 'Order placed by customer'),
(3, 'confirmed', 'Payment received'),
(3, 'processing', 'Items being prepared for shipment'),
(3, 'shipped', 'Shipped via FedEx - Tracking: 789012345678'),
(4, 'pending', 'Order placed - awaiting payment'),
(5, 'pending', 'Order placed by customer'),
(5, 'confirmed', 'Payment received and order confirmed');

-- Insert stock movements
INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, reason, reference) VALUES 
(1, 'in', 50, 0, 50, 'Initial stock', 'PO-001'),
(1, 'out', 2, 50, 48, 'Order ORD-1001', 'ORD-1001'),
(1, 'out', 2, 48, 46, 'Order ORD-1005', 'ORD-1005'),
(2, 'in', 40, 0, 40, 'Initial stock', 'PO-002'),
(2, 'out', 1, 40, 39, 'Order ORD-1001', 'ORD-1001'),
(2, 'out', 2, 39, 37, 'Order ORD-1003', 'ORD-1003'),
(3, 'in', 60, 0, 60, 'Initial stock', 'PO-003'),
(3, 'out', 1, 60, 59, 'Order ORD-1002', 'ORD-1002'),
(3, 'out', 1, 59, 58, 'Order ORD-1004', 'ORD-1004'),
(4, 'in', 25, 0, 25, 'Initial stock', 'PO-004'),
(4, 'out', 1, 25, 24, 'Order ORD-1003', 'ORD-1003'),
(5, 'in', 30, 0, 30, 'Initial stock', 'PO-005'),
(6, 'in', 40, 0, 40, 'Initial stock', 'PO-006'),
(6, 'out', 1, 40, 39, 'Order ORD-1002', 'ORD-1002'),
(7, 'in', 20, 0, 20, 'Initial stock', 'PO-007'),
(7, 'out', 1, 20, 19, 'Order ORD-1005', 'ORD-1005'),
(8, 'in', 80, 0, 80, 'Initial stock', 'PO-008'),
(8, 'out', 1, 80, 79, 'Order ORD-1001', 'ORD-1001'),
(8, 'out', 1, 79, 78, 'Order ORD-1002', 'ORD-1002'),
(8, 'out', 1, 78, 77, 'Order ORD-1003', 'ORD-1003');

-- Insert low stock alerts
INSERT INTO low_stock_alerts (product_id, current_stock, min_stock_level, alert_level) VALUES 
(4, 8, 10, 'low'),
(7, 5, 5, 'critical');

-- Insert promotions
INSERT INTO promotions (name, code, type, discount_type, discount_value, min_order_amount, usage_limit, start_date, end_date, status) VALUES 
('Summer Sale', 'SUMMER25', 'discount-code', 'percentage', 25.00, 50.00, 100, '2024-06-01 00:00:00', '2024-08-31 23:59:59', 'active'),
('Welcome Discount', 'WELCOME10', 'discount-code', 'percentage', 10.00, 0.00, NULL, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 'active'),
('Free Shipping', 'FREESHIP', 'discount-code', 'shipping', 0.00, 75.00, 50, '2024-07-01 00:00:00', '2024-07-31 23:59:59', 'active'),
('Back to School', 'SCHOOL20', 'special-offer', 'percentage', 20.00, 30.00, 200, '2024-08-15 00:00:00', '2024-09-15 23:59:59', 'upcoming');

-- Insert promotion usage
INSERT INTO promotion_usage (promotion_id, order_id, customer_id, discount_amount) VALUES 
(1, 1, 1, 32.49),
(2, 2, 2, 8.00),
(1, 3, 3, 46.25);

-- Insert pages
INSERT INTO pages (title, slug, content, meta_description, status, published_at) VALUES 
('About Us', 'about-us', '<h2>Welcome to ToyBox</h2><p>We are passionate about providing high-quality toys that inspire creativity and learning.</p>', 'Learn about ToyBox and our mission', 'published', NOW()),
('Contact Us', 'contact', '<h2>Get in Touch</h2><p>We would love to hear from you!</p>', 'Contact ToyBox customer service', 'published', NOW()),
('Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>Your privacy is important to us.</p>', 'Read our privacy policy', 'draft', NULL);

-- Insert banners
INSERT INTO banners (title, description, image_url, target_url, position, status, button_text, button_color, sort_order) VALUES 
('Summer Sale!', 'Get up to 50% off on selected toys', 'https://via.placeholder.com/800x300/4361ee/ffffff?text=Summer+Sale', '/products?category=all', 'homepage-top', 'active', 'Shop Now', '#4361ee', 1),
('New Arrivals', 'Discover the latest toys in our collection', 'https://via.placeholder.com/800x300/f72585/ffffff?text=New+Arrivals', '/products?new=true', 'homepage-middle', 'active', 'Explore', '#f72585', 2),
('Free Shipping', 'Free shipping on orders over $75', 'https://via.placeholder.com/800x300/4cc9f0/ffffff?text=Free+Shipping', '/shipping-info', 'all-pages-top', 'active', 'Learn More', '#4cc9f0', 3);

-- Insert activities
INSERT INTO activities (type, title, description, icon, related_id, related_type) VALUES 
('order', 'New Order Received', 'Order #ORD-1005 has been placed', 'shopping-cart', 5, 'order'),
('user', 'New Customer Registration', 'David Brown registered an account', 'user-plus', 5, 'customer'),
('product', 'Low Stock Alert', 'RC Racing Car is running low on stock', 'exclamation-triangle', 7, 'product'),
('sale', 'Daily Sales Report', '$589.91 in total sales today', 'chart-bar', NULL, 'report');

-- Insert product reviews
INSERT INTO product_reviews (product_id, customer_id, rating, title, comment, status) VALUES 
(1, 1, 5, 'Amazing Robot!', 'My son loves this robot! The lights and sounds are fantastic.', 'approved'),
(1, 2, 4, 'Good quality', 'Well made but a bit smaller than expected.', 'approved'),
(2, 3, 5, 'Great building set', 'Hours of fun and very durable pieces.', 'approved'),
(3, 4, 5, 'Educational and fun', 'Perfect for my 8-year-old who loves science.', 'approved'),
(7, 5, 5, 'Fast and durable', 'This RC car is amazing! Very fast and the battery lasts long.', 'approved');

-- Insert wishlist items
INSERT INTO wishlist (customer_id, product_id) VALUES 
(1, 3),
(1, 7),
(2, 1),
(2, 5),
(3, 4),
(4, 2),
(5, 6);

-- Update product counts in categories
UPDATE categories SET product_count = (
    SELECT COUNT(*) FROM products WHERE category_id = categories.id
);