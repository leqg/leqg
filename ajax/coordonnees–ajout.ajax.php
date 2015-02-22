<?php
$data = new People($_POST['contact']);
$data->contact_details_add($_POST['coordonnees']);
