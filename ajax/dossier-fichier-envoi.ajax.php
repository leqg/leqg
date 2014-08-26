<?php
	$nom = (isset($_POST['nom'])) ? $_POST['nom'] : ''; 
	$dossier = (isset($_POST['dossier'])) ? $_POST['dossier'] : ''; 
	$reference = (isset($_POST['reference'])) ? $_POST['reference'] : ''; 
	$labels = (isset($_POST['labels'])) ? $_POST['labels'] : ''; 
	$description = (isset($_POST['description'])) ? $_POST['description'] : ''; 
	$upload = (isset($_FILES['fichier'])) ? $_FILES['fichier'] : '';

	// On commence par récolter les données du POST
	$donnees = array('nom' => $core->securisation_string($nom),
					 'dossier' => $core->securisation_string($dossier),
					 'reference' => $core->securisation_string($reference),
					 'labels' => $core->securisation_string($labels),
					 'description' => $core->securisation_string($description) );

	if (!empty($_FILES['fichier']['tmp_name'])) {
		// On commence par préparer le nom final du fichier
		if ($reference) :
			$extension = $fichier->retourExtension($upload['name']);
			$nom_final = $fichier->preparationNomFichier($reference) . '-' . time() . '.' . $extension;
		else :
			$extension = $fichier->retourExtension($upload['name']);
			$nom_final = $fichier->preparationNomFichier($nom) . '-' . time() . '.' . $extension;
		endif;
		
		// On tâche de récupérer et de déplacer le fichier envoyé
		$fichier->upload('fichier' , 'uploads/' . $nom_final , 15728640);
	} else {
		$nom_final = '';
	}
	
	$enregistrement = $fichier->enregistrement($nom_final , $donnees);
	
	// On redirige vers la fiche de l'interaction
	$args = array( 'id' => $donnees['dossier'] );
	$core->tpl_go_to('dossier', $args, true);

?>