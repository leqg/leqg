<?php
$polls = Maps::poll_search($_GET['bureau']);
echo json_encode($polls);
