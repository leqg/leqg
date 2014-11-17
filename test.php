<?php
//error_reporting(E_ALL);

//require_once('includes.php');

// L'idée, c'est de tester PDO

//require_once('includes.php');
/*
// On tente une connexion au service d'authentification et de gestion des comptes clients
$host = '2001:4b98:dc0:41:216:3eff:fe6d:e95';
$port = 3306;
$dbname = 'leqg-core';
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$user = 'leqg-remote';
$pass = 'pbNND3JY2cfrDUuZ';
$link = new PDO($dsn, $user, $pass);

print_r($link);
*/

// Création d'un cookie pour avoir la paix
setcookie('leqg', hash('sha256', 1), time()+3600*24*7, '/', 'localhost');
setcookie('time', time(), time()+3600*24*7, '/', 'localhost');

/*
// On lance un mécanisme de transfert automatique des coordonnées depuis le système actuel vers le nouveau système
$link = new PDO("mysql:host=" . Configuration::read('db.host') . ";dbname=" . Configuration::read('db.basename') . ";charset=utf8", Configuration::read('db.user'), Configuration::read('db.pass'));

// On va retraiter toutes les données à "problème" présentes dans la BDD en commençant par les contacts
$query = $link->prepare('SELECT * FROM `taches` WHERE `tache_description` LIKE :terme ORDER BY `tache_id` ASC');
$terme = "%&%";
$query->bindParam(':terme', $terme);
$query->execute();

$resultats = $query->fetchAll(PDO::FETCH_ASSOC);

// Fonction de retraitement
function retraitement($var) {
	return html_entity_decode($var, ENT_QUOTES);
}

foreach ($resultats as $resultat)
{
	
	// On retraite les données à problème
	$resultat['tache_description'] = retraitement($resultat['tache_description']);
	
	// On prépare la requête de modification
	$query = $link->prepare('UPDATE `taches` SET `tache_description` = :nom WHERE `tache_id` = :id');
	$query->bindParam(':nom', $resultat['tache_description']);
	$query->bindParam(':id', $resultat['tache_id']);
	
	$query->execute();
	
	echo $resultat['tache_id'] . '<br>';
}
*/

/*
// On lance un mécanisme de détection des numéros de téléphone ou emails existant pour chaque fiche
$query = $link->prepare('SELECT * FROM `coordonnees`');
$query->execute();
$coord = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($coord as $c)
{
	$query = $link->prepare('UPDATE `contacts` SET `contact_' . $c['coordonnee_type'] . '` = 1 WHERE `contact_id` = :id');
	$query->bindParam(':id', $c['contact_id'], PDO::PARAM_INT);
	$query->execute();
}
*/

/*$query = $link->prepare('SELECT `contact_id`, `contact_email`, `contact_telephone`, `contact_mobile` FROM `contacts` WHERE `contact_email` IS NOT NULL OR `contact_telephone` IS NOT NULL OR `contact_mobile` IS NOT NULL');
$query->execute();
$contacts = $query->fetchAll();

// On prépare les requêtes
$email = $link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_email`) VALUES (:contact, "email", :email)');
$mobile = $link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_numero`) VALUES (:contact, "mobile", :numero)');
$fixe = $link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_numero`) VALUES (:contact, "fixe", :numero)');

foreach ($contacts as $contact) {
	// Si le contact possède un email, on l'ajoute à la base de données
	if (!is_null($contact['contact_email']))
	{
		$email->bindParam(':contact', $contact['contact_id']);
		$email->bindParam(':email', $contact['contact_email']);
		$email->execute();
		echo $contact['contact_id'] . ' : ' . $contact['contact_email'] . '<br>';
	}
	
	if (!is_null($contact['contact_mobile']))
	{
		$mobile->bindParam(':contact', $contact['contact_id']);
		$mobile->bindParam(':numero', $contact['contact_mobile'], PDO::PARAM_INT);
		$mobile->execute();
		echo $contact['contact_id'] . ' : ' . $contact['contact_mobile'] . '<br>';
	}
	
	if (!is_null($contact['contact_telephone']))
	{
		$fixe->bindParam(':contact', $contact['contact_id']);
		$fixe->bindParam(':numero', $contact['contact_telephone'], PDO::PARAM_INT);
		$fixe->execute();
		echo $contact['contact_id'] . ' : ' . $contact['contact_telephone'] . '<br>';
	}
}*/


