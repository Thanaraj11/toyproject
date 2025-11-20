<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'productmanage_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'add-product';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $data = array(
            'name' => $_POST['name'],
            'sku' => $_POST['sku'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category_id'],
            'supplier_id' => $_POST['supplier_id'],
            'price' => $_POST['price'],
            'cost_price' => $_POST['cost_price'],
            'current_stock' => $_POST['current_stock'],
            'min_stock_level' => $_POST['min_stock_level'],
            'max_stock_level' => $_POST['max_stock_level'],
            'image_url' => '',
            'status' => $_POST['status']
        );
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = uploadProductImage($_FILES['image']);
            if ($upload_result['success']) {
                $data['image_url'] = $upload_result['file_url'];
            } else {
                $message = 'Error uploading image: ' . $upload_result['error'];
                $message_type = 'error';
            }
        }
        
        if (empty($message)) {
            if (addProduct($conn, $data)) {
                $message = 'Product added successfully!';
                $message_type = 'success';
                $current_tab = 'product-list';
            } else {
                $message = 'Error adding product. Please try again.';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['update_product'])) {
        // Update product
        $product_id = intval($_POST['product_id']);
        $data = array(
            'name' => $_POST['name'],
            'sku' => $_POST['sku'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category_id'],
            'supplier_id' => $_POST['supplier_id'],
            'price' => $_POST['price'],
            'cost_price' => $_POST['cost_price'],
            'current_stock' => $_POST['current_stock'],
            'min_stock_level' => $_POST['min_stock_level'],
            'max_stock_level' => $_POST['max_stock_level'],
            'image_url' => $_POST['current_image_url'],
            'status' => $_POST['status']
        );
        
        // Handle image upload if new image provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = uploadProductImage($_FILES['image']);
            if ($upload_result['success']) {
                $data['image_url'] = $upload_result['file_url'];
            } else {
                $message = 'Error uploading image: ' . $upload_result['error'];
                $message_type = 'error';
            }
        }
        
        if (empty($message)) {
            if (updateProduct($conn, $product_id, $data)) {
                $message = 'Product updated successfully!';
                $message_type = 'success';
                $current_tab = 'product-list';
            } else {
                $message = 'Error updating product. Please try again.';
                $message_type = 'error';
            }
        }
    }
}

// Handle delete requests
if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    if (deleteProduct($conn, $product_id)) {
        $message = 'Product deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting product. Please try again.';
        $message_type = 'error';
    }
}

