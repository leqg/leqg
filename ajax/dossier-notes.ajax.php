<?php
$dossier = new Folder($_POST['dossier']);
$dossier->update('dossier_notes', $_POST['notes']);
