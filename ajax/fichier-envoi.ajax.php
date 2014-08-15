<?php
	$nom = (isset($_POST['nom'])) ? $_POST['nom'] : ''; 
	$contact = (isset($_POST['contact'])) ? $_POST['contact'] : ''; 
	$objet = (isset($_POST['interaction'])) ? $_POST['interaction'] : ''; 
	$reference = (isset($_POST['reference'])) ? $_POST['reference'] : ''; 
	$labels = (isset($_POST['labels'])) ? $_POST['labels'] : ''; 
	$description = (isset($_POST['description'])) ? $_POST['description'] : ''; 

	// On commence par récolter les données du POST
	$donnees = array('nom' => $core->securisation_string($nom),
					 'contact' => $core->securisation_string($contact),
					 'objet' => $core->securisation_string($objet),
					 'reference' => $core->securisation_string($reference),
					 'labels' => $core->securisation_string($labels),
					 'description' => $core->securisation_string($description) );
	
	// On commence par préparer le nom final du fichier
	if ($_POST['reference']) :
		$extension = $fichier->retourExtension($_FILES['fichier']['name']);
		$nom_final = $fichier->preparationNomFichier($_POST['reference']) . '-' . time() . '.' . $extension;
	else :
		$extension = $fichier->retourExtension($_FILES['fichier']['name']);
		$nom_final = $fichier->preparationNomFichier($_POST['nom']) . '-' . time() . '.' . $extension;
	endif;
	
	// On tâche de récupérer et de déplacer le fichier envoyé
	if ( $fichier->upload('fichier' , 'uploads/' . $nom_final , 15728640) ) :
		$enregistrement = $fichier->enregistrement($nom_final , $donnees);
		
		// On redirige vers la fiche de l'interaction
		$args = array( 'id' => $donnees['contact'] , 'interaction' => $donnees['objet'] );
		$core->tpl_go_to('fiche', $args, true);
	else :
		return false;
	endif;

?>