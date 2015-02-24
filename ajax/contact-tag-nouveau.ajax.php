<?php
$data = new People($_POST['contact']);
if (!empty($_POST['tag'])) {
    $data->tag_add($_POST['tag']);
}
