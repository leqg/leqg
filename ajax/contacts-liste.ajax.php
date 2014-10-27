<?php
	// On récupère la liste des tris envoyés par GET
	if (isset($_GET['tri']))
	{
		// On récupère la liste des tris
		$tri = $_GET['tri'];
		
		// On retraite la liste sous forme d'un tableau d'arguments $args
		$args = array();
		
		if (!empty($_GET['tri']))
		{
			$tri = explode(',', $tri);
			
			foreach ($tri as $key => $val)
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
		}
		else
		{
			$args = array('tri' => 'last');
		}
		
		// On travaille la pagination
		$contactParPage = 15;
		
		if (isset($_GET['page']))
		{
			$pageActuelle = $_GET['page'];
		}
		else
		{
			$pageActuelle = 1;
		}
		
		// On calcule le numéro d'ordre du premier contact à afficher
		$premierContact = $contactParPage * ($pageActuelle - 1);
		
		// On lance alors la recherche des fiches
		$fiches = $fiche->liste('JSON', $args, false, $contactParPage, $premierContact);
	}
	else
	{
		// On retourne une erreur dans le cas inverse
		return false;
	}
?>