// Apply filters for product list
$filters = array();
if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $filters['category'] = $_GET['category'];
}
if (isset($_GET['status']) && $_GET['status'] != 'all') {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['stock_status']) && $_GET['stock_status'] != 'all') {
    $filters['stock_status'] = $_GET['stock_status'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get data for display
$products = getAllProducts($conn, $filters);
$categories = getCategories($conn);
$suppliers = getSuppliers($conn);
$product_stats = getProductStats($conn);

// Get product for editing
$edit_product = null;
if (isset($_GET['edit_product'])) {
    $edit_product_id = intval($_GET['edit_product']);
    $edit_product = getProductById($conn, $edit_product_id);
    $current_tab = 'edit-product';
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../back.css">
    <link rel="stylesheet" href="../back1.css">
</head>
<body>
    <?php include '../header2.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Product Management</h1>
                <p>Manage your products and inventory</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>
                <div class="user-profile">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <h4>Admin User</h4>
                    </div>
                </div>
            </div>
        </header>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $product_stats['total_products']; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $product_stats['active_products']; ?></h3>
                    <p>Active Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon low-stock">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $product_stats['low_stock']; ?></h3>
                    <p>Low Stock</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon out-of-stock">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $product_stats['out_of_stock']; ?></h3>
                    <p>Out of Stock</p>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab <?php echo $current_tab == 'add-product' ? 'active' : ''; ?>" onclick="showTab('add-product')">Add New Product</div>
            <div class="tab <?php echo $current_tab == 'product-list' ? 'active' : ''; ?>" onclick="showTab('product-list')">Product List</div>
            <?php if ($edit_product): ?>
                <div class="tab active" id="edit-product-tab">Edit Product</div>
            <?php endif; ?>
        </div>

        <!-- Add New Product Form -->
        <div id="add-product" class="tab-content <?php echo $current_tab == 'add-product' ? 'active' : ''; ?>">
            <div class="form-container">
                <h2 class="form-title">Add New Product</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="add_product" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" id="productName" name="name" class="form-control" placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="productSKU">SKU</label>
                            <input type="text" id="productSKU" name="sku" class="form-control" placeholder="Enter product SKU" required>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Price ($)</label>
                            <input type="number" id="productPrice" name="price" class="form-control" placeholder="Enter price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="productCost">Cost ($)</label>
                            <input type="number" id="productCost" name="cost_price" class="form-control" placeholder="Enter cost" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="productStock">Stock Quantity</label>
                            <input type="number" id="productStock" name="current_stock" class="form-control" placeholder="Enter stock quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="minStockLevel">Min Stock Level</label>
                            <input type="number" id="minStockLevel" name="min_stock_level" class="form-control" value="5" required>
                        </div>
                        <div class="form-group">
                            <label for="maxStockLevel">Max Stock Level</label>
                            <input type="number" id="maxStockLevel" name="max_stock_level" class="form-control" value="50" required>
                        </div>
                        <div class="form-group">
                            <label for="productCategory">Category</label>
                            <select id="productCategory" name="category_id" class="form-control" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="productSupplier">Supplier</label>
                            <select id="productSupplier" name="supplier_id" class="form-control" required>
                                <option value="">Select supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Description</label>
                        <textarea id="productDescription" name="description" class="form-control" placeholder="Enter product description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="productImage">Product Image</label>
                        <input type="file" id="productImage" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="productStatus">Status</label>
                        <select id="productStatus" name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product List -->
        <div id="product-list" class="tab-content <?php echo $current_tab == 'product-list' ? 'active' : ''; ?>" style="<?php echo $current_tab == 'product-list' ? '' : 'display: none;'; ?>">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" action="" class="filter-form">
                    <input type="hidden" name="tab" value="product-list">
                    <div class="filter-group">
                        <label for="category-filter">Category:</label>
                        <select id="category-filter" name="category" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['category']) ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['slug']); ?>" 
                                        <?php echo isset($filters['category']) && $filters['category'] == $category['slug'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['status']) ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="active" <?php echo isset($filters['status']) && $filters['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo isset($filters['status']) && $filters['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="stock-filter">Stock Status:</label>
                        <select id="stock-filter" name="stock_status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['stock_status']) ? 'selected' : ''; ?>>All Stock</option>
                            <option value="in-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'in-stock' ? 'selected' : ''; ?>>In Stock</option>
                            <option value="out-of-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'out-of-stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            <option value="low-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'low-stock' ? 'selected' : ''; ?>>Low Stock</option>
                        </select>
                    </div>
                    <?php if (isset($_GET['category']) || isset($_GET['status']) || isset($_GET['stock_status']) || isset($_GET['search'])): ?>
                        <div class="filter-group">
                            <a href="?tab=product-list" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="product-list">
                <div class="product-list-header">
                    <h2 class="product-list-title">Product List (<?php echo count($products); ?>)</h2>
                    <button class="btn btn-primary" onclick="showTab('add-product')">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="no-data">
                        <p>No products found.</p>
                    </div>
                <?php else: ?>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): 
                                $stock_status = getStockStatus($product['current_stock'], $product['min_stock_level']);
                                $stock_status_display = formatStockStatus($product['current_stock'], $product['min_stock_level']);
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             class="product-image" alt="Product" 
                                             onerror="this.src='https://via.placeholder.com/50'">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['current_stock']; ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>
                                        <?php if ($product['status'] == 'active'): ?>
                                            <span class="status status-<?php echo $stock_status; ?>">
                                                <?php echo $stock_status_display; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="status status-inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?edit_product=<?php echo $product['id']; ?>" class="action-btn action-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete_product=<?php echo $product['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination">
                        <div class="page-item"><i class="fas fa-chevron-left"></i></div>
                        <div class="page-item active">1</div>
                        <div class="page-item">2</div>
                        <div class="page-item">3</div>
                        <div class="page-item">4</div>
                        <div class="page-item"><i class="fas fa-chevron-right"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Product Form -->
        <?php if ($edit_product): ?>
        <div id="edit-product" class="tab-content active">
            <div class="form-container">
                <h2 class="form-title">Edit Product</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="update_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($edit_product['image_url']); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editProductName">Product Name</label>
                            <input type="text" id="editProductName" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_product['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductSKU">SKU</label>
                            <input type="text" id="editProductSKU" name="sku" class="form-control" value="<?php echo htmlspecialchars($edit_product['sku']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductPrice">Price ($)</label>
                            <input type="number" id="editProductPrice" name="price" class="form-control" value="<?php echo $edit_product['price']; ?>" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductCost">Cost ($)</label>
                            <input type="number" id="editProductCost" name="cost_price" class="form-control" value="<?php echo $edit_product['cost_price']; ?>" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductStock">Stock Quantity</label>
                            <input type="number" id="editProductStock" name="current_stock" class="form-control" value="<?php echo $edit_product['current_stock']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editMinStockLevel">Min Stock Level</label>
                            <input type="number" id="editMinStockLevel" name="min_stock_level" class="form-control" value="<?php echo $edit_product['min_stock_level']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editMaxStockLevel">Max Stock Level</label>
                            <input type="number" id="editMaxStockLevel" name="max_stock_level" class="form-control" value="<?php echo $edit_product['max_stock_level']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductCategory">Category</label>
                            <select id="editProductCategory" name="category_id" class="form-control" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $edit_product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editProductSupplier">Supplier</label>
                            <select id="editProductSupplier" name="supplier_id" class="form-control" required>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>" <?php echo $edit_product['supplier_id'] == $supplier['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editProductDescription">Description</label>
                        <textarea id="editProductDescription" name="description" class="form-control" required><?php echo htmlspecialchars($edit_product['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editProductImage">Product Image</label>
                        <?php if ($edit_product['image_url']): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="<?php echo htmlspecialchars($edit_product['image_url']); ?>" alt="Current Product Image" style="max-width: 100px; max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="editProductImage" name="image" class="form-control" accept="image/*">
                        <small>Leave empty to keep current image</small>
                    </div>
                    <div class="form-group">
                        <label for="editProductStatus">Status</label>
                        <select id="editProductStatus" name="status" class="form-control" required>
                            <option value="active" <?php echo $edit_product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $edit_product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <a href="?tab=product-list" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Tab navigation
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).style.display = 'block';
            document.getElementById(tabId).classList.add('active');
            
            // Update active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        }
        
        // Auto-submit filter form when selections change
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelects = document.querySelectorAll('.filter-select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.form) {
                        this.form.submit();
                    }
                });
            });
            
            // Set active tab based on URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                showTab(tabParam);
            }
        });
    </script>
</body>
</html>