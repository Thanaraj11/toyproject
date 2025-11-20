<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'category_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        // Add new category
        $data = array(
            'name' => $_POST['name'],
            'slug' => $_POST['slug'],
            'description' => $_POST['description'],
            'icon' => $_POST['icon'],
            'color' => $_POST['color'],
            'parent_id' => $_POST['parent_id'],
            'status' => $_POST['status']
        );
        
        if (addCategory($conn, $data)) {
            $message = 'Category added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error adding category. Please try again.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['update_category'])) {
        // Update category
        $id = intval($_POST['category_id']);
        $data = array(
            'name' => $_POST['name'],
            'slug' => $_POST['slug'],
            'description' => $_POST['description'],
            'icon' => $_POST['icon'],
            'color' => $_POST['color'],
            'parent_id' => $_POST['parent_id'],
            'status' => $_POST['status']
        );
        
        if (updateCategory($conn, $id, $data)) {
            $message = 'Category updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating category. Please try again.';
            $message_type = 'error';
        }
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if (deleteCategory($conn, $delete_id)) {
        $message = 'Category deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting category. Please try again.';
        $message_type = 'error';
    }
}

// Get categories for display
$categories = getAllCategories($conn);

// Get parent categories for dropdown
$parent_categories = getParentCategories($conn);

// Handle search
$search_results = array();
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_results = searchCategories($conn, $search_term);
    $categories = $search_results; // Replace main categories with search results
}

// Get category for editing if edit_id is set
$edit_category = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_category = getCategoryById($conn, $edit_id);
}

// Close database connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="../back.css">
   <link rel="stylesheet" href="../back1.css">
