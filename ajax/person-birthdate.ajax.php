<?php
$data = new People($_POST['contact']);
$date = explode('/', $_POST['date']);
krsort($date);
$date = implode('-', $date);
$data->update('date_naissance', $date);