// On teste la recherche
//$recherche = 'chem';
//$rue = $core->formatage_recherche($recherche);
//$rues = $carto->recherche_rue('67482', $rue);

//$core->debug($rues);

/*$query = 'SELECT * FROM `rues`';
$sql = $db->query($query);

while ($row = $sql->fetch_assoc()) :

	$rue = $row['rue_nom'];
	$rue = str_replace('Chem ', 'Chemin ', $rue);
	$rue = str_replace('Pce ', 'Place ', $rue);
	$rue = str_replace('Rte ', 'Route ', $rue);
	$rue = str_replace('Imp ', 'Impasse ', $rue);
	$rue = str_replace('Rle ', 'Ruelle ', $rue);
	$rue = str_replace('Ave ', 'Avenue ', $rue);
	$rue = str_replace('Bd ', 'Boulevard ', $rue);
	$rue = str_replace('All ', 'Allée ', $rue);
	$db->query('UPDATE `rues` SET `rue_nom` = "' . $rue . '" WHERE `rue_id` = ' . $row['rue_id']);

endwhile;*/

/*$query = 'SELECT * FROM `contacts` LEFT JOIN `immeubles` ON `immeubles`.`immeuble_id` = `contacts`.`immeuble_id`';
$sql = $db->query($query);

while ($row = $sql->fetch_assoc()) {
	$db->query('UPDATE `contacts` SET `bureau_id` = ' . $row['bureau_id'] . ' WHERE `contact_id` = ' . $row['contact_id']);
}*/


/*$data = $csv->lectureFichier('csv/parti.csv');

// On prépare les tableaux des résultats
$resultats = array();

foreach ($data as $key => $line) {
	
	$donnees = array('nom'		=> trim($line[3]),
					 'prenom'	=> trim($line[4]),
					 'fixe'		=> trim($line[7]),
					 'mobile'	=> trim($line[9]),
					 'email'	=> trim($line[8]),
					 'adresse'	=> trim($line[10]),
					 'numero'	=> null,
					 'rue'		=> null,
					 'cp'		=> trim($line[11]),
					 'ville'	=> trim($line[12]));

	if ($key > 0) break;
	
	// On cherche la fiche dont le nom correspond à la ligne, avec pour tag 'parti'
	$query = 'SELECT * FROM `contacts` WHERE (`contact_nom` LIKE "%' . $core->formatage_recherche($donnees['nom']) . '%" OR `contact_nom_usage` LIKE "%' . $core->formatage_recherche($donnees['nom']) . '%") AND `contact_prenoms` LIKE "%' . $core->formatage_recherche($donnees['prenom']) . '%" AND `contact_tags` LIKE "%parti%"';
	$sql = $db->query($query); $core->debug($query);
	
	if ($sql->num_rows == 1) { $resultats['correct'][] = $line; }
	elseif ($sql->num_rows > 1) { $resultats['doublons'][] = $line; }
	else { $resultats['aucun'][] = $line; }
}

	$core->debug($resultats['doublons']);*/


// Traitement de la date de naissance
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($result = $sql->fetch_assoc()) :
			
		$mysqli->query('UPDATE	contacts
						SET		contact_naissance_date = "' . date('Y-m-d', strtotime($result['contact_naissance_jour'])) . '"
						WHERE	contact_id = ' . $result['contact_id']
					  );
	
	endwhile;
*/


