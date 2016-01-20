<?php
    // Protection de l'accès
    User::protection(5);
    
    // On récupère le lien BDD
    $link = Configuration::read('db.link');
    
    // On regarde combien de résultats correspondent
    $recherche = '%' . preg_replace('#[^[:alnum:]]#u', '%', $_POST['recherche']) . '%';
    
    // On effectue la recherche
    $query = $link->prepare('SELECT `contact_id` FROM `contacts` WHERE CONCAT_WS(" ", `contact_prenoms`, `contact_nom`, `contact_nom_usage`, `contact_nom`, `contact_prenoms`) LIKE :recherche ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
    $query->bindParam(':recherche', $recherche);
    $query->execute();
    
    // On regarde s'il n'y a qu'une réponse pour rediriger tout de suite
if ($query->rowCount() == 1) {
    // On récupère l'ID de la fiche
    $contact = $query->fetch(PDO::FETCH_NUM);
        
    // On redirige selon la destination demandée
    if (isset($_GET['destimation'])) {
        if ($_GET['destination'] == 'interaction') {
            Core::goPage('interaction', array('action' => 'ajout', 'fiche' => $contact[0]), true);
        } else {
            Core::goPage('contacts', array('fiche' => $contact[0]), true);
        }
    } else {
        Core::goPage('contacts', array('fiche' => $contact[0]), true);
    }
}
    
    $contacts = $query->fetchAll(PDO::FETCH_NUM);
    
    Core::loadHeader();
?>

<h2>Résultats</h2>

<ul class="listeEncadree">
	
    <?php foreach ($contacts as $c) : $contact = new Contact(md5($c[0])); ?>
	<li class="electeur">
    <?php if (isset($_GET['destination']) && $_GET['destination'] == 'interaction') : ?>
		<a href="<?php Core::goPage('interaction', array('action' => 'ajout', 'fiche' => $contact->get('contact_id'))); ?>" class="nostyle">
    <?php else: ?> 
		<a href="<?php Core::goPage('contacts', array('fiche' => $contact->get('contact_id'))); ?>" class="nostyle">
    <?php endif; ?>
			<strong><?php echo $contact->get('nom_affichage'); ?></strong>
		</a>
	</li>
    <?php endforeach; ?>

</ul>

<?php Core::loadFooter(); ?>