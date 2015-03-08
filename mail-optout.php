<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('
    UPDATE      `coordonnees`
    SET         `optout` = 1
    WHERE       MD5(`coordonnee_email`) = :email
');
$query->bindValue(':email', $_GET['email']);
$query->execute();
?>

Votre désinscription des newsletters a bien été enregistré.