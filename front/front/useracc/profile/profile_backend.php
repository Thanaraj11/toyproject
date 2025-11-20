<?php
require_once '../../../databse/db_connection.php';

/**
 * Get user profile data
 */
function getUserProfile($user_id) {
    global $conn;
    
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "SELECT id, first_name, last_name, email, phone, avatar_color, status, created_at, last_login 
            FROM customers 
            WHERE id = '$user_id' AND status = 'active'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Get user addresses
 */
function getUserAddresses($user_id) {
    global $conn;
    
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "SELECT id, type, full_name, phone, address_line1, address_line2, city, state, zip_code, country, is_default, created_at
            FROM addresses 
            WHERE customer_id = '$user_id' 
            ORDER BY is_default DESC, type ASC, created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $addresses = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $addresses[] = $row;
        }
    }
    
    return $addresses;
}

/**
 * Get user orders
 */
function getUserOrders($user_id) {
    global $conn;
    
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "SELECT id, order_number, total_amount, status, created_at 
            FROM orders 
            WHERE customer_id = '$user_id' 
            ORDER BY created_at DESC 
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    
    $orders = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

/**
 * Update user profile
 */
function updateUserProfile($user_id, $data) {
    global $conn;
    
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $first_name = mysqli_real_escape_string($conn, $data['first_name']);
    $last_name = mysqli_real_escape_string($conn, $data['last_name']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    
    $sql = "UPDATE customers 
            SET first_name = '$first_name', 
                last_name = '$last_name', 
                phone = '$phone',
                updated_at = NOW() 
            WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['phone'] = $phone;
        return true;
    }
    
    return false;
}

/**
 * Change user password
 */
function changePassword($user_id, $current_password, $new_password) {
    global $conn;
    
    // Verify current password
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "SELECT password_hash FROM customers WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($current_password, $user['password_hash'])) {
            // Update to new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE customers SET password_hash = '$new_password_hash' WHERE id = '$user_id'";
            
            return mysqli_query($conn, $update_sql);
        }
    }
    
    return false;
}

/**
 * Add new address
 */
function addAddress($user_id, $address_data) {
    global $conn;
    
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $type = mysqli_real_escape_string($conn, $address_data['type']);
    $full_name = mysqli_real_escape_string($conn, $address_data['full_name']);
    $phone = mysqli_real_escape_string($conn, $address_data['phone']);
    $address_line1 = mysqli_real_escape_string($conn, $address_data['address_line1']);
    $address_line2 = mysqli_real_escape_string($conn, $address_data['address_line2'] ?? '');
    $city = mysqli_real_escape_string($conn, $address_data['city']);
    $state = mysqli_real_escape_string($conn, $address_data['state']);
    $zip_code = mysqli_real_escape_string($conn, $address_data['zip_code']);
    $country = mysqli_real_escape_string($conn, $address_data['country']);
    $is_default = isset($address_data['is_default']) ? 1 : 0;
    
    // If this is set as default, remove default status from other addresses of same type
    if ($is_default) {
        $reset_sql = "UPDATE addresses SET is_default = 0 
                      WHERE customer_id = '$user_id' AND type = '$type'";
        mysqli_query($conn, $reset_sql);
    }
    
    $sql = "INSERT INTO addresses (
                customer_id, type, full_name, phone, address_line1, address_line2, 
                city, state, zip_code, country, is_default
            ) VALUES (
                '$user_id', '$type', '$full_name', '$phone', '$address_line1', '$address_line2',
                '$city', '$state', '$zip_code', '$country', '$is_default'
            )";
    
    return mysqli_query($conn, $sql);
}

/**
 * Update address
 */
function updateAddress($address_id, $user_id, $address_data) {
    global $conn;
    
    $address_id = mysqli_real_escape_string($conn, $address_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $type = mysqli_real_escape_string($conn, $address_data['type']);
    $full_name = mysqli_real_escape_string($conn, $address_data['full_name']);
    $phone = mysqli_real_escape_string($conn, $address_data['phone']);
    $address_line1 = mysqli_real_escape_string($conn, $address_data['address_line1']);
    $address_line2 = mysqli_real_escape_string($conn, $address_data['address_line2'] ?? '');
    $city = mysqli_real_escape_string($conn, $address_data['city']);
    $state = mysqli_real_escape_string($conn, $address_data['state']);
    $zip_code = mysqli_real_escape_string($conn, $address_data['zip_code']);
    $country = mysqli_real_escape_string($conn, $address_data['country']);
    $is_default = isset($address_data['is_default']) ? 1 : 0;
    
    // If this is set as default, remove default status from other addresses of same type
    if ($is_default) {
        $reset_sql = "UPDATE addresses SET is_default = 0 
                      WHERE customer_id = '$user_id' AND type = '$type' AND id != '$address_id'";
        mysqli_query($conn, $reset_sql);
    }
    
    $sql = "UPDATE addresses SET
                type = '$type',
                full_name = '$full_name',
                phone = '$phone',
                address_line1 = '$address_line1',
                address_line2 = '$address_line2',
                city = '$city',
                state = '$state',
                zip_code = '$zip_code',
                country = '$country',
                is_default = '$is_default',
                updated_at = NOW()
            WHERE id = '$address_id' AND customer_id = '$user_id'";
    
    return mysqli_query($conn, $sql);
}

/**
 * Set address as default
 */
function setDefaultAddress($address_id, $user_id) {
    global $conn;
    
    $address_id = mysqli_real_escape_string($conn, $address_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    
    // First get the address type
    $type_sql = "SELECT type FROM addresses WHERE id = '$address_id' AND customer_id = '$user_id'";
    $type_result = mysqli_query($conn, $type_sql);
    
    if ($type_result && mysqli_num_rows($type_result) > 0) {
        $address = mysqli_fetch_assoc($type_result);
        $type = $address['type'];
        
        // Remove default status from other addresses of same type
        $reset_sql = "UPDATE addresses SET is_default = 0 
                      WHERE customer_id = '$user_id' AND type = '$type'";
        mysqli_query($conn, $reset_sql);
        
        // Set this address as default
        $update_sql = "UPDATE addresses SET is_default = 1 
                       WHERE id = '$address_id' AND customer_id = '$user_id'";
        
        return mysqli_query($conn, $update_sql);
    }
    
    return false;
}

/**
 * Delete address
 */
function deleteAddress($address_id, $user_id) {
    global $conn;
    
    $address_id = mysqli_real_escape_string($conn, $address_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    
    $sql = "DELETE FROM addresses WHERE id = '$address_id' AND customer_id = '$user_id'";
    return mysqli_query($conn, $sql);
}

/**
 * Get address by ID
 */
function getAddressById($address_id, $user_id) {
    global $conn;
    
    $address_id = mysqli_real_escape_string($conn, $address_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    
    $sql = "SELECT * FROM addresses WHERE id = '$address_id' AND customer_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}
?>