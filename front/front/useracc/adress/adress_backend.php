<?php
// Include database connection
include '../../../databse/db_connection.php';

// session_start();

// Set default user ID (in a real application, this would come from login session)
$customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

function getAddresses($customer_id, $type = null) {
    global $conn;
    $addresses = [];
    
    $sql = "SELECT * FROM addresses WHERE customer_id = $customer_id";
    if ($type) {
        $type = mysqli_real_escape_string($conn, $type);
        $sql .= " AND type = '$type'";
    }
    $sql .= " ORDER BY is_default DESC, id DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $addresses[] = $row;
        }
    }
    
    return $addresses;
}

function getAddress($address_id, $customer_id) {
    global $conn;
    
    $sql = "SELECT * FROM addresses WHERE id = $address_id AND customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

function addAddress($address_data, $customer_id) {
    global $conn;
    
    $type = mysqli_real_escape_string($conn, $address_data['type'] ?? 'shipping');
    $full_name = mysqli_real_escape_string($conn, $address_data['full_name']);
    $phone = mysqli_real_escape_string($conn, $address_data['phone']);
    $address_line1 = mysqli_real_escape_string($conn, $address_data['address_line1']);
    $address_line2 = mysqli_real_escape_string($conn, $address_data['address_line2'] ?? '');
    $city = mysqli_real_escape_string($conn, $address_data['city']);
    $state = mysqli_real_escape_string($conn, $address_data['state']);
    $zip_code = mysqli_real_escape_string($conn, $address_data['zip_code']);
    $country = mysqli_real_escape_string($conn, $address_data['country']);
    $is_default = isset($address_data['is_default']) ? 1 : 0;
    
    // If this is set as default, remove default from other addresses of same type
    if ($is_default) {
        $update_sql = "UPDATE addresses SET is_default = 0 WHERE customer_id = $customer_id AND type = '$type'";
        mysqli_query($conn, $update_sql);
    }
    
    $sql = "INSERT INTO addresses (customer_id, type, full_name, phone, address_line1, address_line2, city, state, zip_code, country, is_default) 
            VALUES ($customer_id, '$type', '$full_name', '$phone', '$address_line1', '$address_line2', '$city', '$state', '$zip_code', '$country', $is_default)";
    
    return mysqli_query($conn, $sql);
}

function updateAddress($address_id, $address_data, $customer_id) {
    global $conn;
    
    $type = mysqli_real_escape_string($conn, $address_data['type'] ?? 'shipping');
    $full_name = mysqli_real_escape_string($conn, $address_data['full_name']);
    $phone = mysqli_real_escape_string($conn, $address_data['phone']);
    $address_line1 = mysqli_real_escape_string($conn, $address_data['address_line1']);
    $address_line2 = mysqli_real_escape_string($conn, $address_data['address_line2'] ?? '');
    $city = mysqli_real_escape_string($conn, $address_data['city']);
    $state = mysqli_real_escape_string($conn, $address_data['state']);
    $zip_code = mysqli_real_escape_string($conn, $address_data['zip_code']);
    $country = mysqli_real_escape_string($conn, $address_data['country']);
    $is_default = isset($address_data['is_default']) ? 1 : 0;
    
    // If this is set as default, remove default from other addresses of same type
    if ($is_default) {
        $update_sql = "UPDATE addresses SET is_default = 0 WHERE customer_id = $customer_id AND type = '$type' AND id != $address_id";
        mysqli_query($conn, $update_sql);
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
            is_default = $is_default
            WHERE id = $address_id AND customer_id = $customer_id";
    
    return mysqli_query($conn, $sql);
}

function deleteAddress($address_id, $customer_id) {
    global $conn;
    
    $sql = "DELETE FROM addresses WHERE id = $address_id AND customer_id = $customer_id";
    return mysqli_query($conn, $sql);
}

function setDefaultAddress($address_id, $customer_id) {
    global $conn;
    
    // First get the address type
    $address = getAddress($address_id, $customer_id);
    if (!$address) return false;
    
    $type = $address['type'];
    
    // Remove default from all addresses of the same type
    $update_sql = "UPDATE addresses SET is_default = 0 WHERE customer_id = $customer_id AND type = '$type'";
    mysqli_query($conn, $update_sql);
    
    // Then set the new default
    $sql = "UPDATE addresses SET is_default = 1 WHERE id = $address_id AND customer_id = $customer_id";
    return mysqli_query($conn, $sql);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_address':
                if (addAddress($_POST, $customer_id)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                    exit();
                } else {
                    $error_message = "Error adding address.";
                }
                break;
                
            case 'update_address':
                if (isset($_POST['id']) && updateAddress($_POST['id'], $_POST, $customer_id)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                    exit();
                } else {
                    $error_message = "Error updating address.";
                }
                break;
                
            case 'delete_address':
                if (isset($_POST['id']) && deleteAddress($_POST['id'], $customer_id)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                    exit();
                } else {
                    $error_message = "Error deleting address.";
                }
                break;
                
            case 'set_default':
                if (isset($_POST['id']) && setDefaultAddress($_POST['id'], $customer_id)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                    exit();
                } else {
                    $error_message = "Error setting default address.";
                }
                break;
        }
    }
}

// Get addresses for display
$shipping_addresses = getAddresses($customer_id, 'shipping');
$billing_addresses = getAddresses($customer_id, 'billing');
$all_addresses = getAddresses($customer_id);
?>