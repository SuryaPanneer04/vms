<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userid = $_POST['userid'] ??  $_GET['userid'];
    $handle = $_POST['handle'] ??  $_GET['handle'];

    $url = "https://auth.odyssey-demo.com:7173/Demo/user";

    // 🔐 Your token
    $token = "eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBsaWNhdGlvbiI6IkRlbW8iLCJhZG1pbiI6InNlbGYiLCJ2YWxpZEZyb20iOjE3NzczNjczMzgsInZhbGlkVG8iOjE3Nzc0NTM2Nzh9.sfjt9D1x4TUJKZxMcgpCYa--80Dt7PY32uHoqMv9VPl60uPJrwrRxfhlh7_SKRkPUoGKGlIbZYOhCbwIMzqjww";

    $data = [
        "userid" => $userid,
        "handle" => $handle
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: $token"
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // ⚠️ Optional (for SSL issues in localhost)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        echo "<h3>API Response:</h3>";
        echo "<pre>";
        print_r(json_decode($response, true));
        echo "</pre>";
    }

    curl_close($ch);
}
