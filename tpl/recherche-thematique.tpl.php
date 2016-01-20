<?php
User::protection(5);

if (!isset($_POST['rechercheThematique']) || empty($_POST['rechercheThematique'])) { Core::goTo('dossiers', true); 
}
$results = People::search_tags($_POST['rechercheThematique']);

if (count($results) == 1) {
    Core::goTo('contact', array('contact' => $results[0]), true);
}

Core::loadHeader();
?>
<h2>Résultats de la recherche thématique &laquo;&nbsp;<?php echo $_POST['rechercheThematique']; ?>&nbsp;&raquo;</h2>

<?php if (count($results) == 0) : ?>

	<section class="icone" id="aucunResultat">
		<h3>Il n'existe aucun résultat à votre recherche</h3>
		<a class="nostyle" href="<?php $core->goTo('contacts'); ?>"><button>Revenir au module Contacts</button></a>
	</section>

<?php else : ?>

	<section class="contenu">
		<ul class="listeContacts">
    <?php foreach ($results as $element) : $contact = new People($element[0]); ?><!--
		 --><a class="nostyle" href="<?php Core::goTo('contact', array('contact' => $contact->get('id'))); ?>"><!--
			 --><li class="demi contact <?php if ($contact->get('sexe') == 'H') { echo 'homme'; 
   } elseif ($contact->get('sexe') == 'F') { echo 'femme'; 
} else { echo 'isexe'; 
} ?>">
					<strong><?php echo $contact->display_name(); ?></strong>
					<p><?php echo $contact->display_age(); ?> – <?php echo $contact->city(); ?></p>
				</li><!--
		 --></a><!--
--><?php endforeach; ?>
		</ul>
	</section>
	
<?php endif; ?>


<?php Core::loadFooter(); ?>