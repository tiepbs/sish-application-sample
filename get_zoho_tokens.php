<?php
// Thông tin ứng dụng
$client_id = 'CLIENT_ID';
$client_secret = 'CLIENT_SECRET';
$redirect_uri = 'REDIRECT_URI'; // e.g: https://tbzoho.tunnel.plchi.dev/get_zoho_tokens.php
$scope = '[SCOPES]'; //e.g: 'ZohoSubscriptions.products.CREATE,ZohoSubscriptions.subscriptions.UPDATE,ZohoSubscriptions.settings.READ,ZohoSubscriptions.addons.CREATE,ZohoSubscriptions.subscriptions.READ,ZohoSubscriptions.subscriptions.CREATE,ZohoCRM.modules.ALL,ZohoSubscriptions.customers.CREATE,ZohoSubscriptions.customers.READ,ZohoSubscriptions.hostedpages.READ,ZohoSubscriptions.plans.READ,ZohoSubscriptions.hostedpages.CREATE,ZohoCRM.modules.ALL,ZohoCRM.org.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.ALL'
 
// Bước 3: Xử lý callback từ Zoho sau khi người dùng cấp quyền
if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];
 
    // Bước 4: Gửi yêu cầu để trao đổi mã truy cập lấy access token và refresh token
    $token_url = "https://accounts.zoho.com/oauth/v2/token";
    $token_data = array(
        'code' => $authorization_code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    );
 
    $token_options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($token_data),
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        )
    );
 
    $token_context = stream_context_create($token_options);
    $token_response = file_get_contents($token_url, false, $token_context);
 
    if ($token_response === false) {
        die('Error occurred while fetching tokens.');
    }
 
    $token_data = json_decode($token_response, true);
 
    if (isset($token_data['access_token']) && isset($token_data['refresh_token'])) {
        $access_token = $token_data['access_token'];
        $refresh_token = $token_data['refresh_token'];
 
        // In ra access token và refresh token
        echo "Access Token: $access_token<br>";
        echo "Refresh Token: $refresh_token<br>";
    } else {
        die('Invalid token response.');
    }
 
} else {
    // Bước 1: Xây dựng URL để chuyển hướng người dùng đến Zoho
    $authorize_url = "https://accounts.zoho.com/oauth/v2/auth"
        . "?client_id={$client_id}"
        . "&scope={$scope}"
        . "&response_type=code"
        . "&access_type=offline"
        . "&redirect_uri={$redirect_uri}";
 
    // Chuyển hướng người dùng đến trang đăng nhập Zoho
    header("Location: {$authorize_url}");
    exit;
}
?>
