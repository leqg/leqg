<?php
$data = new Event($_GET['evenement']);
$json = $data->json();
$json = utf8_encode($json);
echo $json;
