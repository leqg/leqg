<?php
$data = new People($_POST['contact']);
$data->tag_remove($_POST['tag']);
