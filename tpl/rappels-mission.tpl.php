<?php
	// On protège la page
	User::protection(5);
	 
	// On fait le lien à la base de données
	$link = Configuration::read('db.link');
	
    // On ouvre la mission
    $mission = new Rappel($_GET['mission']);
    
    // On cherche à voir le nombre de numéros fait
    $appelsFait = $mission->get('fait');
    $total = $mission->get('nombre');
    
    if ($total) {
	    $fait = ($appelsFait * 100) / $total;
    } else {
	    $fait = 0;
    }
    
    
    // On charge le header du template
    Core::loadHeader(); 
?>

    <h2 class="titre" data-mission="<?php echo $mission->get('argumentaire_id'); ?>"><?php echo (!empty($mission->get('argumentaire_nom'))) ? $mission->get('argumentaire_nom') : 'Cliquez ici pour ajouter un titre.'; ?></h2>
    
    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Argumentaire – fil conducteur de l'appel</h4>
            
            <ul class="formulaire">
                <li>
                    <span class="form-icon notes">
                        <textarea name="argumentaire" id="argumentaire" class="long" placeholder="Tapez ici l'argumentaire d'appel présenté aux militants"><?php echo $mission->get('argumentaire_texte'); ?></textarea>
                    </span>
                </li>
            </ul>
        </section>
        
		<section class="contenu demi">
			<h4>Ajout de fiches selon un tri complexe</h4>
			
			<ul class="listeTris">
				<li class="tri ajoutTri premierAjoutTri" data-critere="bureau">Ajout d'un bureau de vote</li>
				<li class="tri ajoutTri" data-critere="rue">Ajout des électeurs d'une rue</li>
				<li class="tri ajoutTri" data-critere="ville">Ajout des électeurs d'une ville</li>
				<li class="tri ajoutTri" data-critere="votes">Participation à une élection</li>
				<li class="tri ajoutTri" data-critere="thema">Ajout d'un critère thématique</li>
			</ul>
		</section>
		
		<?php
			// On récupère les commentaires
			$procurations = $mission->procurations();
			
			// S'il en existe, on affiche le bloc
			if ($procurations) :
		?>
		<section class="contenu demi">
			<h4>Demandes de procuration</h4>
			
			<ul class="listeContacts">
				<?php
					foreach ($procurations as $commentaire) :
						$fiche = new People($commentaire['contact_id']);
						if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
						elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
						else { $sexe = 'isexe'; }
						
						if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
						elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
						else { $nomAffichage = 'Fiche sans nom'; }
				?>
				<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
					<li class="contact <?php echo $sexe; ?>">
						<strong><?php echo $nomAffichage; ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php endif; ?>
		
		<?php
			// On récupère les commentaires
			$procurations = $mission->recontacts();
			
			// S'il en existe, on affiche le bloc
			if ($procurations) :
		?>
		<section class="contenu demi">
			<h4>Demandes de recontact</h4>
			
			<ul class="listeContacts">
				<?php
					foreach ($procurations as $commentaire) :
						$fiche = new People($commentaire['contact_id']);
						if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
						elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
						else { $sexe = 'isexe'; }
						
						if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
						elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
						else { $nomAffichage = 'Fiche sans nom'; }
				?>
				<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
					<li class="contact <?php echo $sexe; ?>">
						<strong><?php echo $nomAffichage; ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php endif; ?>
		
		<?php
			// On récupère les commentaires
			$commentaires = $mission->commentaires();
			
			// S'il en existe, on affiche le bloc
			if ($commentaires) :
		?>
		<section class="contenu demi">
			<h4>Commentaires reportés</h4>
			
			<ul class="listeContacts">
				<?php
					foreach ($procurations as $commentaire) :
						$fiche = new People($commentaire['contact_id']);
						if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
						elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
						else { $sexe = 'isexe'; }
						
						if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
						elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
						else { $nomAffichage = 'Fiche sans nom'; }
				?>
				<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
					<li class="contact <?php echo $sexe; ?>">
						<strong><?php echo $nomAffichage; ?></strong>
						<p><?php echo nl2br($commentaire['rappel_reporting']); ?></p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php endif; ?>
	
		<section class="contenu demi">
	    	<button class="deleting long supprimerMission" style="margin: .25em auto .15em;">Suppression de la mission</button>
		</section>
    </div>
    
    <div class="colonne demi droite">
        <section class="contenu demi">
            <h4>Statistiques sur la mission</h4>
            <?php if ($mission->get('nombre') > 0) : ?>
            <p>Cette mission comporte <strong><?php echo $mission->get('nombre'); ?></strong> numéro<?php echo ($mission->get('nombre') > 1) ? 's' : ''; ?> à contacter.</p>
            <?php else : ?>
            <p>Cette mission ne comporte <strong>aucun</strong> numéro à appeler.</p>
            <?php endif; ?>
 
 			<div id="avancementMission"><div style="width: <?php echo ceil($fait); ?>%;"><?php if ($fait >= 10) { echo ceil($fait); ?>&nbsp;%<?php } ?></div></div>
 			<p>Cette mission a été réalisée à <?php echo ceil($fait); ?>&nbsp;%</p>
 			
 			<a href="<?php Core::goTo('rappels', array('action' => 'appel')); ?>" class="nostyle"><button class="vert" style="margin-bottom: .5em;">Passer un appel</button></a>
       </section>
        
        <section class="contenu demi">
	        <h4>Fiches concernées par cette mission</h4>
			
			<ul class="listeContacts fichesConcernees">
			<?php
				// On récupère la liste des rappels fait ou à effectuer
				$query = $link->prepare('SELECT `contact_id` FROM `rappels` WHERE `argumentaire_id` = :mission');
				$query->bindParam(':mission', $_GET['mission']);
				$query->execute();
				$contacts = $query->fetchAll(PDO::FETCH_ASSOC);
				
				// On récupère les informations sur les fiches
				$fiches = array();
				foreach ($contacts as $contact) {
					$fiche = new People($contact['contact_id']);
					$fiches[$fiche->get('id')] = $fiche->data();
					if ($fiche->get('sexe') == 'H') { $sexe = 'homme'; }
					elseif ($fiche->get('sexe') == 'F') { $sexe = 'femme'; }
					else { $sexe = 'isexe'; }
					
					if (!empty($fiche->display_name())) { $nomAffichage = $fiche->display_name(); }
					elseif (!empty($fiche->get('organisme'))) { $nomAffichage = $fiche->get('organisme'); }
					else { $nomAffichage = 'Fiche sans nom'; }
			?>
				<a href="<?php Core::goTo('contact', array('contact' => $fiche->get('id'))); ?>" class="nostyle contact-<?php echo $fiche->get('id'); ?>">
					<li class="contact <?php echo $sexe; ?>">
						<strong><?php echo $nomAffichage; ?></strong>
					</li>
				</a>
			<?php } ?>
			</ul>
        </section>
        
        <section class="contenu demi invisible changerNom">
            <a href="#" class="fermerColonne">&#xe813;</a>

            <h4>Changement du nom</h4>
            
            <ul class="formulaire">
                <li>
                    <span class="form-icon nom">
                        <input type="text" name="nomMission" id="nomMission" placeholder="Nom de la mission" value="<?php echo $mission->get('argumentaire_nom'); ?>">
                    </span>
                </li>
                <li>
                    <button class="validerNomMission">Valider le changement de nom</button>
                </li>
            </ul>
        </section>
		
		<section class="contenu demi selectionCritere-thema invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'un critère thématique</h4>
			
			<ul class="formulaire">
				<li>
					<label for="choixCritereThema" class="small">Tag à rechercher</label>
					<span class="form-icon decalage"><input type="text" name="choixCritereThema" id="choixCritereThema" placeholder="Thématique à filter"></span>
				</li>
				<li>
					<button class="validerChoixCritereThema">Ajouter le critère de tri</button>
				</li>
			</ul>
		</section>
		
		<section class="contenu demi selectionCritere-bureau invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'un bureau de vote</h4>
			
			<ul class="formulaire">
				<li>
					<label for="rechercheBureauVote" class="small">Recherche du bureau de vote</label>
					<span class="form-icon decalage search"><input type="text" name="rechercheBureauVote" id="rechercheBureauVote" placeholder="Numéro du bureau ou nom si configuré"></span>
				</li>
			</ul>
			
			<ul class="listeDesBureaux form-liste"></ul>
		</section>
		
		<section class="contenu demi selectionCritere-rue invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'une rue</h4>
			
			<ul class="formulaire">
				<li>
					<label for="rechercheRue" class="small">Recherche d'une rue</label>
					<span class="form-icon decalage search"><input type="text" name="rechercheRue" id="rechercheRue" placeholder="Nom de la rue, toute ville confondue"></span>
				</li>
			</ul>
			
			<ul class="listeDesRues form-liste"></ul>
		</section>
		
		<section class="contenu demi selectionCritere-ville invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'une ville</h4>
			
			<ul class="formulaire">
				<li>
					<label for="rechercheVille" class="small">Recherche d'une ville</label>
					<span class="form-icon decalage search"><input type="text" name="rechercheVille" id="rechercheVille" placeholder="Nom de la ville"></span>
				</li>
			</ul>
			
			<ul class="listeDesVilles form-liste"></ul>
		</section>
		
		<section class="contenu demi selectionCritere-votes invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'une élection</h4>
			
			<button class="choixElection long" data-election="mun2008-1" data-clair="Municipales 2008">Municipales 2008</button>
			<button class="choixElection long" data-election="mun2014-1" data-clair="Municipales 2014 – tour 1">Municipales 2014 – tour 1</button>
			<button class="choixElection long" data-election="mun2014-2" data-clair="Municipales 2014 – tour 2">Municipales 2014 – tour 2</button>
			<button class="choixElection long" data-election="eur2014" data-clair="Européennes 2014">Européennes 2014</button>
		</section>
			
		<section class="contenu demi invisible listeFiches">
			<h4>Liste des fiches selon le tri</h4>
			
			<button class="validerRecherche vert">Ajouter les fiches</button>
			
			<input type="hidden" id="nombreFiches" value="0">
			<input type="hidden" id="listeCriteresTri" value="">
			
			<ul class="listeContacts resultatTri"></ul>
		</section>
    </div>
    
<?php Core::loadFooter(); ?>