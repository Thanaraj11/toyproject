<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'content_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'page-editor';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_page'])) {
        // Save page (draft)
        $data = array(
            'title' => $_POST['title'],
            'slug' => $_POST['slug'],
            'content' => $_POST['content'],
            'meta_description' => $_POST['meta_description'],
            'template' => $_POST['template'],
            'status' => 'draft'
        );
        
        if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
            // Update existing page
            $page_id = intval($_POST['page_id']);
            if (updatePage($conn, $page_id, $data)) {
                $message = 'Page saved as draft successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error saving page. Please try again.';
                $message_type = 'error';
            }
        } else {
            // Create new page
            if (addPage($conn, $data)) {
                $message = 'Page created as draft successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error creating page. Please try again.';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['publish_page'])) {
        // Publish page
        $data = array(
            'title' => $_POST['title'],
            'slug' => $_POST['slug'],
            'content' => $_POST['content'],
            'meta_description' => $_POST['meta_description'],
            'template' => $_POST['template'],
            'status' => 'published'
        );
        
        if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
            // Update existing page
            $page_id = intval($_POST['page_id']);
            if (updatePage($conn, $page_id, $data)) {
                $message = 'Page published successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error publishing page. Please try again.';
                $message_type = 'error';
            }
        } else {
            // Create new page
            if (addPage($conn, $data)) {
                $message = 'Page published successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error publishing page. Please try again.';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['save_banner'])) {
        // Save banner
        $data = array(
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image_url' => $_POST['image_url'],
            'target_url' => $_POST['target_url'],
            'position' => $_POST['position'],
            'status' => $_POST['status'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'button_text' => $_POST['button_text'],
            'button_color' => $_POST['button_color']
        );
        
        if (isset($_POST['banner_id']) && !empty($_POST['banner_id'])) {
            // Update existing banner
            $banner_id = intval($_POST['banner_id']);
            if (updateBanner($conn, $banner_id, $data)) {
                $message = 'Banner updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating banner. Please try again.';
                $message_type = 'error';
            }
        } else {
            // Create new banner
            if (addBanner($conn, $data)) {
                $message = 'Banner created successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error creating banner. Please try again.';
                $message_type = 'error';
            }
        }
    }
}

// Handle delete requests
if (isset($_GET['delete_page'])) {
    $page_id = intval($_GET['delete_page']);
    if (deletePage($conn, $page_id)) {
        $message = 'Page deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting page. Please try again.';
        $message_type = 'error';
    }
}

if (isset($_GET['delete_banner'])) {
    $banner_id = intval($_GET['delete_banner']);
    if (deleteBanner($conn, $banner_id)) {
        $message = 'Banner deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting banner. Please try again.';
        $message_type = 'error';
    }
}

// Handle banner status toggle
if (isset($_GET['toggle_banner'])) {
    $banner_id = intval($_GET['toggle_banner']);
    $banner = getBannerById($conn, $banner_id);
    if ($banner) {
        $new_status = $banner['status'] == 'active' ? 'inactive' : 'active';
        if (updateBannerStatus($conn, $banner_id, $new_status)) {
            $message = 'Banner status updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating banner status. Please try again.';
            $message_type = 'error';
        }
    }
}

// Get data for display
$pages = getAllPages($conn);
$banners = getAllBanners($conn);

// Get page for editing if edit_page is set
$edit_page = null;
if (isset($_GET['edit_page'])) {
    $edit_page_id = intval($_GET['edit_page']);
    $edit_page = getPageById($conn, $edit_page_id);
    $current_tab = 'page-editor';
}

// Get banner for editing if edit_banner is set
$edit_banner = null;
if (isset($_GET['edit_banner'])) {
    $edit_banner_id = intval($_GET['edit_banner']);
    $edit_banner = getBannerById($conn, $edit_banner_id);
    $current_tab = 'banner-management';
}

