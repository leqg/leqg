<?php
require_once 'includes.php';

$query = Core::query('campaign-data-md5');
$query->bindValue(':campaign', $_SERVER['QUERY_STRING']);
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);

echo $data['mail'];
