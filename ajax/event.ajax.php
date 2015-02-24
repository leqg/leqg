<?php
if (is_numeric($_GET['evenement'])) {
    $data = new Event($_GET['evenement']);
    $json = $data->json();
    $json = utf8_encode($json);
    echo $json;
} else {
    echo '';
}
