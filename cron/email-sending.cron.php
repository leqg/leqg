<?php
$query = Core::query('tracking-to-send');
$query->execute();

if ($query->rowCount()) {
    $emails = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($emails as $email) {
        Campaign::sending($email['campaign'], $email['email']);
    }
}

Core::debug($emails);
