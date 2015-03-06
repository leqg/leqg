<?php
require_once 'includes.php';

$query = Core::query('campaign-data-md5');
$query->bindValue(':campaign', $_SERVER['QUERY_STRING']);
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $data['objet']; ?></title>
    </head>
    <body style="padding: 0; margin: 0;">
        <?php echo $data['mail']; ?>
    </body>
</html>
