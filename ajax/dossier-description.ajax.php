<?php
$dossier = new Folder($_POST['dossier']);
$dossier->update('dossier_description', $_POST['description']);
