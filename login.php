<?php
session_start();
include('./database.php');
$db = new db();

if (isset($_POST['google_id'])) {
    $google_id = $_POST['google_id'];
    $email = $_POST['email'];
    $name = $_POST['name'];

    $login = $db->select("users", "*", "WHERE google_id = '$google_id'");
    if ($login->num_rows > 0) {
        $fetch = $login->fetch_object();
        $_SESSION['userid'] = $fetch->id_user;
    } else {
        $db->insert("users", [
            "username_user" => $name,
            "email_user" => $email,
            "google_id" => $google_id
        ]);
        $_SESSION['userid'] = $db->getInsertID();
    }
    header("location: ./index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสั่งจองอาหารออนไลน์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
    
        body {
            background-color: #f8f9fa;
            background-image:url('./img/1.jpg');
            background-size: cover;  
            background-position: center; 
            background-repeat: no-repeat; 
            height: 100vh; 
            margin: 0;
            backdrop-filter: blur(10px);    
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .brand-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .g_id_signin {
            margin-top: 10px;
        }
    </style>
    <script>
        function handleCredentialResponse(response) {
            const responsePayload = decodeJwt(response.credential);
            document.getElementById("google_id").value = responsePayload.sub;
            document.getElementById("email").value = responsePayload.email;
            document.getElementById("name").value = responsePayload.name;
            document.getElementById("google-login-form").submit();
        }

        function decodeJwt(token) {
            let base64Url = token.split('.')[1];
            let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            let jsonPayload = decodeURIComponent(atob(base64).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join(''));
            return JSON.parse(jsonPayload);
        }
    </script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-5">
            <div class="card p-4 text-center">
                <h3 class="brand-title">Bagista</h3>
                <p class="text-muted">เข้าสู่ระบบด้วยบัญชี Google ของคุณ</p>
                <div id="g_id_onload"
                    data-client_id="335823475136-p88s4fcsgmkdgdffetpv3g6pjt8ek5qr.apps.googleusercontent.com"
                    data-callback="handleCredentialResponse">
                </div>
                <div class="g_id_signin " align='center' data-type="standard"></div>
                <form id="google-login-form" method="post">
                    <input type="hidden" id="google_id" name="google_id">
                    <input type="hidden" id="email" name="email">
                    <input type="hidden" id="name" name="name">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
