<?php
require_once('includes.php');

$link = Configuration::read('db.link');

$query = $link->prepare('
    SELECT      `coordonnees`.`coordonnee_email` AS `email`,
                `people`.`nom` AS `nom`,
                `people`.`prenoms` AS `prenoms`
    FROM        `coordonnees`
    LEFT JOIN   `people`
    ON          `people`.`id` = `coordonnees`.`contact_id`
    WHERE       MD5(`coordonnee_email`) = :email
    LIMIT       0, 1
');
$query->bindValue(':email', $_GET['email']);
$query->execute();
$infos = $query->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf8">
    <title>Newsletter Philip Cordery</title>
</head>
<body>
    <h1>Newsletter Philip Cordery</h1>
    
    <?php if (isset($_GET['action']) && $_GET['action'] == 'inscription') : ?>
    <div class="alerte">
        Inscription réussie à la lettre d'information
    </div>
    <?php endif; ?>
    
    <form action="mail-update.php?email=<?php echo $_GET['email']; ?>" method="post">
        <ul class="formulaire">
            <li>
                <label for="nom">Nom</label>
                <input type="text" name="nom" value="<?php echo $infos['nom']; ?>">
            </li>
            <li>
                <label for="nom">Prénom</label>
                <input type="text" name="prenom" value="<?php echo $infos['prenoms']; ?>">
            </li>
            <li>
                <label for="nom">Email</label>
                <input type="text" name="email" value="<?php echo $infos['email']; ?>">
            </li>
            <li>
                <input type="submit" value="Valider">
            </li>
        </ul>
    </form>
</body>
</html>