<?php
$rues = Maps::street_search($_GET['rue']);
echo json_encode($rues);
