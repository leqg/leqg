<?php

	// On récupère les informations contenues dans le $_POST
	$infos = array('nom' => $core->securisation_string($_POST['nom']),
				   'nom-usage' => $core->securisation_string($_POST['nomUsage']),
				   'prenoms' => $core->securisation_string($_POST['prenom']),
				   'sexe' => $_POST['sexe'],
				   'telephone' => $_POST['fixe'],
				   'mobile' => $_POST['mobile'],
				   'email' => $_POST['email'],
				   'ville' => $_POST['ville'],
				   'rue' => $_POST['rue'],
				   'immeuble' => $_POST['immeuble']);
				   
	// On ajoute les informations additionnelles demandées
	$infos['bureau'] = $carto->bureauParImmeuble($infos['immeuble']);
	$infos['canton'] = $carto->cantonParImmeuble($infos['immeuble']);
	$infos['adresse_ville'] = $carto->afficherVille($infos['ville'], true);
	$infos['adresse_cp'] = '67000';
	$infos['adresse_rue'] = $carto->afficherRue($infos['rue'], true);
	$infos['adresse_numero'] = $carto->afficherImmeuble($infos['immeuble'], true);
	
	// On formate correctement certaines données
	$infos['mobile'] = preg_replace('`[^0-9]`', '', $infos['mobile']);
	$infos['telephone'] = preg_replace('`[^0-9]`', '', $infos['telephone']);
	
	if ($infos['telephone'] == '0000000000') $infos['telephone'] = null;
	if ($infos['mobile'] == '0000000000') $infos['mobile'] = null;
	if ($infos['email'] == '') $infos['email'] = null;
	
	// On commence par créer la fiche
	$contact = $fiche->creerContact($infos);

	// On affiche le numéro ID pour la redirection AJAX
	echo $contact;
?>