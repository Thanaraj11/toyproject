<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get all pages
function getAllPages($conn, $status = null) {
    $query = "SELECT * FROM pages";
    
    if ($status) {
        $query .= " WHERE status = '$status'";
    }
    
    $query .= " ORDER BY updated_at DESC";
    
    $result = mysqli_query($conn, $query);
    $pages = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $pages[] = $row;
    }
    
    return $pages;
}

// Function to get page by ID
function getPageById($conn, $id) {
    $query = "SELECT * FROM pages WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to get page by slug
function getPageBySlug($conn, $slug) {
    $slug = mysqli_real_escape_string($conn, $slug);
    $query = "SELECT * FROM pages WHERE slug = '$slug' AND status = 'published'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to add new page
function addPage($conn, $data) {
    $title = mysqli_real_escape_string($conn, $data['title']);
    $slug = mysqli_real_escape_string($conn, $data['slug']);
    $content = mysqli_real_escape_string($conn, $data['content']);
    $meta_description = mysqli_real_escape_string($conn, $data['meta_description']);
    $template = mysqli_real_escape_string($conn, $data['template']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    $published_at = $status == 'published' ? 'NOW()' : 'NULL';
    
    $query = "INSERT INTO pages (title, slug, content, meta_description, template, status, published_at) 
              VALUES ('$title', '$slug', '$content', '$meta_description', '$template', '$status', $published_at)";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function to update page
function updatePage($conn, $id, $data) {
    $title = mysqli_real_escape_string($conn, $data['title']);
    $slug = mysqli_real_escape_string($conn, $data['slug']);
    $content = mysqli_real_escape_string($conn, $data['content']);
    $meta_description = mysqli_real_escape_string($conn, $data['meta_description']);
    $template = mysqli_real_escape_string($conn, $data['template']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    // If status changed to published and wasn't published before, set published_at
    $published_at_sql = "";
    if ($status == 'published') {
        $current_page = getPageById($conn, $id);
        if ($current_page && $current_page['status'] != 'published') {
            $published_at_sql = ", published_at = NOW()";
        }
    }
    
    $query = "UPDATE pages 
              SET title = '$title', 
                  slug = '$slug', 
                  content = '$content', 
                  meta_description = '$meta_description', 
                  template = '$template', 
                  status = '$status',
                  updated_at = CURRENT_TIMESTAMP
                  $published_at_sql
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Function to delete page
function deletePage($conn, $id) {
    $query = "DELETE FROM pages WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function to get all banners
function getAllBanners($conn, $status = null) {
    $query = "SELECT * FROM banners";
    
    if ($status) {
        $query .= " WHERE status = '$status'";
    }
    
    $query .= " ORDER BY sort_order ASC, created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $banners = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $banners[] = $row;
    }
    
    return $banners;
}

// Function to get banner by ID
function getBannerById($conn, $id) {
    $query = "SELECT * FROM banners WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to add new banner
function addBanner($conn, $data) {
    $title = mysqli_real_escape_string($conn, $data['title']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $image_url = mysqli_real_escape_string($conn, $data['image_url']);
    $target_url = mysqli_real_escape_string($conn, $data['target_url']);
    $position = mysqli_real_escape_string($conn, $data['position']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $start_date = !empty($data['start_date']) ? "'" . mysqli_real_escape_string($conn, $data['start_date']) . "'" : 'NULL';
    $end_date = !empty($data['end_date']) ? "'" . mysqli_real_escape_string($conn, $data['end_date']) . "'" : 'NULL';
    $button_text = mysqli_real_escape_string($conn, $data['button_text']);
    $button_color = mysqli_real_escape_string($conn, $data['button_color']);
    
    $query = "INSERT INTO banners (title, description, image_url, target_url, position, status, start_date, end_date, button_text, button_color) 
              VALUES ('$title', '$description', '$image_url', '$target_url', '$position', '$status', $start_date, $end_date, '$button_text', '$button_color')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function to update banner
function updateBanner($conn, $id, $data) {
    $title = mysqli_real_escape_string($conn, $data['title']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $image_url = mysqli_real_escape_string($conn, $data['image_url']);
    $target_url = mysqli_real_escape_string($conn, $data['target_url']);
    $position = mysqli_real_escape_string($conn, $data['position']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $start_date = !empty($data['start_date']) ? "'" . mysqli_real_escape_string($conn, $data['start_date']) . "'" : 'NULL';
    $end_date = !empty($data['end_date']) ? "'" . mysqli_real_escape_string($conn, $data['end_date']) . "'" : 'NULL';
    $button_text = mysqli_real_escape_string($conn, $data['button_text']);
    $button_color = mysqli_real_escape_string($conn, $data['button_color']);
    
    $query = "UPDATE banners 
              SET title = '$title', 
                  description = '$description', 
                  image_url = '$image_url', 
                  target_url = '$target_url', 
                  position = '$position', 
                  status = '$status', 
                  start_date = $start_date, 
                  end_date = $end_date,
                  button_text = '$button_text',
                  button_color = '$button_color',
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Function to delete banner
function deleteBanner($conn, $id) {
    $query = "DELETE FROM banners WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function to update banner status
function updateBannerStatus($conn, $id, $status) {
    $status = mysqli_real_escape_string($conn, $status);
    $query = "UPDATE banners SET status = '$status', updated_at = CURRENT_TIMESTAMP WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function to get active banners for frontend
function getActiveBanners($conn, $position = null) {
    $query = "SELECT * FROM banners 
              WHERE status = 'active' 
              AND (start_date IS NULL OR start_date <= NOW()) 
              AND (end_date IS NULL OR end_date >= NOW())";
    
    if ($position) {
        $query .= " AND position = '$position'";
    }
    
    $query .= " ORDER BY sort_order ASC";
    
    $result = mysqli_query($conn, $query);
    $banners = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $banners[] = $row;
    }
    
    return $banners;
}
?>