// Close database connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Admin Panel</title>
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
                <h1>Content Management</h1>
                <p>Manage pages and promotional banners</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search content...">
                </div>
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
            <div class="tab <?php echo $current_tab == 'page-editor' ? 'active' : ''; ?>" onclick="showTab('page-editor')">Page Editor</div>
            <div class="tab <?php echo $current_tab == 'banner-management' ? 'active' : ''; ?>" onclick="showTab('banner-management')">Banner Management</div>
        </div>

        <!-- Page Editor -->
        <div id="page-editor" class="tab-content <?php echo $current_tab == 'page-editor' ? 'active' : ''; ?>">
            <div class="editor-container">
                <div class="editor-header">
                    <h2 class="editor-title"><?php echo $edit_page ? 'Edit Page' : 'Create New Page'; ?></h2>
                    <div class="editor-actions">
                        <button class="btn btn-secondary" onclick="previewPage()"><i class="fas fa-eye"></i> Preview</button>
                        <button type="submit" form="pageEditorForm" name="save_page" value="1" class="btn btn-success"><i class="fas fa-save"></i> Save Draft</button>
                        <button type="submit" form="pageEditorForm" name="publish_page" value="1" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Publish</button>
                    </div>
                </div>

                <form id="pageEditorForm" method="POST" action="">
                    <?php if ($edit_page): ?>
                        <input type="hidden" name="page_id" value="<?php echo $edit_page['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pageTitle">Page Title</label>
                            <input type="text" id="pageTitle" name="title" class="form-control" 
                                   value="<?php echo $edit_page ? htmlspecialchars($edit_page['title']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pageSlug">URL Slug</label>
                            <input type="text" id="pageSlug" name="slug" class="form-control" 
                                   value="<?php echo $edit_page ? htmlspecialchars($edit_page['slug']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pageTemplate">Template</label>
                            <select id="pageTemplate" name="template" class="form-control" required>
                                <option value="default" <?php echo ($edit_page && $edit_page['template'] == 'default') ? 'selected' : ''; ?>>Default Template</option>
                                <option value="homepage" <?php echo ($edit_page && $edit_page['template'] == 'homepage') ? 'selected' : ''; ?>>Homepage</option>
                                <option value="contact" <?php echo ($edit_page && $edit_page['template'] == 'contact') ? 'selected' : ''; ?>>Contact Page</option>
                                <option value="fullwidth" <?php echo ($edit_page && $edit_page['template'] == 'fullwidth') ? 'selected' : ''; ?>>Full Width</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pageStatus">Status</label>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="pageStatus" <?php echo ($edit_page && $edit_page['status'] == 'published') ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span id="pageStatusText"><?php echo ($edit_page && $edit_page['status'] == 'published') ? 'Published' : 'Draft'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="metaDescription">Meta Description</label>
                        <textarea id="metaDescription" name="meta_description" class="form-control" rows="3" 
                                  placeholder="Enter meta description for SEO"><?php echo $edit_page ? htmlspecialchars($edit_page['meta_description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="pageContent">Page Content</label>
                        <div class="wysiwyg-toolbar">
                            <button type="button" class="toolbar-btn" data-command="bold"><i class="fas fa-bold"></i></button>
                            <button type="button" class="toolbar-btn" data-command="italic"><i class="fas fa-italic"></i></button>
                            <button type="button" class="toolbar-btn" data-command="underline"><i class="fas fa-underline"></i></button>
                            <div style="width: 1px; background: #ddd; margin: 0 0.5rem;"></div>
                            <button type="button" class="toolbar-btn" data-command="justifyLeft"><i class="fas fa-align-left"></i></button>
                            <button type="button" class="toolbar-btn" data-command="justifyCenter"><i class="fas fa-align-center"></i></button>
                            <button type="button" class="toolbar-btn" data-command="justifyRight"><i class="fas fa-align-right"></i></button>
                            <div style="width: 1px; background: #ddd; margin: 0 0.5rem;"></div>
                            <button type="button" class="toolbar-btn" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
                            <button type="button" class="toolbar-btn" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
                            <div style="width: 1px; background: #ddd; margin: 0 0.5rem;"></div>
                            <button type="button" class="toolbar-btn" data-command="createLink"><i class="fas fa-link"></i></button>
                            <button type="button" class="toolbar-btn" data-command="unlink"><i class="fas fa-unlink"></i></button>
                            <button type="button" class="toolbar-btn" data-command="insertImage"><i class="fas fa-image"></i></button>
                        </div>
                        <textarea id="pageContent" name="content" class="wysiwyg-editor" style="display: none;"><?php echo $edit_page ? htmlspecialchars($edit_page['content']) : ''; ?></textarea>
                        <div id="pageContentEditor" class="wysiwyg-editor" contenteditable="true">
                            <?php echo $edit_page ? $edit_page['content'] : '<h2>Our Story</h2><p>Founded in 2010, our company began as a small startup with a big vision...</p>'; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Banner Management -->
        <div id="banner-management" class="tab-content <?php echo $current_tab == 'banner-management' ? 'active' : ''; ?>" style="<?php echo $current_tab == 'banner-management' ? '' : 'display: none;'; ?>">
            <div class="banners-container">
                <div class="banners-header">
                    <h2 class="banners-title">Promotional Banners</h2>
                    <a href="?tab=banner-management&add_banner=true" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Banner
                    </a>
                </div>

                <?php if (isset($_GET['add_banner']) || $edit_banner): ?>
                    <!-- Banner Form -->
                    <div class="form-container">
                        <h2 class="form-title"><?php echo $edit_banner ? 'Edit Banner' : 'Add New Banner'; ?></h2>
                        <form method="POST" action="">
                            <input type="hidden" name="save_banner" value="1">
                            <?php if ($edit_banner): ?>
                                <input type="hidden" name="banner_id" value="<?php echo $edit_banner['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bannerTitle">Banner Title</label>
                                    <input type="text" id="bannerTitle" name="title" class="form-control" 
                                           value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['title']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="bannerPosition">Position</label>
                                    <select id="bannerPosition" name="position" class="form-control" required>
                                        <option value="homepage-top" <?php echo ($edit_banner && $edit_banner['position'] == 'homepage-top') ? 'selected' : ''; ?>>Homepage - Top</option>
                                        <option value="homepage-middle" <?php echo ($edit_banner && $edit_banner['position'] == 'homepage-middle') ? 'selected' : ''; ?>>Homepage - Middle</option>
                                        <option value="all-pages-top" <?php echo ($edit_banner && $edit_banner['position'] == 'all-pages-top') ? 'selected' : ''; ?>>All Pages - Top</option>
                                        <option value="sidebar" <?php echo ($edit_banner && $edit_banner['position'] == 'sidebar') ? 'selected' : ''; ?>>Sidebar</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="bannerDescription">Description</label>
                                <textarea id="bannerDescription" name="description" class="form-control" rows="3"><?php echo $edit_banner ? htmlspecialchars($edit_banner['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bannerImage">Image URL</label>
                                    <input type="url" id="bannerImage" name="image_url" class="form-control" 
                                           value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['image_url']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="bannerTarget">Target URL</label>
                                    <input type="url" id="bannerTarget" name="target_url" class="form-control" 
                                           value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['target_url']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bannerStatus">Status</label>
                                    <select id="bannerStatus" name="status" class="form-control" required>
                                        <option value="active" <?php echo ($edit_banner && $edit_banner['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($edit_banner && $edit_banner['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="scheduled" <?php echo ($edit_banner && $edit_banner['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bannerButtonText">Button Text</label>
                                    <input type="text" id="bannerButtonText" name="button_text" class="form-control" 
                                           value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['button_text']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bannerStartDate">Start Date</label>
                                    <input type="datetime-local" id="bannerStartDate" name="start_date" class="form-control" 
                                           value="<?php echo $edit_banner && $edit_banner['start_date'] ? date('Y-m-d\TH:i', strtotime($edit_banner['start_date'])) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="bannerEndDate">End Date</label>
                                    <input type="datetime-local" id="bannerEndDate" name="end_date" class="form-control" 
                                           value="<?php echo $edit_banner && $edit_banner['end_date'] ? date('Y-m-d\TH:i', strtotime($edit_banner['end_date'])) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <a href="?tab=banner-management" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary"><?php echo $edit_banner ? 'Update Banner' : 'Add Banner'; ?></button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Banners Grid -->
                    <?php if (empty($banners)): ?>
                        <div class="no-data">
                            <p>No banners found.</p>
                        </div>
                    <?php else: ?>
                        <div class="banners-grid">
                            <?php foreach ($banners as $banner): ?>
                                <div class="banner-card">
                                    <div class="banner-image" style="background-image: url('<?php echo htmlspecialchars($banner['image_url']); ?>');">
                                        <div class="banner-overlay">
                                            <div class="banner-actions">
                                                <a href="?tab=banner-management&edit_banner=<?php echo $banner['id']; ?>" class="action-btn action-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="action-btn action-preview" onclick="previewBanner('<?php echo htmlspecialchars($banner['image_url']); ?>')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="?tab=banner-management&delete_banner=<?php echo $banner['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this banner?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="banner-content">
                                        <h3 class="banner-title"><?php echo htmlspecialchars($banner['title']); ?></h3>
                                        <div class="banner-details">
                                            <span><?php echo htmlspecialchars($banner['position']); ?></span>
                                            <span class="status status-<?php echo $banner['status']; ?>"><?php echo ucfirst($banner['status']); ?></span>
                                        </div>
                                        <p><?php echo htmlspecialchars($banner['description']); ?></p>
                                        <div class="banner-footer">
                                            <small>
                                                <?php if ($banner['start_date']): ?>
                                                    <?php echo date('M j, Y', strtotime($banner['start_date'])); ?>
                                                <?php else: ?>
                                                    Created: <?php echo date('M j, Y', strtotime($banner['created_at'])); ?>
                                                <?php endif; ?>
                                            </small>
                                            <a href="?tab=banner-management&toggle_banner=<?php echo $banner['id']; ?>" class="toggle-switch-link">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" <?php echo $banner['status'] == 'active' ? 'checked' : ''; ?> onchange="this.parentNode.parentNode.click()">
                                                    <span class="slider"></span>
                                                </label>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
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
        
        // WYSIWYG Editor functionality
        document.querySelectorAll('.toolbar-btn').forEach(button => {
            button.addEventListener('click', function() {
                const command = this.getAttribute('data-command');
                document.getElementById('pageContentEditor').focus();
                
                if (command === 'createLink') {
                    const url = prompt('Enter URL:');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else if (command === 'insertImage') {
                    const url = prompt('Enter image URL:');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else {
                    document.execCommand(command, false, null);
                }
                
                // Update hidden textarea
                updatePageContent();
            });
        });
        
        // Update hidden textarea with editor content
        function updatePageContent() {
            document.getElementById('pageContent').value = document.getElementById('pageContentEditor').innerHTML;
        }
        
        // Auto-update on editor changes
        document.getElementById('pageContentEditor').addEventListener('input', updatePageContent);
        
        // Auto-generate slug from page title
        document.getElementById('pageTitle').addEventListener('input', function() {
            const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            document.getElementById('pageSlug').value = slug;
        });
        
        // Update status text based on toggle
        document.getElementById('pageStatus').addEventListener('change', function() {
            document.getElementById('pageStatusText').textContent = this.checked ? 'Published' : 'Draft';
        });
        
        // Preview functions
        function previewPage() {
            alert('Preview functionality would open the page in a new tab');
        }
        
        function previewBanner(imageUrl) {
            window.open(imageUrl, '_blank');
        }
        
        // Initialize page content on load
        document.addEventListener('DOMContentLoaded', function() {
            updatePageContent();
        });
    </script>
</body>
</html>