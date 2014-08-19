<?php

/*
 *	Script de vérification de la présence de fiches similaires à celle proposé à l'ajout dans la base de données
 *	
 *	En cas de fiches similaires trouvées, cela propose :
 *	
 *		1. les fiches en question pour accéder à la fiche, en ajoutant les coordonnées entrées dans le formulaire précédent à la fiche choisie
 *		
 *		2. la possibilité de continuer le processus de création d'une nouvelle fiche par l'ajout d'une adresse
 *
 */


 	// On récupère les informations envoyées par le formulaire
	 	$infos = array(	'nom'			=> $_POST['nom'],
	 					'nom-usage'		=> $_POST['nom-usage'],
	 					'prenom'			=> $_POST['prenom'],
	 					'sexe'			=> $_POST['sexe'],
	 					'fixe'			=> $core->securisation_string($_POST['fixe']),
	 					'mobile'			=> $core->securisation_string($_POST['mobile']),
	 					'email'			=> $core->securisation_string($_POST['email']),
	 					'dateNaissance' => $_POST['dateNaissance']);
	 
	// On formate la date de naissance
 		$date = explode('/', $infos['dateNaissance']);
 		krsort($date);
 		$date = implode('-', $date);
 
	// On formate correctement certaines données
		$infos['mobile'] = preg_replace('`[^0-9]`', '', $infos['mobile']);
		$infos['fixe'] = preg_replace('`[^0-9]`', '', $infos['fixe']);

 	// On va procéder à la recherche de fiches similaires
 		$contacts = $fiche->recherche($infos['prenom'], $infos['nom'], $infos['nom-usage'], $infos['sexe']);
?>

<h3>Sélectionnez une fiche à mettre à jour ou validez la création</h3>

<section class="liste" id="selectionDoublons" data-nom="<?php echo $infos['nom']; ?>" data-nomUsage="<?php echo $infos['nom-usage']; ?>" data-prenom="<?php echo $infos['prenom']; ?>" data-sexe="<?php echo $infos['sexe']; ?>" data-naissance="<?php echo $date; ?>" data-fixe="<?php echo $infos['fixe']; ?>" data-mobile="<?php echo $infos['mobile']; ?>" data-email="<?php echo $infos['email']; ?>">
	<article class="fiche" id="nouvelleFiche">
		<header>
			<h3><span>Ajouter une<br> nouvelle fiche</span></h3>
		</header>
	</article>
	
	<?php foreach ($contacts as $contact) : $fiche->acces($contact['id'], true); ?>
	
	<article class="fiche existante" data-contact="<?php $fiche->the_ID(); ?>">
		<header>
			<h3><?php $fiche->affichage_nom('span'); ?></h3>
		</header>
		<ul>
			<li><?php $fiche->date_naissance(' / '); ?> – <?php $fiche->age(); ?></li>
			<?php if ($fiche->get_immeuble()) : ?><li><?php $carto->afficherVille($carto->villeParImmeuble($fiche->get_immeuble())); ?></li><?php endif; ?>
		</ul>
	</article>
	
	<?php endforeach; ?>
</section>