<?php
$cities = Maps::city_search($_GET['ville']);
echo json_encode($cities);
