-- Insert Admins
INSERT INTO admins (username, password, email, first_name, last_name, role, status) VALUES 
('superadmin', MD5('admin123'), 'superadmin@toysrus.com', 'John', 'Anderson', 'super_admin', 'active'),
('admin1', MD5('admin123'), 'admin1@toysrus.com', 'Sarah', 'Miller', 'admin', 'active'),
('admin2', MD5('admin123'), 'admin2@toysrus.com', 'Mike', 'Johnson', 'admin', 'active'),
('moderator1', MD5('mod123'), 'mod1@toysrus.com', 'Emily', 'Chen', 'moderator', 'active'),
('moderator2', MD5('mod123'), 'mod2@toysrus.com', 'David', 'Wilson', 'moderator', 'inactive');

-- Insert Categories
INSERT INTO categories (name, slug, description, icon, color, parent_id, product_count, image_url, sort_order) VALUES 
('Action Figures', 'action-figures', 'Collectible action figures from popular movies, TV shows, and comics', 'user', '#4361ee', NULL, 45, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 1),
('Building Sets', 'building-sets', 'Creative construction toys and building blocks for all ages', 'cube', '#f72585', NULL, 38, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 2),
('Educational Toys', 'educational-toys', 'Learning toys that develop STEM skills and creativity', 'graduation-cap', '#4cc9f0', NULL, 52, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 3),
('Outdoor Play', 'outdoor-play', 'Toys for backyard adventures and outdoor activities', 'sun', '#f8961e', NULL, 28, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 4),
('Board Games', 'board-games', 'Family games, puzzles, and strategy games for all ages', 'chess-board', '#4895ef', NULL, 67, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 5),
('Dolls & Playsets', 'dolls-playsets', 'Dolls, dollhouses, and imaginative play sets', 'female', '#7209b7', NULL, 41, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 6),
('Remote Control', 'remote-control', 'RC vehicles, drones, and remote-controlled toys', 'gamepad', '#f77f00', NULL, 23, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 7),
('Arts & Crafts', 'arts-crafts', 'Creative supplies for painting, drawing, and crafting', 'palette', '#2ec4b6', NULL, 59, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 8),
('Preschool Toys', 'preschool-toys', 'Toys for toddlers and preschool development', 'baby', '#3a0ca3', NULL, 34, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 9),
('Electronic Toys', 'electronic-toys', 'Interactive toys with lights, sounds, and technology', 'tablet', '#e71d36', NULL, 31, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 10),
('Marvel Superheroes', 'marvel-superheroes', 'Action figures from Marvel Comics universe', 'bolt', '#dc2f02', 1, 15, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 1),
('LEGO Sets', 'lego-sets', 'Official LEGO building sets and collections', 'cubes', '#f72585', 2, 22, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 1),
('Science Kits', 'science-kits', 'Educational science experiments and discovery kits', 'flask', '#4cc9f0', 3, 18, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=300', 1);

-- Insert Suppliers
INSERT INTO suppliers (name, email, phone, address, contact_person, status) VALUES 
('ToyMaster Distributors', 'orders@toymaster.com', '(800) 555-1234', '123 Industrial Blvd, Chicago, IL 60601', 'Robert Johnson', 'active'),
('LEGO Group', 'wholesale@lego.com', '(800) 555-2345', '555 Building Lane, Enfield, CT 06082', 'Sarah Chen', 'active'),
('Hasbro Inc', 'distribution@hasbro.com', '(800) 555-3456', '1027 Newport Ave, Pawtucket, RI 02861', 'Michael Brown', 'active'),
('Mattel Worldwide', 'supply@mattel.com', '(800) 555-4567', '333 Continental Blvd, El Segundo, CA 90245', 'Jennifer Wilson', 'active'),
('Melissa & Doug', 'orders@melissaanddoug.com', '(800) 555-5678', '1 Puzzle Lane, Wilton, CT 06897', 'David Miller', 'active'),
('Spin Master Toys', 'wholesale@spinmaster.com', '(800) 555-6789', '450 Front St West, Toronto, ON M5V 1B6', 'Amanda Taylor', 'active'),
('MGA Entertainment', 'distribution@mgae.com', '(800) 555-7890', '1 MGA Plaza, Van Nuys, CA 91406', 'Christopher Lee', 'active'),
('Bandai America', 'orders@bandai.com', '(800) 555-8901', '5551 Katella Ave, Cypress, CA 90630', 'Jessica Martinez', 'active'),
('VTech Electronics', 'supply@vtech.com', '(800) 555-9012', '1156 W 150th St, Gardena, CA 90247', 'Kevin Anderson', 'active'),
('Ravensburger', 'orders@ravensburger.com', '(800) 555-0123', '1 Puzzle Way, Buffalo, NY 14206', 'Lisa Garcia', 'active');

-- Insert Products (50 products)
INSERT INTO products (name, slug, sku, description, short_description, category_id, supplier_id, price, cost_price, original_price, current_stock, min_stock_level, max_stock_level, image_url, is_featured, rating, review_count, weight, dimensions) VALUES 
('LEGO Creator Expert Roller Coaster', 'lego-creator-expert-roller-coaster', 'LEGO-10261', 'Build and recreate the thrilling experience of a classic amusement park roller coaster with this highly detailed LEGO Creator Expert model. Features a full circuit track, 2 trains, ticket booth, cotton candy cart, and 8 minifigures.', 'Buildable roller coaster with motor', 12, 2, 379.99, 220.00, 399.99, 12, 3, 25, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.8, 127, 5.2, '28 x 58 x 14 cm'),
('Marvel Legends Spider-Man Action Figure', 'marvel-legends-spider-man', 'HAS-MLSP01', 'Highly articulated 6-inch scale Spider-Man figure with multiple accessories including web effects and alternate hands. Part of the Marvel Legends series with premium detailing.', '6" Spider-Man with web accessories', 11, 3, 24.99, 12.50, 29.99, 45, 10, 100, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.6, 89, 0.3, '15 x 8 x 4 cm'),
('Barbie Dreamhouse', 'barbie-dreamhouse', 'MAT-BDH2024', 'Three-story dreamhouse with elevator, working slide, pool, and 70+ accessories. Features lights and sounds with multiple rooms including bedroom, kitchen, and bathroom.', '3-story dollhouse with elevator', 6, 4, 199.99, 110.00, 249.99, 8, 5, 30, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.4, 203, 12.5, '48 x 32 x 18 cm'),
('Melissa & Doug Wooden Puzzle Set', 'melissa-doug-wooden-puzzle-set', 'MD-PUZ50', 'Set of 4 wooden puzzles featuring animals, vehicles, numbers, and shapes. Made from high-quality wood with easy-grasp pieces for toddlers.', '4 educational wooden puzzles', 9, 5, 29.99, 15.00, 34.99, 67, 20, 150, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.7, 156, 1.8, '30 x 23 x 6 cm'),
('Nerf Elite 2.0 EaglePoint RD-8 Blaster', 'nerf-elite-eaglepoint', 'HAS-NERFEP8', 'Motorized blaster with 8-dart drum and tactical rail. Includes 8 official Elite darts and requires 4 AA batteries (not included).', 'Motorized dart blaster with 8-dart capacity', 4, 3, 49.99, 25.00, 59.99, 23, 10, 50, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.3, 78, 1.2, '35 x 20 x 8 cm'),
('VTech KidiZoom Smartwatch DX2', 'vtech-kidizoom-smartwatch', 'VT-KZSDX2', 'Kids smartwatch with dual cameras, games, and photo effects. Water-resistant design with included watchband and charger.', 'Kids camera watch with games', 10, 9, 49.99, 22.00, 59.99, 34, 15, 80, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.2, 145, 0.4, '12 x 12 x 4 cm'),
('Crayola Inspiration Art Case', 'crayola-inspiration-art-case', 'CRAY-ART140', 'Complete art set with 140 pieces including crayons, markers, colored pencils, and paper. Portable case with organizational compartments.', '140-piece art supplies kit', 8, 1, 24.99, 12.00, 29.99, 89, 25, 200, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.5, 267, 2.1, '35 x 25 x 8 cm'),
('Hot Wheels City Ultimate Garage', 'hot-wheels-ultimate-garage', 'MAT-HWCG2024', 'Multi-level garage with elevator, stunt ramps, and crash-through features. Includes 2 Hot Wheels cars and works with most Hot Wheels vehicles.', '5-foot tall racing garage', 2, 4, 129.99, 70.00, 159.99, 15, 8, 40, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.6, 98, 4.8, '152 x 45 x 35 cm'),
('Play-Doh Kitchen Creations', 'play-doh-kitchen-creations', 'HAS-PDKC32', 'Ultimate Play-Doh kitchen set with oven, stove, and 32 tools. Includes 10 cans of Play-Doh compound in various colors.', 'Dough kitchen with 32 accessories', 9, 3, 39.99, 18.00, 49.99, 56, 20, 120, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.4, 189, 2.3, '40 x 30 x 12 cm'),
('L.O.L. Surprise! OMG House', 'lol-surprise-omg-house', 'MGA-LOLHOUSE', 'Four-story dollhouse with 45+ surprises including furniture, accessories, and exclusive dolls. Features working elevator and light-up elements.', '4-story surprise dollhouse', 6, 7, 149.99, 80.00, 179.99, 12, 6, 35, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.3, 134, 6.5, '50 x 35 x 20 cm'),
('National Geographic Mega Science Kit', 'natgeo-mega-science-kit', 'NG-SCIENCE45', 'Complete STEM kit with 45 experiments including volcano, crystal growing, and dig kits. Includes detailed learning guide and all necessary materials.', '45 science experiments in one', 13, 1, 49.99, 25.00, 59.99, 28, 12, 60, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.7, 203, 1.5, '30 x 22 x 8 cm'),
('Paw Patrol Ultimate Rescue Fire Truck', 'paw-patrol-fire-truck', 'SPN-PPURFT', 'Large fire truck with extending ladder, water squirters, and lights and sounds. Includes Chase figure and rescue tools.', 'Rescue vehicle with extending ladder', 9, 6, 59.99, 30.00, 69.99, 41, 15, 80, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.5, 167, 1.8, '40 x 18 x 15 cm'),
('Ravensburger Disney Villainous Board Game', 'ravensburger-villainous', 'RAV-DV2024', 'Strategy board game where players take on the role of Disney villains. Each character has unique objectives and abilities. For 2-6 players.', 'Disney villain strategy game', 5, 10, 39.99, 20.00, 44.99, 23, 10, 50, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.8, 289, 1.2, '27 x 27 x 7 cm'),
('DJI Mini 3 Drone', 'dji-mini-3-drone', 'DJI-MINI3', 'Compact drone with 4K camera, 38-minute flight time, and intelligent features. Weighs under 249g with obstacle sensing and QuickTransfer.', '4K camera drone for beginners', 7, 1, 469.99, 280.00, 559.99, 7, 3, 20, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.9, 78, 0.249, '18 x 13 x 5 cm'),
('Baby Alive Sweet Spoonfuls Doll', 'baby-alive-sweet-spoonfuls', 'HAS-BASS2024', 'Interactive doll that eats, drinks, and wets. Comes with food, spoon, bottle, and diaper. Features realistic eating motions and sounds.', 'Interactive feeding baby doll', 6, 3, 34.99, 16.00, 42.99, 38, 15, 75, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.2, 145, 0.9, '35 x 20 x 10 cm'),
('Magnatiles Clear Colors 100-Piece Set', 'magnatiles-clear-colors', 'MAG-CL100', 'Magnetic building tiles in clear colors that are compatible with all Magna-Tiles sets. Develops spatial and creative skills through magnetic construction.', '100 magnetic building tiles', 2, 1, 129.99, 65.00, 149.99, 19, 8, 40, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.9, 312, 3.2, '30 x 25 x 10 cm'),
('Pokémon Trading Card Game Battle Academy', 'pokemon-battle-academy', 'POK-BA2024', 'Complete Pokémon TCG learning set with 3 ready-to-play decks, game board, and tutorial guide. Perfect for beginners to learn the trading card game.', 'Pokémon TCG learning set', 5, 8, 24.99, 12.00, 29.99, 52, 20, 100, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.6, 178, 0.8, '26 x 18 x 5 cm'),
('Little Tikes Cape Cottage Playhouse', 'little-tikes-cape-cottage', 'LT-CCPH2024', 'Indoor/outdoor playhouse with working door, windows, and mail slot. Easy assembly and durable construction for years of play.', 'Classic cottage playhouse', 4, 1, 179.99, 95.00, 199.99, 6, 3, 15, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.5, 89, 12.8, '90 x 90 x 102 cm'),
('Osmo Little Genius Starter Kit', 'osmo-little-genius', 'OSMO-LGSK', 'Educational iPad game system that combines physical play with digital learning. Includes 4 learning games for letters, shapes, and problem-solving.', 'iPad learning system for kids', 3, 1, 79.99, 40.00, 99.99, 24, 10, 50, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.7, 234, 1.5, '25 x 20 x 8 cm'),
('Transformers Studio Series Optimus Prime', 'transformers-optimus-prime', 'HAS-TFSS86', 'Highly detailed Transformers figure that converts from robot to truck in 35 steps. Movie-accurate design with premium paint applications.', 'Movie-accurate transforming figure', 1, 3, 54.99, 28.00, 64.99, 31, 12, 60, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.8, 167, 0.6, '20 x 15 x 8 cm'),
('Fisher-Price Think & Learn Code-a-pillar', 'fisher-price-code-a-pillar', 'FP-TLCAP', 'Programmable caterpillar toy that teaches sequencing and problem-solving. Each segment controls a different movement direction.', 'Programming learning toy', 3, 4, 49.99, 22.00, 59.99, 42, 15, 80, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.3, 156, 1.8, '45 x 20 x 15 cm'),
('Monopoly Ultimate Banking Edition', 'monopoly-ultimate-banking', 'HAS-MONUB2024', 'Electronic banking version of Monopoly with touch-free cards and unit. No paper money - all transactions are digital.', 'Electronic banking Monopoly', 5, 3, 34.99, 16.00, 39.99, 67, 25, 120, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.4, 278, 1.5, '40 x 27 x 5 cm'),
('Nerf Rival Kronos XVIII-500', 'nerf-rival-kronos', 'HAS-NRK500', 'Rival series blaster with 5-round internal magazine and slam-fire action. Uses high-impact rounds for competitive play.', 'Competitive foam blaster', 4, 3, 29.99, 14.00, 34.99, 38, 15, 75, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.7, 189, 0.9, '22 x 15 x 8 cm'),
('Playmobil City Life Police Station', 'playmobil-police-station', 'PLY-PS70249', 'Detailed police station with jail cells, office, and garage. Includes 4 figures, police car, and numerous accessories.', 'Police headquarters playset', 2, 1, 89.99, 45.00, 109.99, 18, 8, 35, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.6, 134, 2.3, '45 x 30 x 15 cm'),
('Barbie Fashionistas Doll Collection', 'barbie-fashionistas-2024', 'MAT-BF2024', 'Set of 3 diverse Barbie dolls with unique fashions, body types, and careers. Promotes inclusivity and imaginative play.', '3 diverse Barbie dolls', 6, 4, 29.99, 14.00, 34.99, 89, 30, 150, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.5, 267, 0.6, '28 x 18 x 6 cm'),
('LEGO Star Wars Millennium Falcon', 'lego-star-wars-falcon', 'LEGO-75192', 'Ultimate Collector Series Millennium Falcon with over 7,500 pieces. Highly detailed interior and exterior with display stand.', 'UCS Millennium Falcon replica', 12, 2, 849.99, 480.00, 899.99, 3, 1, 8, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.9, 89, 13.2, '84 x 56 x 21 cm'),
('Melissa & Doug Let''s Play House! Dust!', 'melissa-doug-cleaning-set', 'MD-CHS25', 'Realistic cleaning set with 25 pieces including mop, broom, duster, and spray bottle. Encourages role play and responsibility.', '25-piece cleaning playset', 9, 5, 34.99, 16.00, 39.99, 56, 20, 100, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.8, 189, 1.8, '35 x 25 x 8 cm'),
('Hot Wheels 50-Car Pack', 'hot-wheels-50-pack', 'MAT-HW50PK', 'Assortment of 50 different Hot Wheels cars in various styles and colors. Great for starting or expanding a collection.', '50 assorted die-cast cars', 2, 4, 24.99, 10.00, 29.99, 124, 50, 250, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.6, 345, 1.5, '30 x 20 x 10 cm'),
('VTech Touch and Learn Activity Desk', 'vtech-activity-desk', 'VT-TLAD2024', 'Interactive learning desk with 5 activity pages that teach letters, numbers, music, and more. Grows with child through multiple modes.', 'Interactive learning desk', 3, 9, 79.99, 38.00, 89.99, 27, 10, 50, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.4, 178, 4.2, '50 x 40 x 15 cm'),
('Catan Board Game', 'catan-board-game', 'CAT-BASE2024', 'Award-winning strategy game where players collect resources and build settlements. For 3-4 players ages 10 and up.', 'Classic resource strategy game', 5, 10, 49.99, 24.00, 54.99, 45, 15, 80, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', TRUE, 4.8, 456, 1.3, '30 x 30 x 7 cm'),
('L.O.L. Surprise! Tweens Series 2', 'lol-tweens-series-2', 'MGA-LTWS2', 'Collectible tweens dolls with 15+ surprises each. Features fashion, accessories, and mystery elements for unboxing excitement.', 'Collectible surprise dolls', 6, 7, 24.99, 11.00, 29.99, 78, 25, 120, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.5, 234, 0.4, '18 x 12 x 6 cm'),
('Step2 Rain Showers Splash Pond', 'step2-splash-pond', 'ST2-RSSP2024', 'Water table with spinning water wheel, waterfall, and splash pond. Includes 10 water toys for outdoor water play.', 'Interactive water play table', 4, 1, 89.99, 45.00, 109.99, 14, 6, 30, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=400&fit=crop', FALSE, 4.7, 167, 5.8, '80 x 60 x 25 cm'),
('Marvel Legends Iron Man Hall of Armor', 'marvel-iron-man-armor', 'HAS-MLIA2024', 'Deluxe Iron Man figure with Hall of Armor display case. Includes multiple interchangeable armor parts and effect pieces.', 'Iron Man with display case', 11, 3, 44.99, 22.00, 49.99, 32, 12, 60, 'https://images.placeholder.com/400x400/4361ee/ffffff?text=Iron+Man', FALSE, 4.6, 145, 0.7, '20 x 15 x 10 cm'),
('Play-Doh Super Color Kit', 'play-doh-super-color-kit', 'HAS-PDSC20', '20 cans of Play-Doh in vibrant colors with 5 tools. Non-toxic and reusable modeling compound for creative play.', '20-color dough set with tools', 8, 3, 19.99, 8.00, 24.99, 156, 50, 300, 'https://images.placeholder.com/400x400/f72585/ffffff?text=Play-Doh', FALSE, 4.4, 289, 1.2, '25 x 18 x 8 cm'),
('Ravensburger 1000-Piece Puzzle', 'ravensburger-1000-puzzle', 'RAV-1000WP', 'High-quality 1000-piece puzzle with random-cut pieces for perfect fit. Features beautiful landscape artwork.', '1000-piece premium puzzle', 5, 10, 19.99, 9.00, 24.99, 67, 25, 120, 'https://images.placeholder.com/400x400/4895ef/ffffff?text=Puzzle', FALSE, 4.8, 198, 0.8, '27 x 20 x 5 cm'),
('Baby Einstein Magic Touch Piano', 'baby-einstein-piano', 'BE-MTP2024', 'Wooden toddler piano that plays with touch - no buttons needed. Features 6 instruments and 6 musical styles.', 'Touch-sensitive baby piano', 9, 1, 39.99, 18.00, 49.99, 48, 15, 80, 'https://images.placeholder.com/400x400/f8961e/ffffff?text=Piano', FALSE, 4.5, 134, 1.5, '30 x 25 x 10 cm'),
('Nerf Fortnite BASR-L Blaster', 'nerf-fortnite-basr-l', 'HAS-NFBASRL', 'Fortnite-themed sniper blaster with 6-dart clip and bolt-action prime. Includes 6 Elite darts and scope attachment.', 'Fortnite sniper replica', 4, 3, 39.99, 18.00, 49.99, 29, 12, 60, 'https://images.placeholder.com/400x400/e71d36/ffffff?text=Nerf', FALSE, 4.6, 178, 1.1, '60 x 25 x 10 cm'),
('LEGO Harry Potter Hogwarts Castle', 'lego-harry-potter-castle', 'LEGO-71043', 'Massive Hogwarts Castle with over 6,000 pieces. Includes microfigures of Harry, Ron, Hermione and detailed castle features.', 'Hogwarts Castle replica', 12, 2, 469.99, 260.00, 499.99, 8, 3, 20, 'https://images.placeholder.com/400x400/7209b7/ffffff?text=Hogwarts', TRUE, 4.9, 156, 8.8, '58 x 43 x 19 cm'),
('Barbie Camper Van', 'barbie-camper-van', 'MAT-BCV2024', 'Convertible camper van that transforms from vehicle to playset. Includes Barbie doll and camping accessories.', 'Transformable camper vehicle', 6, 4, 59.99, 28.00, 69.99, 23, 10, 50, 'https://images.placeholder.com/400x400/f72585/ffffff?text=Barbie', FALSE, 4.4, 89, 1.2, '35 x 20 x 15 cm'),
('Crayola Light Up Tracing Pad', 'crayola-tracing-pad', 'CRAY-LTP2024', 'LED light pad for tracing and drawing with 10 tracing sheets and supplies. Battery operated with adjustable brightness.', 'LED art tracing tablet', 8, 1, 29.99, 13.00, 34.99, 72, 25, 120, 'https://images.placeholder.com/400x400/2ec4b6/ffffff?text=Crayola', FALSE, 4.3, 167, 0.9, '35 x 25 x 3 cm'),
('Paw Patrol Ultimate Rescue HQ', 'paw-patrol-rescue-hq', 'SPN-PPURHQ', 'Deluxe headquarters with elevator, slide, and transforming vehicles. Includes 6 pup figures and numerous rescue accessories.', 'Paw Patrol command center', 9, 6, 129.99, 65.00, 149.99, 11, 5, 25, 'https://images.placeholder.com/400x400/4cc9f0/ffffff?text=Paw+Patrol', TRUE, 4.7, 145, 3.5, '45 x 35 x 20 cm'),
('Osmo Creative Starter Kit', 'osmo-creative-starter', 'OSMO-CSK2024', 'Drawing and creative learning system for iPad. Includes drawing board, marker, and games that teach art and creativity.', 'iPad drawing learning system', 3, 1, 69.99, 32.00, 79.99, 34, 12, 60, 'https://images.placeholder.com/400x400/3a0ca3/ffffff?text=Osmo', FALSE, 4.6, 189, 1.3, '25 x 20 x 6 cm'),
('Transformers Generations Selects', 'transformers-generations-selects', 'HAS-TFGS2024', 'Collector-grade Transformers figures with premium deco and accessories. Features characters from various Transformers series.', 'Premium collector figures', 1, 3, 39.99, 18.00, 44.99, 41, 15, 75, 'https://images.placeholder.com/400x400/f77f00/ffffff?text=Transformers', FALSE, 4.7, 134, 0.5, '18 x 12 x 6 cm'),
('Playmobil Family Fun at the Zoo', 'playmobil-zoo-set', 'PLY-ZOO70321', 'Zoo playset with animals, enclosures, and family figures. Includes lion, elephant, giraffe, and zoo keeper accessories.', 'Zoo animal playset', 2, 1, 69.99, 32.00, 79.99, 26, 10, 50, 'https://images.placeholder.com/400x400/2ec4b6/ffffff?text=Zoo', FALSE, 4.5, 98, 1.8, '40 x 30 x 12 cm'),
('Melissa & Doug Wooden Railway Set', 'melissa-doug-railway', 'MD-WRS120', '120-piece wooden railway system with trains, tracks, and accessories. Compatible with major wooden railway brands.', '120-piece train set', 2, 5, 89.99, 42.00, 99.99, 18, 8, 35, 'https://images.placeholder.com/400x400/f8961e/ffffff?text=Train+Set', FALSE, 4.8, 167, 3.2, '50 x 35 x 10 cm'),
('VTech Write and Learn Creative Center', 'vtech-creative-center', 'VT-WLCC2024', 'Magnetic drawing board with animated demonstrations for letter and number learning. Includes stencils and stampers.', 'Electronic drawing board', 3, 9, 29.99, 13.00, 34.99, 58, 20, 100, 'https://images.placeholder.com/400x400/4895ef/ffffff?text=VTech', FALSE, 4.4, 145, 1.1, '35 x 25 x 5 cm'),
('Codenames Board Game', 'codenames-board-game', 'CGE-CDM2024', 'Word association party game for 2-8 players. Teams compete to identify their agents based on one-word clues.', 'Word association party game', 5, 10, 24.99, 11.00, 29.99, 52, 20, 100, 'https://images.placeholder.com/400x400/7209b7/ffffff?text=Codenames', TRUE, 4.8, 289, 0.9, '24 x 24 x 6 cm'),
('L.O.L. Surprise! Winter Disco Series', 'lol-winter-disco', 'MGA-LWDS2024', 'Holiday-themed surprise dolls with glitter fashions and accessories. Each doll has 15+ surprises to unbox.', 'Holiday collectible dolls', 6, 7, 19.99, 8.00, 24.99, 89, 30, 150, 'https://images.placeholder.com/400x400/4cc9f0/ffffff?text=LOL+Surprise', FALSE, 4.5, 178, 0.3, '16 x 10 x 5 cm'),
('Little Tikes 3-in-1 Sports Zone', 'little-tikes-sports-zone', 'LT-3SZN2024', 'Multi-sport set with basketball, soccer, and bowling. Adjustable height grows with child for years of active play.', '3-sports activity center', 4, 1, 79.99, 38.00, 89.99, 22, 8, 40, 'https://images.placeholder.com/400x400/e71d36/ffffff?text=Sports', FALSE, 4.6, 134, 4.5, '80 x 60 x 30 cm'),
('Marvel Legends X-Men Collection', 'marvel-x-men-collection', 'HAS-MLXM2024', 'Set of 3 X-Men figures with premium detailing and accessories. Includes Wolverine, Cyclops, and Storm with display base.', 'X-Men action figure set', 11, 3, 74.99, 35.00, 84.99, 19, 8, 35, 'https://images.placeholder.com/400x400/dc2f02/ffffff?text=X-Men', TRUE, 4.7, 156, 0.9, '25 x 18 x 8 cm');

-- Insert Product Images
INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES 
(1, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'LEGO Roller Coaster front view', TRUE, 1),
(1, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'LEGO Roller Coaster side view', FALSE, 2),
(1, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'LEGO Roller Coaster details', FALSE, 3),
(2, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Spider-Man figure front', TRUE, 1),
(2, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Spider-Man figure back', FALSE, 2),
(3, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Barbie Dreamhouse front', TRUE, 1),
(3, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Barbie Dreamhouse interior', FALSE, 2),
(4, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Wooden puzzle set', TRUE, 1),
(5, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Nerf blaster front', TRUE, 1),
(6, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'VTech smartwatch', TRUE, 1),
(7, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Crayola art case', TRUE, 1),
(8, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Hot Wheels garage', TRUE, 1),
(9, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'Play-Doh kitchen', TRUE, 1),
(10, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&h=600&fit=crop', 'LOL Surprise house', TRUE, 1);

-- Insert Customers (50 customers)
INSERT INTO customers (first_name, last_name, email, phone, password_hash, status, avatar_color, email_verified) VALUES 
('James', 'Wilson', 'james.wilson@email.com', '(555) 123-4567', MD5('password123'), 'active', '#4361ee', TRUE),
('Sarah', 'Johnson', 'sarah.johnson@email.com', '(555) 234-5678', MD5('password123'), 'active', '#f72585', TRUE),
('Michael', 'Brown', 'michael.brown@email.com', '(555) 345-6789', MD5('password123'), 'active', '#4cc9f0', TRUE),
('Emily', 'Davis', 'emily.davis@email.com', '(555) 456-7890', MD5('password123'), 'active', '#f8961e', TRUE),
('David', 'Miller', 'david.miller@email.com', '(555) 567-8901', MD5('password123'), 'active', '#4895ef', TRUE),
('Jennifer', 'Taylor', 'jennifer.taylor@email.com', '(555) 678-9012', MD5('password123'), 'active', '#7209b7', TRUE),
('Christopher', 'Anderson', 'chris.anderson@email.com', '(555) 789-0123', MD5('password123'), 'active', '#f77f00', TRUE),
('Amanda', 'Thomas', 'amanda.thomas@email.com', '(555) 890-1234', MD5('password123'), 'active', '#2ec4b6', TRUE),
('Matthew', 'Jackson', 'matthew.jackson@email.com', '(555) 901-2345', MD5('password123'), 'active', '#3a0ca3', TRUE),
('Jessica', 'White', 'jessica.white@email.com', '(555) 012-3456', MD5('password123'), 'active', '#e71d36', TRUE),
('Daniel', 'Harris', 'daniel.harris@email.com', '(555) 123-4567', MD5('password123'), 'active', '#dc2f02', TRUE),
('Ashley', 'Martin', 'ashley.martin@email.com', '(555) 234-5678', MD5('password123'), 'active', '#4361ee', TRUE),
('Robert', 'Thompson', 'robert.thompson@email.com', '(555) 345-6789', MD5('password123'), 'active', '#f72585', TRUE),
('Michelle', 'Garcia', 'michelle.garcia@email.com', '(555) 456-7890', MD5('password123'), 'active', '#4cc9f0', TRUE),
('William', 'Martinez', 'william.martinez@email.com', '(555) 567-8901', MD5('password123'), 'active', '#f8961e', TRUE),
('Elizabeth', 'Robinson', 'elizabeth.robinson@email.com', '(555) 678-9012', MD5('password123'), 'active', '#4895ef', TRUE),
('Joshua', 'Clark', 'joshua.clark@email.com', '(555) 789-0123', MD5('password123'), 'active', '#7209b7', TRUE),
('Stephanie', 'Rodriguez', 'stephanie.rodriguez@email.com', '(555) 890-1234', MD5('password123'), 'active', '#f77f00', TRUE),
('Andrew', 'Lewis', 'andrew.lewis@email.com', '(555) 901-2345', MD5('password123'), 'active', '#2ec4b6', TRUE),
('Laura', 'Lee', 'laura.lee@email.com', '(555) 012-3456', MD5('password123'), 'active', '#3a0ca3', TRUE),
('Kevin', 'Walker', 'kevin.walker@email.com', '(555) 123-4567', MD5('password123'), 'active', '#e71d36', TRUE),
('Nicole', 'Hall', 'nicole.hall@email.com', '(555) 234-5678', MD5('password123'), 'active', '#dc2f02', TRUE),
('Brian', 'Allen', 'brian.allen@email.com', '(555) 345-6789', MD5('password123'), 'active', '#4361ee', TRUE),
('Rebecca', 'Young', 'rebecca.young@email.com', '(555) 456-7890', MD5('password123'), 'active', '#f72585', TRUE),
('Steven', 'King', 'steven.king@email.com', '(555) 567-8901', MD5('password123'), 'active', '#4cc9f0', TRUE),
('Kimberly', 'Wright', 'kimberly.wright@email.com', '(555) 678-9012', MD5('password123'), 'active', '#f8961e', TRUE),
('Edward', 'Scott', 'edward.scott@email.com', '(555) 789-0123', MD5('password123'), 'active', '#4895ef', TRUE),
('Amy', 'Green', 'amy.green@email.com', '(555) 890-1234', MD5('password123'), 'active', '#7209b7', TRUE),
('Timothy', 'Baker', 'timothy.baker@email.com', '(555) 901-2345', MD5('password123'), 'active', '#f77f00', TRUE),
('Angela', 'Adams', 'angela.adams@email.com', '(555) 012-3456', MD5('password123'), 'active', '#2ec4b6', TRUE),
('Richard', 'Nelson', 'richard.nelson@email.com', '(555) 123-4567', MD5('password123'), 'active', '#3a0ca3', TRUE),
('Samantha', 'Carter', 'samantha.carter@email.com', '(555) 234-5678', MD5('password123'), 'active', '#e71d36', TRUE),
('Charles', 'Mitchell', 'charles.mitchell@email.com', '(555) 345-6789', MD5('password123'), 'active', '#dc2f02', TRUE),
('Christina', 'Perez', 'christina.perez@email.com', '(555) 456-7890', MD5('password123'), 'active', '#4361ee', TRUE),
('Patrick', 'Roberts', 'patrick.roberts@email.com', '(555) 567-8901', MD5('password123'), 'active', '#f72585', TRUE),
('Megan', 'Turner', 'megan.turner@email.com', '(555) 678-9012', MD5('password123'), 'active', '#4cc9f0', TRUE),
('Jason', 'Phillips', 'jason.phillips@email.com', '(555) 789-0123', MD5('password123'), 'active', '#f8961e', TRUE),
('Heather', 'Campbell', 'heather.campbell@email.com', '(555) 890-1234', MD5('password123'), 'active', '#4895ef', TRUE),
('Ryan', 'Parker', 'ryan.parker@email.com', '(555) 901-2345', MD5('password123'), 'active', '#7209b7', TRUE),
('Rachel', 'Evans', 'rachel.evans@email.com', '(555) 012-3456', MD5('password123'), 'active', '#f77f00', TRUE),
('Jeffrey', 'Edwards', 'jeffrey.edwards@email.com', '(555) 123-4567', MD5('password123'), 'active', '#2ec4b6', TRUE),
('Lauren', 'Collins', 'lauren.collins@email.com', '(555) 234-5678', MD5('password123'), 'active', '#3a0ca3', TRUE),
('Gary', 'Stewart', 'gary.stewart@email.com', '(555) 345-6789', MD5('password123'), 'active', '#e71d36', TRUE),
('Tiffany', 'Sanchez', 'tiffany.sanchez@email.com', '(555) 456-7890', MD5('password123'), 'active', '#dc2f02', TRUE),
('Jacob', 'Morris', 'jacob.morris@email.com', '(555) 567-8901', MD5('password123'), 'active', '#4361ee', TRUE),
('Amber', 'Rogers', 'amber.rogers@email.com', '(555) 678-9012', MD5('password123'), 'active', '#f72585', TRUE),
('Nathan', 'Reed', 'nathan.reed@email.com', '(555) 789-0123', MD5('password123'), 'active', '#4cc9f0', TRUE),
('Victoria', 'Cook', 'victoria.cook@email.com', '(555) 890-1234', MD5('password123'), 'active', '#f8961e', TRUE),
('Samuel', 'Morgan', 'samuel.morgan@email.com', '(555) 901-2345', MD5('password123'), 'active', '#4895ef', TRUE),
('Kelly', 'Bell', 'kelly.bell@email.com', '(555) 012-3456', MD5('password123'), 'active', '#7209b7', TRUE);

-- Insert Addresses
INSERT INTO addresses (customer_id, type, full_name, phone, address_line1, city, state, zip_code, country, is_default) VALUES 
(1, 'shipping', 'James Wilson', '(555) 123-4567', '123 Maple Street', 'New York', 'NY', '10001', 'United States', TRUE),
(1, 'billing', 'James Wilson', '(555) 123-4567', '123 Maple Street', 'New York', 'NY', '10001', 'United States', TRUE),
(2, 'shipping', 'Sarah Johnson', '(555) 234-5678', '456 Oak Avenue', 'Los Angeles', 'CA', '90210', 'United States', TRUE),
(3, 'shipping', 'Michael Brown', '(555) 345-6789', '789 Pine Road', 'Chicago', 'IL', '60601', 'United States', TRUE),
(4, 'shipping', 'Emily Davis', '(555) 456-7890', '321 Elm Street', 'Houston', 'TX', '77001', 'United States', TRUE),
(5, 'shipping', 'David Miller', '(555) 567-8901', '654 Birch Lane', 'Phoenix', 'AZ', '85001', 'United States', TRUE),
(6, 'shipping', 'Jennifer Taylor', '(555) 678-9012', '987 Cedar Blvd', 'Philadelphia', 'PA', '19102', 'United States', TRUE),
(7, 'shipping', 'Christopher Anderson', '(555) 789-0123', '159 Spruce Court', 'San Antonio', 'TX', '78201', 'United States', TRUE),
(8, 'shipping', 'Amanda Thomas', '(555) 890-1234', '753 Willow Way', 'San Diego', 'CA', '92101', 'United States', TRUE),
(9, 'shipping', 'Matthew Jackson', '(555) 901-2345', '246 Aspen Drive', 'Dallas', 'TX', '75201', 'United States', TRUE),
(10, 'shipping', 'Jessica White', '(555) 012-3456', '135 Redwood Road', 'San Jose', 'CA', '95101', 'United States', TRUE);

-- Insert Orders (20 orders)
INSERT INTO orders (order_number, customer_id, total_amount, subtotal_amount, shipping_amount, tax_amount, status, payment_status, payment_method, transaction_id, shipping_address_id, billing_address_id) VALUES 
('ORD-10001', 1, 154.97, 139.97, 10.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001234', 1, 1),
('ORD-10002', 2, 89.99, 79.99, 5.00, 5.00, 'processing', 'paid', 'PayPal', 'TXN-001235', 3, 3),
('ORD-10003', 3, 234.98, 214.98, 15.00, 5.00, 'shipped', 'paid', 'Credit Card', 'TXN-001236', 4, 4),
('ORD-10004', 4, 34.99, 29.99, 0.00, 5.00, 'pending', 'pending', 'Credit Card', NULL, 5, 5),
('ORD-10005', 5, 119.98, 109.98, 5.00, 5.00, 'confirmed', 'paid', 'Credit Card', 'TXN-001237', 6, 6),
('ORD-10006', 6, 279.99, 259.99, 15.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001238', 7, 7),
('ORD-10007', 7, 64.99, 59.99, 0.00, 5.00, 'delivered', 'paid', 'PayPal', 'TXN-001239', 8, 8),
('ORD-10008', 8, 189.99, 174.99, 10.00, 5.00, 'processing', 'paid', 'Credit Card', 'TXN-001240', 9, 9),
('ORD-10009', 9, 94.99, 84.99, 5.00, 5.00, 'shipped', 'paid', 'Credit Card', 'TXN-001241', 10, 10),
('ORD-10010', 10, 154.99, 139.99, 10.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001242', 11, 11),
('ORD-10011', 11, 74.99, 64.99, 5.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001243', 1, 1),
('ORD-10012', 12, 199.99, 184.99, 10.00, 5.00, 'processing', 'paid', 'PayPal', 'TXN-001244', 1, 1),
('ORD-10013', 13, 44.99, 39.99, 0.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001245', 1, 1),
('ORD-10014', 14, 129.99, 119.99, 5.00, 5.00, 'shipped', 'paid', 'Credit Card', 'TXN-001246', 1, 1),
('ORD-10015', 15, 84.99, 74.99, 5.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001247', 1, 1),
('ORD-10016', 16, 229.99, 209.99, 15.00, 5.00, 'processing', 'paid', 'PayPal', 'TXN-001248', 1, 1),
('ORD-10017', 17, 59.99, 49.99, 5.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001249', 1, 1),
('ORD-10018', 18, 169.99, 154.99, 10.00, 5.00, 'shipped', 'paid', 'Credit Card', 'TXN-001250', 1, 1),
('ORD-10019', 19, 99.99, 89.99, 5.00, 5.00, 'delivered', 'paid', 'Credit Card', 'TXN-001251', 1, 1),
('ORD-10020', 20, 144.99, 129.99, 10.00, 5.00, 'processing', 'paid', 'PayPal', 'TXN-001252', 1, 1);

-- Insert Order Items
INSERT INTO order_items (order_id, product_id, product_name, product_sku, product_price, quantity, total_price) VALUES 
(1, 1, 'LEGO Creator Expert Roller Coaster', 'LEGO-10261', 379.99, 1, 379.99),
(1, 2, 'Marvel Legends Spider-Man Action Figure', 'HAS-MLSP01', 24.99, 2, 49.98),
(2, 3, 'Barbie Dreamhouse', 'MAT-BDH2024', 199.99, 1, 199.99),
(3, 4, 'Melissa & Doug Wooden Puzzle Set', 'MD-PUZ50', 29.99, 3, 89.97),
(3, 5, 'Nerf Elite 2.0 EaglePoint RD-8 Blaster', 'HAS-NERFEP8', 49.99, 1, 49.99),
(4, 6, 'VTech KidiZoom Smartwatch DX2', 'VT-KZSDX2', 49.99, 1, 49.99),
(5, 7, 'Crayola Inspiration Art Case', 'CRAY-ART140', 24.99, 2, 49.98),
(5, 8, 'Hot Wheels City Ultimate Garage', 'MAT-HWCG2024', 129.99, 1, 129.99),
(6, 9, 'Play-Doh Kitchen Creations', 'HAS-PDKC32', 39.99, 1, 39.99),
(7, 10, 'L.O.L. Surprise! OMG House', 'MGA-LOLHOUSE', 149.99, 1, 149.99),
(8, 12, 'National Geographic Mega Science Kit', 'NG-SCIENCE45', 49.99, 1, 49.99),
(8, 15, 'Baby Alive Sweet Spoonfuls Doll', 'HAS-BASS2024', 34.99, 1, 34.99),
(9, 18, 'Pokémon Trading Card Game Battle Academy', 'POK-BA2024', 24.99, 2, 49.98),
(10, 20, 'Osmo Little Genius Starter Kit', 'OSMO-LGSK', 79.99, 1, 79.99),
(11, 13, 'Ravensburger Disney Villainous Board Game', 'RAV-DV2024', 39.99, 1, 39.99),
(11, 17, 'Magnatiles Clear Colors 100-Piece Set', 'MAG-CL100', 129.99, 1, 129.99),
(12, 19, 'Little Tikes Cape Cottage Playhouse', 'LT-CCPH2024', 179.99, 1, 179.99),
(13, 11, 'DJI Mini 3 Drone', 'DJI-MINI3', 469.99, 1, 469.99),
(14, 14, 'Transformers Studio Series Optimus Prime', 'HAS-TFSS86', 54.99, 1, 54.99),
(14, 16, 'Fisher-Price Think & Learn Code-a-pillar', 'FP-TLCAP', 49.99, 1, 49.99),
(15, 21, 'Monopoly Ultimate Banking Edition', 'HAS-MONUB2024', 34.99, 1, 34.99),
(15, 25, 'Playmobil City Life Police Station', 'PLY-PS70249', 89.99, 1, 89.99),
(16, 22, 'Nerf Rival Kronos XVIII-500', 'HAS-NRK500', 29.99, 2, 59.98),
(16, 28, 'Barbie Fashionistas Doll Collection', 'MAT-BF2024', 29.99, 1, 29.99),
(17, 23, 'LEGO Star Wars Millennium Falcon', 'LEGO-75192', 849.99, 1, 849.99),
(18, 24, 'Melissa & Doug Let''s Play House! Dust!', 'MD-CHS25', 34.99, 1, 34.99),
(18, 27, 'VTech Touch and Learn Activity Desk', 'VT-TLAD2024', 79.99, 1, 79.99),
(19, 26, 'Hot Wheels 50-Car Pack', 'MAT-HW50PK', 24.99, 3, 74.97),
(20, 29, 'Catan Board Game', 'CAT-BASE2024', 49.99, 1, 49.99),
(20, 30, 'L.O.L. Surprise! Tweens Series 2', 'MGA-LTWS2', 24.99, 1, 24.99);

-- Insert Order Status History
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
(5, 'confirmed', 'Payment received and order confirmed'),
(6, 'pending', 'Order placed by customer'),
(6, 'confirmed', 'Payment processed successfully'),
(6, 'processing', 'Order in packing station'),
(7, 'pending', 'Order placed by customer'),
(7, 'confirmed', 'Payment received'),
(7, 'shipped', 'Shipped via UPS - Tracking: 1Z9876543212345678'),
(7, 'delivered', 'Delivered to customer'),
(8, 'pending', 'Order placed by customer'),
(8, 'confirmed', 'Awaiting inventory check'),
(9, 'pending', 'Order placed by customer'),
(9, 'confirmed', 'Payment received'),
(9, 'processing', 'Special handling required for fragile items'),
(10, 'pending', 'Order placed by customer'),
(10, 'confirmed', 'Payment received'),
(10, 'shipped', 'International shipping initiated');

-- Insert Stock Movements
INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, reason, reference) VALUES 
(1, 'in', 50, 0, 50, 'Initial stock', 'PO-001'),
(1, 'out', 2, 50, 48, 'Order ORD-10001', 'ORD-10001'),
(2, 'in', 100, 0, 100, 'Initial stock', 'PO-002'),
(2, 'out', 5, 100, 95, 'Order ORD-10001', 'ORD-10001'),
(3, 'in', 30, 0, 30, 'Initial stock', 'PO-003'),
(3, 'out', 1, 30, 29, 'Order ORD-10002', 'ORD-10002'),
(4, 'in', 150, 0, 150, 'Initial stock', 'PO-004'),
(4, 'out', 10, 150, 140, 'Order ORD-10003', 'ORD-10003'),
(5, 'in', 75, 0, 75, 'Initial stock', 'PO-005'),
(5, 'out', 3, 75, 72, 'Order ORD-10003', 'ORD-10003'),
(6, 'in', 50, 34, 84, 'Restock from supplier', 'PO-006'),
(7, 'out', 5, 89, 84, 'Order fulfillment', 'ORD-10011'),
(8, 'in', 20, 15, 35, 'New shipment received', 'PO-007'),
(9, 'out', 10, 56, 46, 'Bulk order', 'ORD-10012'),
(10, 'adjustment', 2, 12, 14, 'Inventory correction', 'ADJ-001'),
(11, 'in', 15, 28, 43, 'Seasonal stock increase', 'PO-008'),
(12, 'out', 3, 28, 25, 'Online orders', 'ORD-10013'),
(13, 'in', 30, 23, 53, 'Supplier delivery', 'PO-009'),
(14, 'out', 8, 31, 23, 'Store sales', 'ORD-10014'),
(15, 'adjustment', -2, 38, 36, 'Damaged goods', 'ADJ-002');

-- Insert Low Stock Alerts
INSERT INTO low_stock_alerts (product_id, current_stock, min_stock_level, alert_level) VALUES 
(1, 5, 10, 'low'),
(3, 2, 5, 'critical'),
(8, 3, 8, 'critical'),
(15, 4, 15, 'low'),
(20, 2, 10, 'critical'),
(19, 3, 6, 'critical'),
(23, 1, 3, 'critical'),
(28, 5, 10, 'low');

-- Insert Promotions
INSERT INTO promotions (name, code, type, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, used_count, start_date, end_date, status) VALUES 
('Summer Sale 2024', 'SUMMER25', 'discount-code', 'percentage', 25.00, 50.00, 100.00, 500, 45, '2024-06-01 00:00:00', '2024-08-31 23:59:59', 'active'),
('Welcome Discount', 'WELCOME10', 'discount-code', 'percentage', 10.00, 0.00, 50.00, NULL, 123, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 'active'),
('Free Shipping', 'FREESHIP50', 'discount-code', 'shipping', 0.00, 50.00, NULL, 200, 67, '2024-07-01 00:00:00', '2024-07-31 23:59:59', 'active'),
('Back to School', 'SCHOOL20', 'special-offer', 'percentage', 20.00, 30.00, 75.00, 300, 12, '2024-08-15 00:00:00', '2024-09-15 23:59:59', 'upcoming'),
('Flash Sale', 'FLASH30', 'flash-sale', 'percentage', 30.00, 25.00, 60.00, 100, 89, '2024-07-15 00:00:00', '2024-07-16 23:59:59', 'active'),
('Holiday Special', 'HOLIDAY15', 'discount-code', 'percentage', 15.00, 75.00, 45.00, 400, 234, '2024-11-20 00:00:00', '2024-12-31 23:59:59', 'inactive');

-- Insert Promotion Usage
INSERT INTO promotion_usage (promotion_id, order_id, customer_id, discount_amount) VALUES 
(1, 1, 1, 32.49),
(1, 2, 2, 22.50),
(2, 3, 3, 8.99),
(3, 4, 4, 15.00),
(1, 5, 5, 29.99),
(2, 6, 6, 18.50),
(5, 7, 7, 45.00),
(1, 8, 8, 42.50),
(2, 9, 9, 9.50),
(3, 10, 10, 12.00),
(2, 11, 11, 7.50),
(3, 12, 12, 12.00),
(1, 13, 13, 11.25),
(5, 14, 14, 38.99),
(2, 15, 15, 8.50),
(1, 16, 16, 57.50),
(3, 17, 17, 8.00),
(2, 18, 18, 25.49),
(5, 19, 19, 29.99),
(1, 20, 20, 36.25);

-- Insert Pages
INSERT INTO pages (title, slug, content, meta_description, status, created_by, published_at) VALUES 
('About Us', 'about-us', '<h1>About ToyStore</h1><p>Welcome to the ultimate destination for toys and games!</p>', 'Learn about our toy store', 'published', 1, NOW()),
('Contact Us', 'contact', '<h1>Contact ToyStore</h1><p>Get in touch with our team.</p>', 'Contact information', 'published', 1, NOW()),
('Shipping Information', 'shipping', '<h1>Shipping Details</h1><p>Learn about our shipping policies.</p>', 'Shipping and delivery information', 'published', 1, NOW()),
('Returns Policy', 'returns', '<h1>Returns & Exchanges</h1><p>Our return policy details.</p>', 'Return and exchange policy', 'published', 1, NOW()),
('Privacy Policy', 'privacy', '<h1>Privacy Policy</h1><p>How we protect your data.</p>', 'Privacy policy information', 'draft', 1, NULL);

-- Insert Banners
INSERT INTO banners (title, description, image_url, target_url, position, status, button_text, button_color, sort_order, created_by) VALUES 
('Summer Toy Sale', 'Up to 50% off on selected toys', 'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?w=800', '/products?category=all', 'homepage-top', 'active', 'Shop Now', '#4361ee', 1, 1),
('New Arrivals', 'Discover the latest toys in our collection', 'https://images.unsplash.com/photo-1578774183724-395272fd6fe0?w=800', '/products?new=true', 'homepage-middle', 'active', 'Explore', '#f72585', 2, 1),
('Free Shipping', 'Free shipping on orders over $50', 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=800', '/shipping-info', 'all-pages-top', 'active', 'Learn More', '#4cc9f0', 3, 1),
('Educational Toys', 'Learning through play', 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?w=800', '/categories/educational-toys', 'homepage-bottom', 'active', 'Discover', '#f8961e', 4, 1),
('Holiday Special', 'Perfect gifts for the holiday season', 'https://images.unsplash.com/photo-1549465228-94f546e2e1b8?w=800', '/products?featured=true', 'homepage-top', 'scheduled', 'Find Gifts', '#4895ef', 5, 1),
('Back to School', 'Educational toys for young learners', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800', '/categories/educational-toys', 'homepage-middle', 'upcoming', 'Shop Learning', '#7209b7', 6, 1),
('LEGO Collection', 'Build amazing creations', 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=800', '/categories/building-sets', 'category-top', 'active', 'Build Now', '#f77f00', 1, 1),
('Action Figures', 'Heroes and villains await', 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?w=800', '/categories/action-figures', 'category-top', 'active', 'Collect', '#2ec4b6', 2, 1);

-- Insert Activities
INSERT INTO activities (type, title, description, icon, related_id, related_type, created_by, created_at) VALUES 
('order', 'New Order Received', 'Order #ORD-10001 has been placed by James Wilson', 'shopping-cart', 1, 'order', 1, '2024-01-15 09:30:00'),
('user', 'New Customer Registration', 'Sarah Johnson registered a new account', 'user-plus', 2, 'customer', 1, '2024-01-15 10:15:00'),
('product', 'Low Stock Alert', 'LEGO Roller Coaster is running low on stock (5 remaining)', 'exclamation-triangle', 1, 'product', 1, '2024-01-15 11:20:00'),
('sale', 'Daily Sales Report', '$1,245.67 in total sales today', 'chart-bar', NULL, 'report', 1, '2024-01-15 17:00:00'),
('inventory', 'Stock Updated', '50 units of Marvel Spider-Man added to inventory', 'box', 2, 'product', 2, '2024-01-16 08:45:00'),
('order', 'Order Shipped', 'Order #ORD-10003 has been shipped to Michael Brown', 'truck', 3, 'order', 2, '2024-01-16 14:30:00'),
('user', 'Customer Login', 'Emily Davis logged into their account', 'user-check', 4, 'customer', 1, '2024-01-16 16:20:00'),
('product', 'New Product Added', 'Barbie Dreamhouse added to product catalog', 'plus-circle', 3, 'product', 1, '2024-01-17 09:10:00'),
('promotion', 'Promotion Created', 'Summer Sale 2024 promotion activated', 'tag', 1, 'promotion', 1, '2024-01-17 11:30:00'),
('order', 'Order Delivered', 'Order #ORD-10001 delivered successfully', 'package', 1, 'order', 1, '2024-01-18 13:15:00'),
('user', 'Password Reset', 'David Miller requested password reset', 'key', 5, 'customer', 1, '2024-01-18 15:40:00'),
('inventory', 'Stock Adjustment', 'Adjusted Melissa & Doug Puzzle stock count', 'edit', 4, 'product', 2, '2024-01-19 10:20:00'),
('order', 'Order Cancelled', 'Order #ORD-10004 was cancelled by customer', 'x-circle', 4, 'order', 1, '2024-01-19 11:50:00'),
('product', 'Product Review Added', 'New 5-star review for LEGO Roller Coaster', 'star', 1, 'product', 1, '2024-01-20 09:30:00'),
('sale', 'Weekly Sales Summary', '$8,765.43 in total sales this week', 'trending-up', NULL, 'report', 1, '2024-01-20 18:00:00'),
('user', 'Customer Profile Updated', 'Jennifer Taylor updated their profile information', 'user-edit', 6, 'customer', 1, '2024-01-21 14:25:00'),
('order', 'Payment Received', 'Payment confirmed for Order #ORD-10005', 'credit-card', 5, 'order', 2, '2024-01-21 16:10:00'),
('product', 'Price Update', 'Nerf Blaster price updated to $49.99', 'dollar-sign', 5, 'product', 1, '2024-01-22 08:45:00'),
('inventory', 'Low Stock Resolved', 'LEGO Roller Coaster stock replenished', 'check-circle', 1, 'product', 2, '2024-01-22 11:30:00'),
('promotion', 'Promotion Usage', 'SUMMER25 code used by customer #15', 'shopping-bag', 1, 'promotion', 1, '2024-01-22 13:20:00'),
('system', 'Database Backup', 'Automatic nightly backup completed successfully', 'database', NULL, 'system', 1, '2024-01-23 02:00:00'),
('user', 'Admin Login', 'Super Admin logged into admin panel', 'shield', 1, 'admin', 1, '2024-01-23 08:15:00'),
('order', 'Bulk Order Processing', 'Processed 15 orders in batch', 'package', NULL, 'order', 2, '2024-01-23 10:30:00'),
('product', 'Category Updated', 'Updated Action Figures category details', 'folder', 1, 'category', 1, '2024-01-23 14:20:00'),
('promotion', 'Promotion Expired', 'Flash Sale promotion has ended', 'clock', 5, 'promotion', 1, '2024-01-23 23:59:00');

-- Insert Product Reviews
INSERT INTO product_reviews (product_id, customer_id, rating, title, comment, status, created_at) VALUES 
(1, 1, 5, 'Amazing Build Quality!', 'This LEGO roller coaster is absolutely incredible. The details are amazing and it was so much fun to build with my son. Highly recommended for any LEGO fan!', 'approved', '2024-01-10 14:30:00'),
(1, 2, 4, 'Great but challenging', 'The build was more complex than expected, but the final result is stunning. The motor works perfectly and the coaster actually runs smoothly.', 'approved', '2024-01-11 16:45:00'),
(2, 3, 5, 'Perfect Spider-Man Figure', 'As a Marvel collector, this is one of the best Spider-Man figures I own. The articulation is fantastic and the accessories are great quality.', 'approved', '2024-01-12 09:20:00'),
(2, 4, 4, 'Good value for money', 'Nice detailing and good poseability. The web effects are a nice touch. Would recommend for any Spider-Man fan.', 'approved', '2024-01-13 11:15:00'),
(3, 5, 5, 'Dream come true for my daughter', 'My daughter absolutely loves this Barbie Dreamhouse. The quality is excellent and there are so many features to explore. Worth every penny!', 'approved', '2024-01-14 13:40:00'),
(3, 6, 4, 'Great features', 'The elevator and slide work well. Assembly took some time but the instructions were clear. My kids play with this for hours.', 'approved', '2024-01-15 15:25:00'),
(4, 7, 5, 'Educational and fun', 'These puzzles are perfect for my 3-year-old. The wooden pieces are durable and the images are engaging. Great for developing motor skills.', 'approved', '2024-01-16 10:10:00'),
(4, 8, 5, 'High quality puzzles', 'Melissa & Doug never disappoints. The puzzles are well-made and the pieces fit perfectly. We have almost all of their puzzle sets.', 'approved', '2024-01-17 12:30:00'),
(5, 9, 4, 'Fun blaster', 'My son loves this Nerf blaster. The motorized feature works well and it has good range. Batteries not included was a bit disappointing though.', 'approved', '2024-01-18 14:45:00'),
(5, 10, 3, 'Okay performance', 'Works as expected but the battery life could be better. Kids enjoy it but it is not the most powerful blaster in the series.', 'approved', '2024-01-19 16:20:00'),
(6, 11, 4, 'Great kids watch', 'My daughter loves the camera and games. The battery lasts a decent amount of time. Good value for the features offered.', 'approved', '2024-01-20 08:35:00'),
(6, 12, 5, 'Perfect first smartwatch', 'Bought this for my 7-year-old and she absolutely loves it. The parental controls are useful and the games are age-appropriate.', 'approved', '2024-01-21 10:50:00'),
(7, 13, 5, 'Complete art set', 'This has everything my artistic daughter needs. The case keeps everything organized and the quality of supplies is good for the price.', 'approved', '2024-01-22 13:15:00'),
(7, 14, 4, 'Good variety', 'Lots of colors and tools. The markers work well and the crayons are vibrant. Great for young artists starting out.', 'approved', '2024-01-23 15:40:00'),
(8, 15, 5, 'Hours of fun', 'My son plays with this Hot Wheels garage every day. The multiple levels and features keep him entertained for hours. Very durable construction.', 'approved', '2024-01-24 17:25:00'),
(8, 16, 4, 'Bigger than expected', 'This is quite large but that makes it more fun. Assembly required some time but the result is impressive. Cars run smoothly through the tracks.', 'approved', '2024-01-25 09:30:00'),
(9, 17, 5, 'Creative fun', 'My kids love this Play-Doh kitchen. The tools work well and there is plenty of Play-Doh included. Easy to clean up which is a bonus.', 'approved', '2024-01-26 11:45:00'),
(9, 18, 4, 'Good set', 'Lots of accessories and colors. The oven feature is fun for kids. The Play-Doh quality is good and doesn''t dry out too quickly.', 'approved', '2024-01-27 14:00:00'),
(10, 19, 5, 'Surprise hit', 'My daughter was thrilled with all the surprises in this LOL house. The quality is good and there are many accessories to play with.', 'approved', '2024-01-28 16:15:00'),
(10, 20, 4, 'Fun unboxing experience', 'The surprise element makes this exciting for kids. The dolls are cute and the house has nice features. A bit pricey but good quality.', 'approved', '2024-01-29 18:30:00');

-- Insert Wishlist
INSERT INTO wishlist (customer_id, product_id, added_at) VALUES 
(1, 3, '2024-01-10 09:00:00'),
(1, 8, '2024-01-11 10:30:00'),
(1, 15, '2024-01-12 14:20:00'),
(2, 1, '2024-01-13 11:45:00'),
(2, 12, '2024-01-14 16:10:00'),
(3, 5, '2024-01-15 08:30:00'),
(3, 18, '2024-01-16 13:25:00'),
(4, 7, '2024-01-17 10:15:00'),
(4, 20, '2024-01-18 15:40:00'),
(5, 2, '2024-01-19 09:50:00'),
(5, 9, '2024-01-20 14:35:00'),
(6, 4, '2024-01-21 11:20:00'),
(6, 16, '2024-01-22 17:05:00'),
(7, 6, '2024-01-23 08:45:00'),
(7, 13, '2024-01-24 12:30:00'),
(8, 10, '2024-01-25 10:55:00'),
(8, 19, '2024-01-26 15:20:00'),
(9, 11, '2024-01-27 09:15:00'),
(9, 17, '2024-01-28 14:50:00'),
(10, 14, '2024-01-29 11:25:00'),
(10, 20, '2024-01-30 16:40:00');

-- Insert Password Resets
INSERT INTO password_resets (email, token, expires_at, used, created_at) VALUES 
('james.wilson@email.com', 'abc123def456ghi789jkl012mno345pqr678stu901', '2024-01-16 09:30:00', TRUE, '2024-01-15 09:30:00'),
('sarah.johnson@email.com', 'xyz789uvw012abc345def678ghi901jkl234mno567', '2024-01-17 14:20:00', FALSE, '2024-01-16 14:20:00'),
('michael.brown@email.com', 'pqr234stu567vwx890yza123bcd456efg789hij012', '2024-01-18 11:45:00', TRUE, '2024-01-17 11:45:00'),
('emily.davis@email.com', 'klm345nop678qrs901tuv234wxy567zab890cde123', '2024-01-19 16:30:00', FALSE, '2024-01-18 16:30:00'),
('david.miller@email.com', 'fgh456ijk789lmn012opq345rst678uvw901xyz234', '2024-01-20 13:15:00', TRUE, '2024-01-19 13:15:00');

-- Insert Reports
INSERT INTO reports (report_type, report_name, date_range_start, date_range_end, file_format, file_path, generated_by, parameters, created_at) VALUES 
('sales', 'Monthly Sales Report - January 2024', '2024-01-01', '2024-01-31', 'pdf', '/reports/sales_january_2024.pdf', 1, '{"include_refunds": false, "group_by_category": true}', '2024-02-01 09:00:00'),
('customers', 'New Customers Report - Q1 2024', '2024-01-01', '2024-03-31', 'excel', '/reports/customers_q1_2024.xlsx', 1, '{"include_inactive": false, "export_emails": true}', '2024-04-01 10:30:00'),
('products', 'Top Selling Products - 2024', '2024-01-01', '2024-12-31', 'csv', '/reports/top_products_2024.csv', 2, '{"min_sales": 50, "sort_by": "revenue"}', '2024-01-15 14:20:00'),
('inventory', 'Low Stock Alert Report', '2024-01-01', '2024-01-31', 'pdf', '/reports/low_stock_january_2024.pdf', 2, '{"alert_level": "critical", "include_suppliers": true}', '2024-02-01 11:45:00'),
('financial', 'Quarterly Financial Summary - Q1 2024', '2024-01-01', '2024-03-31', 'excel', '/reports/financial_q1_2024.xlsx', 1, '{"include_expenses": true, "tax_details": true}', '2024-04-05 08:15:00'),
('sales', 'Daily Sales Breakdown', '2024-01-15', '2024-01-15', 'csv', '/reports/daily_sales_20240115.csv', 2, '{"hourly_breakdown": true, "payment_methods": true}', '2024-01-16 09:30:00'),
('products', 'Product Performance by Category', '2024-01-01', '2024-01-31', 'pdf', '/reports/category_performance_january.pdf', 1, '{"compare_previous_month": true, "include_margins": true}', '2024-02-01 16:20:00'),
('customers', 'Customer Lifetime Value Report', '2023-01-01', '2024-01-31', 'excel', '/reports/customer_lifetime_value.xlsx', 1, '{"min_orders": 2, "include_demographics": true}', '2024-02-05 13:10:00');

-- Update product counts in categories
UPDATE categories SET product_count = (SELECT COUNT(*) FROM products WHERE category_id = categories.id);

-- Update product ratings based on reviews
UPDATE products p SET 
    rating = (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id AND status = 'approved'),
    review_count = (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id AND status = 'approved')
WHERE id IN (SELECT DISTINCT product_id FROM product_reviews);