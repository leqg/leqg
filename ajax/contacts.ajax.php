<?php
	// On créé le lien vers la BDD Client
	$dsn =  'mysql:host=' . Configuration::read('db.host') . 
			';dbname=' . Configuration::read('db.basename');
	$user = Configuration::read('db.user');
	$pass = Configuration::read('db.pass');
	$link = new PDO($dsn, $user, $pass);
	
	// Variables initiales
	$nombreParPages = 15;

	// On récupère la liste des tris
	if (isset($_GET['tri'], $_GET['debut']))
	{
		// On récupère la variable de tri
		$tris = $_GET['tri'];
		$premiereFiche = $_GET['debut'];
		
		// On prépare les requêtes selon les tris
		if (empty($tris))
		{
			// On prépare la requête
			$query = $link->prepare('SELECT `contact_id` FROM `contacts` ORDER BY `contact_id` DESC LIMIT ' . $premiereFiche . ', ' . $nombreParPages);
			
			// On vérifie la structure de la requête
			if ($query)
			{
				// On exécute la requête
				$query->execute();
				
				// On prépare le tableau des fiches
				$fiches = $query->fetchAll(PDO::FETCH_ASSOC);
			}
			else
			{
				Core::debug($link->errorInfo());
			}
		}
		else
		{
			// On prépare le début de le requête
			$rq = 'SELECT `contact_id` FROM `contacts` ';
			
			// On retraite les tris en question dans le tableau $args
			$args = array();
			$tris = explode(',', $tris);
			foreach ($tris as $key => $val)
			{
				$val = explode(':', $val);
				
				if ($val[0] == 'bureau')
				{
					$args[$val[0]][] = $val[1];
				}
				else
				{
					$args[$val[0]] = $val[1];
				}
			}

			// On lance le traitement des arguments dans un tableau $conditions
			$conditions = array();
			$immeubles = array(); // On prépare également le tableau $immeubles pour le traitement des critères géographiques
			$bureaux = array(); // On prépare également le tableau $buraeux pour le traitement des critères de bureaux électoraux

			// On lance une boucle de traitement des arguments
			foreach ($args as $key => $arg) :

				// conditions relatives aux coordonnées
				if ($key == 'contact') {
				
					if ($arg == 'tous') $conditions[] = '( `contact_email` IS NOT NULL OR `contact_mobile` IS NOT NULL OR `contact_telephone` IS NOT NULL )';
					if ($arg == 'email') $conditions[] = '`contact_email` IS NOT NULL';
					if ($arg == 'mobile') $conditions[] = '`contact_mobile` IS NOT NULL';
					if ($arg == 'telephone') $conditions[] = '`contact_telephone` IS NOT NULL';
					
				} elseif ($key == 'bureau') {
					
					// on fait la liste des bureaux dans un tableau bureaux
					$bureaux = $arg;
					
				} elseif ($key == 'tags') {
				
					$conditions[] = '`contact_tags` LIKE "%' . $arg . '%"';
					
				} elseif ($key == 'electoral') {
					
					if ($arg == 'oui') $conditions[] = '`contact_electeur` = 1';
					if ($arg == 'non') $conditions[] = '`contact_electeur` = 0';
				
				}
				
			endforeach;

			// On prépare le tableau $conditionSQL qui contient les différentes conditions à installer dans la requête (géographique, coordonnées, divers)
			$conditionSQL = array();
			
			// S'il existe des bureaux de vote sélectionnés, on installe le critère bureau de vote dans la base de données
			if (count($bureaux) > 0) {
				$conditionSQL[] = ' ( `bureau_id` = ' . implode(' OR `bureau_id` = ', $bureaux) . ' ) ';
			}
			
			// S'il existe des immeubles sélectionnés, on installe le critère géographique dans la base de données
			if (count($immeubles) > 0) {
				$conditionSQL[] = ' ( `immeuble_id` = ' . implode(' OR `immeuble_id` = ', $immeubles) . ' ) ';
			}
			
			// S'il existe des conditions, on les reformate et on les ajoute à la requête
			if (count($conditions) > 0) :
			
				// On formate la liste des conditions en SQL
				$conditionSQL[] = ' ( ' . implode(' AND ', $conditions) . ' ) ';
							
			endif;
			
			// S'il existe des conditions à installer dans la requête SQL, on le fait
			if (count($conditionSQL) > 0) {
				$rq.= ' WHERE ' . implode(' AND ', $conditionSQL) . ' ';
			}
			
			$rq.= ' ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC LIMIT ' . $premiereFiche . ', ' . $nombreParPages;

			// On prépare la requête
			$query = $link->prepare($rq);
			
			// On exécute la requête
			$query->execute();
			
			// On prépare le tableau des fiches
			$fiches = $query->fetchAll(PDO::FETCH_ASSOC);
		}
		
		// On s'occupe d'ouvrir chaque fiches pour réaliser l'export JSON par contact dans le tableau $lignes
		$lignes = array();
		foreach ($fiches as $key => $fiche)
		{
			// On ouvre la fiche contact
			$contact = new contact(md5($fiche['contact_id']));
			
			// On exporte les données connues sous format JSON
			$lignes[$key] = $contact->donnees();
		}
		
		// On retourne sous forme de JSON le tableau contenant les lignes
		echo json_encode($lignes);
	}
?>