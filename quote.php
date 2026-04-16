<?php
// Security and validation
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$subject = 'New Towing Quote Request - Washington State';
$to = 'towing@logixpress.com';
$from = 'no-reply@logixpress.com'; // Fixed domain sender

// Collect and sanitize Customer Information
$customer_name    = sanitizeInput($_POST['customer_name'] ?? '');
$customer_email   = sanitizeInput($_POST['customer_email'] ?? '');
$customer_phone   = sanitizeInput($_POST['customer_phone'] ?? '');
$service_type     = sanitizeInput($_POST['service_type'] ?? '');

// Collect and sanitize Vehicle Information
$vehicle_year          = sanitizeInput($_POST['vehicle_year'] ?? '');
$vehicle_make          = sanitizeInput($_POST['vehicle_make'] ?? '');
$vehicle_model         = sanitizeInput($_POST['vehicle_model'] ?? '');
$vehicle_type          = sanitizeInput($_POST['vehicle_type'] ?? '');
$vehicle_condition     = sanitizeInput($_POST['vehicle_condition'] ?? '');
$license_plate         = sanitizeInput($_POST['license_plate'] ?? 'Not provided');

// Collect and sanitize Location Information
$pickup_address        = sanitizeInput($_POST['pickup_address'] ?? '');
$dropoff_address       = sanitizeInput($_POST['dropoff_address'] ?? '');
$service_time          = sanitizeInput($_POST['service_time'] ?? '');
$vehicle_accessibility = sanitizeInput($_POST['vehicle_accessibility'] ?? 'Not specified');
$estimated_distance    = sanitizeInput($_POST['estimated_distance'] ?? '');

// Quote Information
$estimated_price       = sanitizeInput($_POST['estimated_price'] ?? '0.00');

// Additional Information
$additional_notes      = sanitizeInput($_POST['additional_notes'] ?? 'None provided');
$agree_terms           = isset($_POST['agree_terms']) ? 'Yes' : 'No';
$submission_time       = date('Y-m-d H:i:s');

// Basic validation
if (empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($service_type)) {
    echo 'failed';
    exit;
}

if (!validateEmail($customer_email)) {
    echo 'failed';
    exit;
}

// Build Email with proper headers
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
$headers .= "From: LogiXpress Towing <".$from.">\r\n";
$headers .= "Reply-To: ".$customer_name." <".$customer_email.">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Format message
$message  = "=== TOWING QUOTE REQUEST ===\n";
$message .= "Submission Date: $submission_time\n";
$message .= "Service Area: Washington State\n\n";

$message .= "=== CUSTOMER INFORMATION ===\n";
$message .= "Name: $customer_name\n";
$message .= "Email: $customer_email\n";
$message .= "Phone: $customer_phone\n";
$message .= "Service Requested: $service_type\n\n";

$message .= "=== VEHICLE INFORMATION ===\n";
$message .= "Year: $vehicle_year\n";
$message .= "Make: $vehicle_make\n";
$message .= "Model: $vehicle_model\n";
$message .= "Type: $vehicle_type\n";
$message .= "Condition: $vehicle_condition\n";
$message .= "License Plate: $license_plate\n\n";

$message .= "=== LOCATION DETAILS ===\n";
$message .= "Pickup Location: $pickup_address\n";
$message .= "Drop-off Location: $dropoff_address\n";
$message .= "Estimated Distance: $estimated_distance miles\n";
$message .= "Service Urgency: $service_time\n";
$message .= "Vehicle Accessibility: $vehicle_accessibility\n\n";

$message .= "=== PRICE ESTIMATE ===\n";
$message .= "Estimated Starting Price: \$$estimated_price\n";
$message .= "Quote Type: Initial Estimate Only\n";
$message .= "Note: Final price must be confirmed manually by dispatcher\n\n";

$message .= "=== ADDITIONAL INFORMATION ===\n";
$message .= "Special Notes: $additional_notes\n";
$message .= "Terms Accepted: $agree_terms\n\n";

$message .= "=== QUOTE REQUIREMENTS ===\n";
$message .= "This is a towing service request for Washington State.\n";
$message .= "Please contact the customer promptly with pricing and availability.\n";
$message .= "Emergency requests should be prioritized.\n\n";

$message .= "=== SYSTEM INFO ===\n";
$message .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
$message .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";

// Send email
if (@mail($to, $subject, $message, $headers)) {
    echo 'sent';
} else {
    echo 'failed';
}
?>
