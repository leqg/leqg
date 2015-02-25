<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$person = People::create();
$person = new People($person);
$person->tag_add('newsletter');
$person->contact_details_add($_POST['email']);

header('Location: http://cordery.leqg.info/mail-info.php?email='.md5($_POST['email']));