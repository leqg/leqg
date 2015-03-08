<?php
$headers = getallheaders();
$request = file_get_contents('php://input');

// check send secret
if ($headers['X-Hub-Signature'] == 'sha1=' . hash_hmac('sha1', $request, 'YoshiLoveLeQG')) {
    $output = shell_exec('git pull');
    echo $output;
    http_response_code(200);
} else {
    http_response_code(403);
}