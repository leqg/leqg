<?php
	if (isset($_POST['fiche'], $_POST['rue'], $_POST['immeuble']))
	{
		// On créé le bordel
		$immeuble = $carto->ajoutImmeuble(array('rue' => $_POST['rue'], 'numero' => $_POST['immeuble']));
		
		// On ouvre le contact
		$contact = new contact(md5($_POST['fiche']));
		
		// On modifie l'adresse
		$contact->modification('adresse_id', $immeuble);
		
		// On rouvre le contact
		$contact = new contact(md5($_POST['fiche']));
		
		// On cherche l'adresse en question
		$adresse = $contact->adresse('declaree');
		
		// On retourne l'adresse
		echo $adresse;
	}
?>