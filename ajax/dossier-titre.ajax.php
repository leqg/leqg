<?php
$dossier = new Folder($_POST['dossier']);
$dossier->update('dossier_nom', $_POST['titre']);
