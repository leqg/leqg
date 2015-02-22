<?php
	// On protège la page
	User::protection(5);
	
	// On commence par vérifier qu'une recherche a été lancée
	if (!isset($_POST['recherche']) || empty($_POST['recherche'])) Core::tpl_go_to('contacts', true);
	
	// On récupère cette recherche
	$terme = $_POST['recherche'];
	
	// On effectue la recherche
	$resultats = People::search($terme);

	// On regarde le nombre de réponses trouvées
	$nombre = count($resultats);

	// S'il n'y a qu'une réponse, on redirige directement vers la réponse
	if ($nombre == 1) {
		$reponse = $resultats[0];
		Core::tpl_go_to('contact', array('contact' => $reponse['id']), true);
	}
	
	// Quand tout est bon, on affiche le template
	Core::tpl_header();
?>
<h2>Résultats de la recherche &laquo;&nbsp;<?php echo $terme; ?>&nbsp;&raquo;</h2>

<?php if ($nombre == 0) : ?>

	<section class="icone" id="aucunResultat">
		<h3>Il n'existe aucun résultat à votre recherche</h3>
		<a class="nostyle" href="<?php $core->tpl_go_to('contacts'); ?>"><button>Revenir au module Contacts</button></a>
	</section>
	
<?php else : ?>
	
	<section class="contenu">
		<ul class="listeContacts">
			<?php foreach ($resultats as $resultat) : $contact = new People($resultat['id']); ?><!--
		 --><a class="nostyle" href="<?php Core::tpl_go_to('contact', array('contact' => $contact->get('id'))); ?>"><!--
			 --><li class="demi contact <?php if ($contact->get('sexe') == 'H') { echo 'homme'; } elseif ($contact->get('contact_sexe') == 'F') { echo 'femme'; } else { echo 'isexe'; } ?>">
					<strong><?php echo $contact->get('nom_complet'); ?></strong>
					<p><?php echo $contact->display_age(); ?> – <?php echo $contact->city(); ?></p>
				</li><!--
		 --></a><!--
		 --><?php endforeach; ?>
		</ul>
	</section>
	
<?php endif; ?>


<?php Core::tpl_footer(); ?>