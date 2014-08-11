<?php

	// on récupère les informations envoyées
	$donnees = array('nom' => $_POST['nom'],
					 'nomUsage' => $_POST['nomUsage'],
					 'prenom' => $_POST['prenom'],
					 'sexe' => $_POST['sexe'],
					 'fixe' => $_POST['fixe'],
					 'mobile' => $_POST['mobile'],
					 'email' => $_POST['email'],
					 'ville' => $_POST['ville']);
	
	$infos = $donnees;
?>
<section id="fiche-electeur" data-ville="<?php echo $infos['ville']; ?>" data-nom="<?php echo $infos['nom']; ?>" data-nomUsage="<?php echo $infos['nom-usage']; ?>" data-prenom="<?php echo $infos['prenom']; ?>" data-sexe="<?php echo $infos['sexe']; ?>" data-fixe="<?php echo $infos['fixe']; ?>" data-mobile="<?php echo $infos['mobile']; ?>" data-email="<?php echo $infos['email']; ?>">
	<header>
		<h2 class="titre"><span class="nom"><?php echo $donnees['nom']; ?></span><span class="nomUsage"><?php echo $donnees['nomUsage']; ?></span><span><?php echo $donnees['prenom']; ?></span></h2>
	</header>
	
	<ul class="ficheInteraction adresse">
		<li id="ville">
			<label>Ville</label>
			<p><?php echo $carto->afficherVille($donnees['ville']); ?></p>
		</li>
		<li id="recherche-rue">
			<label for="form-recherche-rue">Rue</label>
			<input type="text" name="recherche-rue" id="form-recherche-rue">
		</li>
		<li id="resultat-rue">
			<label>Confirmez</label>
			<ul class="nostyle"></ul>
		</li>
	</ul>
</section>