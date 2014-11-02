<?php
	if (isset($_POST['fiche'], $_POST['ville'], $_POST['rue'], $_POST['immeuble']))
	{
		// On créé le bordel
		$rue = $carto->ajoutRue($_POST['ville'], $_POST['rue'], $_POST['immeuble']);
		$immeuble = $carto->ajoutImmeuble(array('rue' => $rue, 'numero' => $_POST['immeuble']));
		
		// On ouvre le contact
		$contact = new contact(md5($_POST['fiche']));
		
		// On modifie l'adresse
		$contact->modification('adresse_id', $immeuble);
		
		// On rouvre le contact
		$contact = new contact(md5($_POST['fiche']));
		
		// On récupère l'adresse
		$adresse = $contact->adresse('declaree');
		
		// On renvoit l'adresse
		echo $adresse;
	}
?>