</head>
<body>
     
    <?php include '../header2.php' ?>
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Category Management</h1>
                <p>Organize your products into categories</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search categories..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab <?php echo !isset($_GET['add']) && !isset($_GET['edit_id']) ? 'active' : ''; ?>" onclick="showTab('category-list')">Categories List</div>
            <div class="tab <?php echo isset($_GET['add']) ? 'active' : ''; ?>" onclick="showTab('add-category')">Add New Category</div>
            <?php if (isset($_GET['edit_id'])): ?>
                <div class="tab active" id="edit-tab">Edit Category</div>
            <?php endif; ?>
        </div>

        <!-- Category List -->
        <div id="category-list" class="tab-content <?php echo !isset($_GET['add']) && !isset($_GET['edit_id']) ? 'active' : ''; ?>">
            <div class="category-list">
                <div class="category-list-header">
                    <h2 class="category-list-title">All Categories</h2>
                    <a href="?add=true" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Category
                    </a>
                </div>
                
                <?php if (empty($categories)): ?>
                    <div class="no-data">
                        <p>No categories found.</p>
                    </div>
                <?php else: ?>
                    <table class="category-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div class="category-icon <?php echo htmlspecialchars($category['name']); ?>" style="background-color: <?php echo htmlspecialchars($category['color']); ?>;">
                                                <i class="fas fa-<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                            </div>
                                            <div>
                                                <div><?php echo htmlspecialchars($category['name']); ?></div>
                                                <div style="font-size: 0.875rem; color: #6c757d;"><?php echo htmlspecialchars($category['description']); ?></div>
                                                <?php if ($category['parent_name']): ?>
                                                    <div style="font-size: 0.75rem; color: #8e9aaf;">Parent: <?php echo htmlspecialchars($category['parent_name']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $category['product_count']; ?></td>
                                    <td>
                                        <span class="status status-<?php echo $category['status']; ?>">
                                            <?php echo ucfirst($category['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $category['id']; ?>" class="action-btn action-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete_id=<?php echo $category['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this category?')">
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
                        <div class="page-item"><i class="fas fa-chevron-right"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Category Form -->
        <div id="add-category" class="tab-content <?php echo isset($_GET['add']) ? 'active' : ''; ?>" style="<?php echo isset($_GET['add']) ? '' : 'display: none;'; ?>">
            <div class="form-container">
                <h2 class="form-title">Add New Category</h2>
                <form method="POST" action="">
                    <input type="hidden" name="add_category" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="categoryName">Category Name</label>
                            <input type="text" id="categoryName" name="name" class="form-control" placeholder="Enter category name" required>
                        </div>
                        <div class="form-group">
                            <label for="categorySlug">Slug</label>
                            <input type="text" id="categorySlug" name="slug" class="form-control" placeholder="URL-friendly identifier" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea id="categoryDescription" name="description" class="form-control" placeholder="Enter category description" required></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Icon</label>
                            <div class="icon-selector">
                                <?php
                                $icons = array('laptop', 'tshirt', 'home', 'book', 'spa', 'mobile-alt', 'mitten', 'couch');
                                foreach ($icons as $icon): ?>
                                    <div class="icon-option <?php echo $icon == 'laptop' ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="categoryIcon" name="icon" value="laptop">
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <div class="color-selector">
                                <?php
                                $colors = array('#4361ee', '#f72585', '#4cc9f0', '#f8961e', '#4895ef', '#3a0ca3', '#7209b7', '#f77f00');
                                foreach ($colors as $color): ?>
                                    <div class="color-option <?php echo $color == '#4361ee' ? 'selected' : ''; ?>" style="background-color: <?php echo $color; ?>" data-color="<?php echo $color; ?>"></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="categoryColor" name="color" value="#4361ee">
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="parentCategory">Parent Category</label>
                            <select id="parentCategory" name="parent_id" class="form-control">
                                <option value="">None</option>
                                <?php foreach ($parent_categories as $parent): ?>
                                    <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="categoryStatus">Status</label>
                            <select id="categoryStatus" name="status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="category.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Form -->
        <?php if ($edit_category): ?>
        <div id="edit-category" class="tab-content active">
            <div class="form-container">
                <h2 class="form-title">Edit Category</h2>
                <form method="POST" action="">
                    <input type="hidden" name="update_category" value="1">
                    <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editCategoryName">Category Name</label>
                            <input type="text" id="editCategoryName" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_category['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editCategorySlug">Slug</label>
                            <input type="text" id="editCategorySlug" name="slug" class="form-control" value="<?php echo htmlspecialchars($edit_category['slug']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editCategoryDescription">Description</label>
                        <textarea id="editCategoryDescription" name="description" class="form-control" required><?php echo htmlspecialchars($edit_category['description']); ?></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Icon</label>
                            <div class="icon-selector">
                                <?php
                                $icons = array('laptop', 'tshirt', 'home', 'book', 'spa', 'mobile-alt', 'mitten', 'couch');
                                foreach ($icons as $icon): ?>
                                    <div class="icon-option <?php echo $icon == $edit_category['icon'] ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="editCategoryIcon" name="icon" value="<?php echo htmlspecialchars($edit_category['icon']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <div class="color-selector">
                                <?php
                                $colors = array('#4361ee', '#f72585', '#4cc9f0', '#f8961e', '#4895ef', '#3a0ca3', '#7209b7', '#f77f00');
                                foreach ($colors as $color): ?>
                                    <div class="color-option <?php echo $color == $edit_category['color'] ? 'selected' : ''; ?>" style="background-color: <?php echo $color; ?>" data-color="<?php echo $color; ?>"></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="editCategoryColor" name="color" value="<?php echo htmlspecialchars($edit_category['color']); ?>">
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editParentCategory">Parent Category</label>
                            <select id="editParentCategory" name="parent_id" class="form-control">
                                <option value="">None</option>
                                <?php foreach ($parent_categories as $parent): ?>
                                    <option value="<?php echo $parent['id']; ?>" <?php echo $edit_category['parent_id'] == $parent['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parent['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editCategoryStatus">Status</label>
                            <select id="editCategoryStatus" name="status" class="form-control" required>
                                <option value="active" <?php echo $edit_category['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $edit_category['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="category.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Category</button>
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
        }
        
        // Icon selection
        document.querySelectorAll('.icon-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('categoryIcon').value = this.getAttribute('data-icon');
                if (document.getElementById('editCategoryIcon')) {
                    document.getElementById('editCategoryIcon').value = this.getAttribute('data-icon');
                }
            });
        });
        
        // Color selection
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('categoryColor').value = this.getAttribute('data-color');
                if (document.getElementById('editCategoryColor')) {
                    document.getElementById('editCategoryColor').value = this.getAttribute('data-color');
                }
            });
        });
        
        // Auto-generate slug from category name
        document.getElementById('categoryName').addEventListener('input', function() {
            const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            document.getElementById('categorySlug').value = slug;
        });
    </script>
</body>
</html>