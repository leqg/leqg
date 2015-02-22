<?php
$name = (isset($_GET['nom'])) ? $_GET['nom'] : '';
$desc = (isset($_GET['desc'])) ? $_GET['desc'] : '';
$event = (isset($_GET['event'])) ? $_GET['event'] : 0;

$dossier = Folder::create($name, $desc);
$dossier = new Folder($dossier);
$event = new Event($event);
$event->link_folder($dossier->get('id'));

echo $dossier->json();
