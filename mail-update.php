<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('
    SELECT      `contact_id` AS `id`
    FROM        `coordonnees`
    WHERE       MD5(`coordonnee_email`) = :email
    LIMIT       0, 1
');
$query->bindValue(':email', $_GET['email']);
$query->execute();
$infos = $query->fetch(PDO::FETCH_ASSOC);
Core::debug($infos);
$person = new People($infos['id']);
$person->update('nom', $_POST['nom']);
$person->update('prenoms', $_POST['prenom']);

$query = $link->prepare('
    UPDATE      `coordonnees`
    SET         `coordonnee_email` = :email
    WHERE       MD5(`coordonnee_email`) = :id
');
$query->bindValue(':email', $_POST['newemail']);
$query->bindValue(':id', $_GET['email']);
$query->execute();

header('Location: http://cordery.leqg.info/mail-info.php?email='.md5($_POST['newemail']));