// Traitement des villes de naissance pour les inscrire dans la base de données
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($result = $sql->fetch_assoc()) :
	
		$ville_origine = $result['contact_naissance_commune_texte'];
		$dept_origine = $result['contact_naissance_departement'];
		
		if ($dept_origine == 99) {
			// la personne est née à l'étranger
			$etranger = 1;
			$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "0" WHERE contact_id = '.$result['contact_id']);
		} else {
			$etranger = 0;
			
			// Pour éviter les problèmes d'apostrophes (dans la BDD souvent des espaces) des villes comme L'isle, on remplace par un joker
			$remplacement = array("'", " ", "OE");
			$ville_origine = str_replace($remplacement, '%', $ville_origine);
			
			// On évite certains caractères spéciaux
			$ville_origine = str_replace('œ', 'oe', $ville_origine);
			
			// Cas particulier de Paris / Marseille / Lyon, si ça commence par ces noms, on affiche juste la ville sans l'arrondissement
			if (preg_match('/^PARIS/', $ville_origine)) { $ville_origine = 'PARIS'; }
			if (preg_match('/^LYON/', $ville_origine)) { $ville_origine = 'LYON'; }
			if (preg_match('/^MARSEILLE/', $ville_origine)) { $ville_origine = 'MARSEILLE'; }
			
			// Cas particulier des DOM-TOM où la recherche doit porter sur 971 au lieu de 97
			if ($dept_origine == 97) { $dept_origine = '97%'; }
			
			// Cas particulier des villes mal orthographiées dans les bases de données
			if ($ville_origine == 'SAINT-DIE' && $dept_origine == 88) { $ville_origine = 'SAINT-DIE-DES-VOSGES'; }
			if ($ville_origine == 'CHERBOURG' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
			if ($ville_origine == 'OCTEVILLE' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
			if ($ville_origine == 'MEULAN' && $dept_origine == 78) { $ville_origine = 'MEULAN-EN-YVELINES'; }
			
			// Cas particulier de Chalons sur Marne qui a changé de nom pour Chalons en Champagne
			if ($ville_origine == 'CHALONS-SUR-MARNE' && $dept_origine == 51) { $ville_origine = 'CHALONS-EN-CHAMPAGNE'; }
			
			$q = '	SELECT	*
					FROM	communes
					WHERE	departement_id LIKE "' . $dept_origine . '"
					AND		commune_nom LIKE "' . $ville_origine . '"
				 ';
				 
			$sql2 = $mysqli->query($q);
			
			// Si on ne trouve pas de fiche, alors on dit qu'on ne sait pas
			if ($sql2->num_rows == 1) {
				$r = $sql2->fetch_assoc();
				$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "' . $r['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
			} else {
				// On essaye juste de voir s'il n'y a pas un petit article devant
				$q3 = '	SELECT	*
						FROM	communes
						WHERE	departement_id LIKE "' . $dept_origine . '"
						AND		commune_nom LIKE "%' . $ville_origine . '"
					 ';
					 
				$sql3 = $mysqli->query($q3);
				if ($sql3->num_rows == 1) {
					$r3 = $sql3->fetch_assoc();
					$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "' . $r3['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
				} else {
					// On regarde enfin si ça a un rapport avec la corse
					if ($dept_origine == 2) {
						$q4 = '	SELECT	*
								FROM	communes
								WHERE	departement_id LIKE "20%"
								AND		commune_nom LIKE "' . $ville_origine . '"
							 ';
							 
						$sql4 = $mysqli->query($q4);
						if ($sql4->num_rows == 1) {
							$r4 = $sql4->fetch_assoc();
							$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "' . $r4['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
						} else {
							$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "0" WHERE contact_id = '.$result['contact_id']);
						}
					} else {
						$mysqli->query('UPDATE contacts SET contact_naissance_commune_id = "0" WHERE contact_id = '.$result['contact_id']);
					}
				}
			}
			
		}

	endwhile;
*/


// Traitement des codes postaux
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($result = $sql->fetch_assoc()) :

		// Identifiant du contact
		$id = $result['contact_id'];

		// On explose le contenu à chaque espace
		$adresse = explode(' ', $result['contact_adresse_ville'], 2);
		
		print_r($adresse); echo '<br>';
		
		if ($adresse[1] == 'STRASBOURG') {
			$q = 'UPDATE contacts SET contact_adresse_cp = "' . $adresse[0] . '", contact_adresse_ville = "' . $adresse[1] . '" WHERE contact_id = ' . $id;
			$mysqli->query($q);
		}

	endwhile;
*/


// Liaison des villes déclarées dans les adresses avec leur communes.commune_id
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($result = $sql->fetch_assoc()) :
	
		$ville_origine = $result['contact_adresse_ville'];
		$dept_origine = str_split($result['contact_adresse_cp'], 2);
		$dept_origine = $dept_origine[0];
		
		if ($dept_origine == '') {
			// la personne est née à l'étranger
			$etranger = 1;
			echo 'etranger';
			$mysqli->query('UPDATE contacts SET commune_id = "0" WHERE contact_id = '.$result['contact_id']);
		} else {
			$etranger = 0;
			
			// Pour éviter les problèmes d'apostrophes (dans la BDD souvent des espaces) des villes comme L'isle, on remplace par un joker
			$remplacement = array("'", " ", "OE");
			$ville_origine = str_replace($remplacement, '%', $ville_origine);
			
			// On évite certains caractères spéciaux
			$ville_origine = str_replace('œ', 'oe', $ville_origine);
			
			// Cas particulier de Paris / Marseille / Lyon, si ça commence par ces noms, on affiche juste la ville sans l'arrondissement
			if (preg_match('/^PARIS/', $ville_origine)) { $ville_origine = 'PARIS'; }
			if (preg_match('/^LYON/', $ville_origine)) { $ville_origine = 'LYON'; }
			if (preg_match('/^MARSEILLE/', $ville_origine)) { $ville_origine = 'MARSEILLE'; }
			
			// Cas particulier des DOM-TOM où la recherche doit porter sur 971 au lieu de 97
			if ($dept_origine == 97) { $dept_origine = '97%'; }
			
			// Cas particulier des villes mal orthographiées dans les bases de données
			if ($ville_origine == 'SAINT-DIE' && $dept_origine == 88) { $ville_origine = 'SAINT-DIE-DES-VOSGES'; }
			if ($ville_origine == 'CHERBOURG' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
			if ($ville_origine == 'OCTEVILLE' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
			if ($ville_origine == 'MEULAN' && $dept_origine == 78) { $ville_origine = 'MEULAN-EN-YVELINES'; }
			
			// Cas particulier de Chalons sur Marne qui a changé de nom pour Chalons en Champagne
			if ($ville_origine == 'CHALONS-SUR-MARNE' && $dept_origine == 51) { $ville_origine = 'CHALONS-EN-CHAMPAGNE'; }
			
			$q = '	SELECT	*
					FROM	communes
					WHERE	departement_id LIKE "' . $dept_origine . '"
					AND		commune_nom LIKE "' . $ville_origine . '"
				 ';
				 
			$sql2 = $mysqli->query($q);
			
			// Si on ne trouve pas de fiche, alors on dit qu'on ne sait pas
			if ($sql2->num_rows == 1) {
				$r = $sql2->fetch_assoc();
				$mysqli->query('UPDATE contacts SET commune_id = "' . $r['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
			} else {
				// On essaye juste de voir s'il n'y a pas un petit article devant
				$q3 = '	SELECT	*
						FROM	communes
						WHERE	departement_id LIKE "' . $dept_origine . '"
						AND		commune_nom LIKE "%' . $ville_origine . '"
					 ';
					 
				$sql3 = $mysqli->query($q3);
				if ($sql3->num_rows == 1) {
					$r3 = $sql3->fetch_assoc();
					$mysqli->query('UPDATE contacts SET commune_id = "' . $r3['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
				} else {
					// On regarde enfin si ça a un rapport avec la corse
					if ($dept_origine == 2) {
						$q4 = '	SELECT	*
								FROM	communes
								WHERE	departement_id LIKE "20%"
								AND		commune_nom LIKE "' . $ville_origine . '"
							 ';
							 
						$sql4 = $mysqli->query($q4);
						if ($sql4->num_rows == 1) {
							$r4 = $sql4->fetch_assoc();
							$mysqli->query('UPDATE contacts SET commune_id = "' . $r4['commune_id'] . '" WHERE contact_id = '.$result['contact_id']);
						} else {
							$mysqli->query('UPDATE contacts SET commune_id = "0" WHERE contact_id = '.$result['contact_id']);
						}
					} else {
						$mysqli->query('UPDATE contacts SET commune_id = "0" WHERE contact_id = '.$result['contact_id']);
					}
				}
			}
			
		}

	endwhile;
*/


// Traitement des numéros de rue
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($result = $sql->fetch_assoc()) :

		// Identifiant du contact
		$id = $result['contact_id'];

		// On explose le contenu à chaque espace
		$adresse = explode(' ', $result['contact_adresse_rue'], 2);
		
		// On regarde si le premier morceau est un chiffre
		$adresse[0]{0};
		if (is_numeric($adresse[0]{0})) {
			$numero = $adresse[0];
			$adresse = addslashes($adresse[1]);
		}
		else {
			$numero = NULL;
			$adresse = addslashes($adresse[0].' '.$adresse[1]);
		}
		
		$q = "UPDATE contacts SET contact_adresse_numero='".$numero."', contact_adresse_rue='".$adresse."' WHERE contact_id='".$id."'";	

		$mysqli->query($q);

	endwhile;
*/


// On transforme les gens en électeurs et on leur affecte leur canton

//	$mysqli->query("UPDATE contacts SET contact_electeur = 1");
//	$mysqli->query("UPDATE contacts SET canton_id = 2775");


// On va tâcher de lister les rues de la base de données
/*
	$query = "SELECT * FROM contacts";
	$sql = $mysqli->query($query);
	
	while ($row = $sql->fetch_assoc()) :

		echo $row['contact_adresse_numero']." - ".$row['contact_adresse_rue']."<br>";
		
	// À partir de là, on essayer de récupérer des données vis à vis de cette adresse
		$bureau = $row['bureau_id'];
		$canton = $row['canton_id'];
		$commune = $row['commune_id'];
		echo $bureau . ' - ' . $canton . ' - ' . $commune . ' - ';
		
	// À partir de là, on récupère l'adresse
		$adresse = addslashes($row['contact_adresse_rue']);
	
		// On regarde si l'adresse existe déjà dans la BDD
		$query2 = "SELECT * FROM rues WHERE bureau_id='".$bureau."' AND rue_nom='".$adresse."'";
		$sql2 = $mysqli->query($query2);
		$row2 = $sql2->fetch_assoc();
		$nb_row2 = $sql2->num_rows;
		
		if ($nb_row2 == 0) {
			echo "La rue n'existe pas<br>";
			$query3 = "INSERT INTO rues VALUES ('', '', '".$canton."', '".$commune."', '".$bureau."', '".$adresse."')";
			echo $query3."<br>";
			$mysqli->query($query3);
			$adresse_id = $mysqli->insert_id;
		} else {
			echo "La rue existe<br>";
			$adresse_id = $row2['rue_id'];
		}
		
		$sql2->close();


	// À partir de là, on essaye de savoir dans quel immeuble ils vivent
		$numero = $row['contact_adresse_numero'];
		
		// On regarde si dans le bureau de vote, à l'adresse demandé, ce numéro a déjà été déclaré
		$query4 = "SELECT * FROM immeubles WHERE rue_id='".$adresse_id."' AND immeuble_numero='".$numero."'";
		$sql4 = $mysqli->query($query4);
		$row4 = $sql4->fetch_assoc();
		$nb_row2 = $sql4->num_rows;
		
		if ($nb_row2 == 0) {
			echo "L'immeuble n'existe pas<br>";
			$query5 = "INSERT INTO immeubles VALUES ('', '', '".$canton."', '".$commune."', '".$bureau."', '".$adresse_id."', '".$numero."')";
			echo $query5.'<br>';
			$mysqli->query($query5);
			$immeuble_id = $mysqli->insert_id;
		} else {
			echo "L'immeuble existe déjà<br>";
			$immeuble_id = $row4['immeuble_id'];
		}
		
		$sql4->close();

		
		$query6 = "UPDATE contacts SET rue_id='".$adresse_id."', immeuble_id='".$immeuble_id."' WHERE contact_id='".$row['contact_id']."'";
		echo $query6;
		$mysqli->query($query6);

		
		echo '<br><br>';

	endwhile;
*/


// On calcule un mot de passe pour l'ajouter à la base
//echo $user->encrypt_pass('evecsanobi-67');	

?>