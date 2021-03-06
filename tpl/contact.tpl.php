<?php
	// On protège le module
	User::protection(5);
	
	// Chargement de l'objet contact
	$data = new People($_GET['contact']);
	$address = $data->postal_address();

	// Chargement de l'entête
	Core::loadHeader();
?>

<h2 class="titre" id="nomContact" data-fiche="<?php echo $data->get('id'); ?>"><?php if (!empty($data->display_name())) { echo $data->display_name(); } else { echo 'Cliquez pour ajouter un nom'; } ?></h2>

<div class="colonne demi gauche">
	<section id="fiche-details" class="contenu demi">
		<ul class="icones-etatcivil">
			<li class="sexe <?php echo strtolower($data->display_sex()); ?> ?>"><?php echo $data->display_sex(true); ?></li>
			<li class="electeur <?php if ($data->get('electeur_europeen')) { echo 'eur'; } elseif ($data->get('electeur')) { echo 'oui'; } else { echo 'non'; } ?>"><?php if ($data->get('electeur_europeen')) { ?><span title="Électeur européen">Européen</span><?php } else { ?>Électeur<?php } ?></li>
			<li class="sms <?php if ($data->contact_details_exist('mobile')) { ?>envoyerSMS<?php } ?>">SMS</li>
			<li class="email <?php if ($data->contact_details_exist('email')) { ?>envoyerEmail<?php } ?>">Email</li>
		</ul>
	
		<h4>Données connues</h4>
		<ul class="etatcivil">
			<li class="naissance modif" data-info="naissance"><?php if ($data->get('date_naissance') != '0000-00-00') { echo $data->birthdate(); } else { echo '<span class="inconnu">Date de naissance inconnue</span>'; } ?></li>
			<li class="age"><?php echo $data->display_age(); ?></li>
			<li class="adresse modif" data-info="adresse"><?php if (!empty($address['reel'])) { echo $address['reel']; } else { ?><span class="inconnu">Adresse inconnue</span><?php } ?></li>
			<li class="organisme modif" data-info="organisme">
				<?php 
				if (!empty($data->get('organisme')) && !empty($data->get('fonction'))) :
					echo $data->get('organisme') . ' (' . $data->get('fonction') . ')';
				elseif (!empty($data->get('organisme')) && empty($data->get('fonction'))) :
					echo $data->get('organisme');
				elseif (empty($data->get('organisme')) && !empty($data->get('fonction'))) :
					echo $data->get('fonction');
				else : ?>
					<span class="inconnu">Pas d'organisme renseigné</span>
				<?php endif; ?>
			</li>
		</ul>
		
		<?php if ($data->get('electeur') == 1 || $data->get('electeur_europeen') == 1) : ?>
		<h4>Données électorales</h4>
		<ul class="etatcivil">
			<li class="bureau"><?php $bureau = People::poll($data->get('id')); echo 'Bureau '.$bureau['number']; ?></li>
			<li class="immeuble"><?php echo $address['officiel']; ?></li>
		</ul>
		<?php endif; ?>
		
		<h4>Données de contact</h4>
		<ul class="etatcivil coordonnees">
			<?php $coordonnees = $data->contact_details(); foreach ($coordonnees as $coordonnee) : ?>
			<li class="<?php echo $coordonnee['coordonnee_type']; ?>" id="<?php echo $coordonnee['coordonnee_type']; ?>-<?php echo $coordonnee['coordonnee_id']; ?>" data-id="<?php echo $coordonnee['coordonnee_id']; ?>"><?php 
				if ($coordonnee['coordonnee_type'] == 'email')
				{
					echo $coordonnee['coordonnee_email'];
				}
				else
				{ 
					Core::formatPhone($coordonnee['coordonnee_numero']); 
				} 
		  ?></li>
			<?php endforeach; ?>
			<li class="ajout ajouterCoordonnees">Ajouter une nouvelle information de contact</li>
		</ul>
	</section>
	
	<section id="fichesLiees" class="contenu demi">
		<h4>Fiches liées</h4>
		
		<ul class="etatcivil">
			<?php $fiches = $data->linked_people(); foreach ($fiches as $identifiant => $fiche) : $ficheLiee = new contact($identifiant); ?>
			<li class="lien fiche-liee-<?php echo $ficheLiee->get('id'); ?>"><a href="<?php Core::goPage('contact', array('contact' => $ficheLiee->get('id'))); ?>"><?php echo $ficheLiee->display_name(); ?></a> <a href="#" class="retraitLiaison nostyle" data-fiche="<?php echo $ficheLiee->get('id'); ?>"><small>&#xe8b0;</small></a></li>
			<?php endforeach; ?>
			<li class="ajout ajouterLien">Ajouter une nouvelle fiche liée</li>
		</ul>
	</section>
	
	<section class="contenu demi">
    	<button class="deleting long supprimerFiche" style="margin: .25em auto .15em;">Suppression de la fiche</button>
	</section>
</div>


<div id="colonneDroite" class="colonne demi droite">
	<?php if (!empty($address['reel']) || !empty($address['officiel'])) { ?><section id="mapbox-contact" class="contenu demi"></section><?php } ?>
	
	<section id="TagsContact" class="contenu demi">
		<h4>Tags liés au contact</h4>
		
		<ul class="listeDesTags">
			<?php if (count($data->get('tags'))) : foreach ($data->get('tags') as $tag) : ?>
			<li class="tag" data-tag="<?php echo $tag; ?>"><?php echo $tag; ?></li>
			<?php endforeach; endif; ?>
			<li class="ajout ajouterTag">Ajouter un nouveau tag</li>
			<li class="ajout formulaireTag hide"><input type="text" name="tag" id="tag" class="formulaireCache" placeholder="Indiquez le nouveau tag puis appuyez sur entrée"></li>
		</ul>
	</section>
	
	<section id="listeEvenements" class="contenu demi">
		<h4>Événements connus</h4>
		
		<ul class="listeDesEvenements">
			<li class="evenement nouvelEvenement">
				<strong>Créer un nouvel événement</strong>
			</li>
			<?php $events = $data->events(); if (count($events)) : foreach ($events as $event) : $event = new Event($event['id']); ?>
			<?php
				// on regarde si on peut ouvrir l'événement
				if ($event->link() == 2) {
					echo '<a href="#" class="accesEvenement nostyle evenement-' . $event->get('id') . '" data-evenement="' . $event->get('id') . '">';
				}
				// on regarde si on peut rediriger vers la campagne
				elseif ($event->link() == 1) {
					echo '<a href="'; Core::goPage($event->get('type'), array('campagne' => $event->get('campaign'))); echo '" class="nostyle">';
				}
			?>
				<li class="evenement <?php echo $event->get('type'); ?> <?php if ($event->link()) { ?>clic<?php } ?>">
					<small><span><?php echo Event::displayType($event->get('type')); ?></span></small>
					<strong><?php echo (!empty($event->get('objet'))) ? $event->get('objet') : 'Événement sans titre'; ?></strong>
					<ul class="infosAnnexes">
						<li class="date"><?php echo date('d/m/Y', strtotime($event->get('date'))); ?></li>
					</ul>
				</li>
			<?php if ($event->link()) { ?></a><?php } ?>
			<?php endforeach; endif; ?>
		</ul>
	</section>
	
	<section class="contenu demi invisible changerNom">
		<a href="#" class="fermerColonne">&#xe813;</a>

		<h4>Modification du nom</h4>
		
		<ul class="formulaire">
			<li>
				<label class="small">Nom de famille</label>
				<span class="form-icon decalage nom"><input type="text" name="changerNomFamille" id="changerNomFamille" value="<?php echo mb_convert_case($data->get('nom'), MB_CASE_UPPER); ?>"></span>
			</li>
			<li>
				<label class="small">Nom d'usage</label>
				<span class="form-icon decalage nom"><input type="text" name="changerNomUsage" id="changerNomUsage" value="<?php echo mb_convert_case($data->get('nom_usage'), MB_CASE_UPPER); ?>"></span>
			</li>
			<li>
				<label class="small">Prénoms</label>
				<span class="form-icon decalage nom"><input type="text" name="changerPrenoms" id="changerPrenoms" value="<?php echo mb_convert_case($data->get('prenoms'), MB_CASE_TITLE); ?>"></span>
			</li>
			<li>
				<button class="validerChangementNom">Changer le nom</button>
			</li>
		</ul>
	</section>
	
	<section id="evenement" class="contenu demi invisible" data-evenement="">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="eventInfos formulaire small">
			<li>
				<label class="small" for="eventTitre">Objet</label>
				<span class="form-icon objet">
					<input type="text" name="titre" id="eventTitre" value="">
				</span>
			</li>
			<li>
				<label class="small" for="eventType">Type</label>
				<span class="form-icon type">
					<label class="sbox" for="eventType">
						<select name="type" id="eventType">
							<option value="contact">Entrevue</option>
							<option value="telephone">Contact téléphonique</option>
							<option value="courriel">Courrier électronique</option>
							<option value="courrier">Correspondance postale</option>
							<option value="autre">Autre</option>
						</select>
					</label>
				</span>
			</li>
			<li>
				<label class="small" for="eventLieu">Lieu</label>
				<span class="form-icon lieu">
					<input type="text" name="lieu" id="eventLieu" value="">
				</span>
			</li>
			<li>
				<label class="small" for="eventDate">Date</label>
				<span class="form-icon date">
					<input type="text" name="date" id="eventDate" value="" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}">
				</span>
			</li>
			<li>
				<label class="small" for="eventNotes">Notes</label>
				<span class="form-icon notes">
					<textarea name="notes" id="eventNotes" style="height: 10em;"></textarea>
				</span>
			</li>
			<li>
			    <label class="small">Dossier</label>
			    <ul class="affichageDossier">
    			        <li class="dossier lierDossier">
    			            <strong>Créer / Lier à un dossier</strong>
    			        </li>
    			        <a href="" class="transparent afficherInfosDossier" style="display: none;">
        			        <li class="dossier">
        			            <strong></strong>
        			            <em></em>
        			        </li>
    			        </a>
			    </ul>
			</li>
			<li>
				<label class="small">Tâches</label>
				<ul class="listeDesTaches">
					<li class="tache nouvelleTache">
						<strong>Ajouter une nouvelle tâche</strong>
					</li>
				</ul>
			</li>
			<li>
				<label class="small">Fichiers</label>
				<ul class="listeDesFichiers">
					<li class="fichier nouveauFichier">
						<strong>Ajouter un nouveau fichier</strong>
					</li>
				</ul>
			</li>
			<li>
				<button class="supprimerEvenement long deleting">Supprimer l'événement</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible ajoutFichier">
		<a href="#" class="revenirEvenement">&#xe813;</a>
		
		<form id="envoiDeFichier" action="ajax.php?script=fichier-envoi" method="post" enctype="multipart/form-data">
			<input type="hidden" name="fiche" value="<?php echo $data->get('id'); ?>">
			<input type="hidden" name="evenement" id="formEvenement" value="">
			<input type="hidden" name="MAX_FILE_SIZE" value="15728640">
			<ul class="formulaire">
				<li>
					<label class="small" for="formFichier">Fichier</label>
					<span class="form-icon decalage file">
						<input type="file" id="formFichier" name="formFichier">
					</span>
				</li>
				<li>
					<label class="small" for="formFichierTitre">Titre du fichier</label>
					<span class="form-icon decalage titre">
						<input type="text" id="formFichierTitre" name="formFichierTitre">
					</span>
				</li>
				<li>
					<label class="small" for="formFichierDesc">Description</label>
					<span class="form-icon decalage description">
						<input type="text" id="formFichierDesc" name="formFichierDesc">
					</span>
				</li>
				<li>
					<button type="submit" class="envoiFichier">Envoyer le fichier</button>
				</li>
			</ul>
		</form>
	</section>

	<section id="ChercherFicheALier" class="contenu demi invisible">
		<ul class="formulaire">
			<li>
				<label>Recherchez une fiche à lier</label>
				<span class="form-icon search"><input type="text" name="rechercheFiche" id="rechercheFiche" placeholder="Pierre Dupont"></span>
			</li>
		</ul>
		<ul class="form-liste invisible" id="listeFichesALier"></ul>
	</section>
	
	<section class="contenu demi invisible modifierNaissance">
		<a href="#" class="fermerColonne">&#xe813;</a>

		<ul class="formulaire">
			<li>
				<label class="small" for="dateDeNaissance">Date de naissance</label>
				<span class="form-icon decalage naissance"><input type="text" name="dateDeNaissance" id="dateDeNaissance" placeholder="jj/mm/aaaa" value="<?php if ($data->get('date_naissance') != '0000-00-00') echo $data->birthdate(); ?>" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"></span>
			</li>
			<li>
				<button class="sauvegardeDateNaissance">Sauvegarder les informations</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible modifierOrganisme">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="changerOrganisme">Organisme</label>
				<span class="form-icon decalage organisme"><input type="text" name="changerOrganisme" id="changerOrganisme" value="<?php echo $data->get('organisme'); ?>" placeholder="Organisation"></span>
			</li>
			<li>
				<label class="small" for="changerFonction">Fonction</label>
				<span class="form-icon decalage fonction"><input type="text" name="changerFonction" id="changerFonction" value="<?php echo $data->get('fonction'); ?>" placeholder="Fonction"></span>
			</li>
			<li>
				<button class="sauvegarderOrganisation">Sauvegarder les informations</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible modifierAdresse">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="rechercherRue">Rue</label>
				<span class="form-icon decalage rue"><input type="text" name="rechercherRue" id="rechercherRue"></span>
			</li>
			<li>
        		<ul class="form-liste invisible" id="listeRues"></ul>
			</li>
			<li>
			    <button class="deleting supprimerAdresse">Retirer l'adresse de la fiche</button>
			</li>
		</ul>
	</section>

	<section class="demi contenu invisible choixImmeuble">
		<a href="#" class="fermerColonne">&#xe813;</a>

		<ul class="formulaire">
			<li>
				<label>Sélectionnez des immeubles</label>
				<span class="form-icon rue"><input type="text" name="rueSelectionImmeuble" id="rueSelectionImmeuble" value=""></span>
			</li>
		</ul>
		
		<ul class="form-liste invisible" id="listeImmeubles"></ul>
	</section>
	
	<section class="contenu demi invisible creationNouvelleRueSelectionVille">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="rechercherRue">Rechercher la ville</label>
				<span class="form-icon decalage ville"><input type="text" name="rechercherVille" id="rechercherVille"></span>
			</li>
			<li>
        		<ul class="form-liste invisible" id="listeVilles"></ul>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible creationNouvelleRue">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="immeubleNouvelleRue">Immeuble de la rue</label>
				<span class="form-icon decalage immeuble"><input type="text" name="immeubleNouvelleRue" id="immeubleNouvelleRue"></span>
			</li>
			<li>
				<label class="small" for="nomNouvelleRue">Nom de la rue</label>
				<span class="form-icon decalage rue"><input type="text" name="nomNouvelleRue" id="nomNouvelleRue"></span>
			</li>
			<li>
				<label class="small" for="nomCodePostal">Code postal</label>
				<span class="form-icon decalage ville"><input type="text" name="nomCodePostal" id="nomCodePostal"></span>
			</li>
			<li>
				<label class="small" for="communeNouvelleRue">Ville de la rue</label>
				<span class="form-icon decalage ville"><input type="text" name="communeNouvelleRue" id="communeNouvelleRue" disabled></span>
				<input type="hidden" name="villeNouvelleRue" id="villeNouvelleRue">
			</li>
			<li>
				<button class="validerCreationRue">Créer cette nouvelle adresse</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible formCreerNouvelImmeuble">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="nouvelImmeuble">Nouvel immeuble à créer</label>
				<span class="form-icon decalage immeuble"><input type="text" name="nouvelImmeuble" id="nouvelImmeuble" data-rue=""></span>
			</li>
			<li>
				<button class="validerCreationImmeuble">Créer ce nouvel immeuble</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible ajouterTache">
		<a href="#" class="revenirEvenement">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="formAjoutTache">Tâche</label>
				<span class="form-icon decalage tache"><input type="text" name="formAjoutTache" id="formAjoutTache" placeholder="Tâche à réaliser"></span>
			</li>
			<li>
				<label class="small" for="formDeadlineTache">Date limite</label>
				<span class="form-icon decalage date"><input type="text" name="formDeadlineTache" id="formDeadlineTache" placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}"></span>
			</li>
			<li>
				<label class="small" for="formDestinataireTache">Destinataire</label>
				<span class="form-icon utilisateur">
					<label class="sbox" for="formDestinataireTache">
						<select id="formDestinataireTache" name="formDestinataireTache">
							<?php $users = User::all(0); foreach ($users as $user) : ?>
							<option value="<?php echo $user['id']; ?>"><?php echo $user['firstname']; ?> <?php echo $user['lastname']; ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</span>
			</li>
			<li>
				<button class="validerTache">Ajouter la tâche</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible newContactDetail">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="modifEmail">Numéro de téléphone ou adresse email</label>
				<span class="form-icon decalage"><input type="text" name="newCoord" id="newCoord"></span>
			</li>
			<li>
				<button class="enregistrerNewCoord" data-type="email">Enregistrer</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible modifier-email" data-id="">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="modifEmail">Modifier l'adresse email</label>
				<span class="form-icon decalage email"><input type="text" name="modifEmail" id="modifEmail"></span>
			</li>
			<li>
				<button class="validerChangement" data-type="email">Enregistrer le changement</button>
			</li>
			<li>
				<button class="supprimerCoordonnee deleting" data-type="email">Supprimer cet email</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible modifier-fixe" data-id="">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="modifFixe">Modifier le numéro de téléphone fixe</label>
				<span class="form-icon decalage fixe"><input type="text" name="modifFixe" id="modifFixe"></span>
			</li>
			<li>
				<button class="validerChangement" data-type="fixe">Enregistrer le changement</button>
			</li>
			<li>
				<button class="supprimerCoordonnee deleting" data-type="fixe">Supprimer ce numéro</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible modifier-mobile" data-id="">
		<a href="#" class="fermerColonne">&#xe813;</a>
		
		<ul class="formulaire">
			<li>
				<label class="small" for="modifMobile">Modifier le numéro de téléphone mobile</label>
				<span class="form-icon decalage mobile"><input type="text" name="modifMobile" id="modifMobile"></span>
			</li>
			<li>
				<button class="validerChangement" data-type="mobile">Enregistrer le changement</button>
			</li>
			<li>
				<button class="supprimerCoordonnee deleting" data-type="mobile">Supprimer ce numéro</button>
			</li>
		</ul>
	</section>
	
	<section class="contenu demi invisible envoi-sms">
    	    <a href="#" class="fermerColonne">&#xe813;</a>
    	    
    	    <h4>Envoi d'un SMS</h4>
    	    
    	    <ul class="formulaire">
        	    <li>
        	        <label class="small" for="choixNumero">Choix du numéro</label>
        	        <span class="form-icon sms">
	        	        <label class="sbox" for="choixNumero">
		        	        <select name="choixNumero" id="choixNumero">
		            			<?php $coordonnees = $data->contact_details(); foreach ($coordonnees as $coordonnee) : if ($coordonnee['coordonnee_type'] == 'mobile') : ?>
		            	        <option value="<?php echo $coordonnee['coordonnee_id']; ?>"><?php Core::formatPhone($coordonnee['coordonnee_numero']); ?></option>
		            	        <?php endif; endforeach; ?>
		        	        </select>
	        	        </label>
        	        </span>
	            </li>
	            <li>
	                <label class="small" for="messageSMS">Message à envoyer</label>
	                <span class="form-icon decalage sms"><textarea name="messageSMS" id="messageSMS" placeholder="SMS à envoyer"></textarea></span>
	            </li>
	            <li>
	                <button class="SMSsending">Envoi du SMS ( <i>&#xe8cd;</i> <small>0,08&nbsp;&euro;</small> )</button>
	            </li>
    	    </ul>
	</section>
	
	<section class="contenu demi invisible envoi-email">
    	    <a href="#" class="fermerColonne">&#xe813;</a>
    	    
    	    <h4>Envoi d'un courrier électronique</h4>
    	    
    	    <ul class="formulaire">
        	    <li>
        	        <label class="small" for="choixNumero">Choix de l'adresse</label>
        	        <span class="form-icon email">
	        	        <label class="sbox" for="choixAdresse">
		        	        <select name="choixAdresse" id="choixAdresse">
		            			<?php $coordonnees = $data->contact_details('email'); foreach ($coordonnees as $coordonnee) : if ($coordonnee['coordonnee_type'] == 'email') : ?>
		            	        <option value="<?php echo $coordonnee['coordonnee_id']; ?>"><?php echo $coordonnee['coordonnee_email']; ?></option>
		            	        <?php endif; endforeach; ?>
		        	        </select>
	        	        </label>
        	        </span>
            </li>
            <li>
                <label class="small" for="objetEmail">Objet du courrier électronique</label>
                <span class="form-icon decalage objet"><input type="text" name="objetEmail" id="objetEmail" placeholder="Objet de l'email"></span>
            </li>
            <li>
                <label class="small" for="messageEmail">Message à envoyer</label>
                <span class="form-icon decalage email"><textarea name="messageEmail" id="messageEmail" placeholder="Email à envoyer"></textarea></span>
            </li>
            <li>
                <button class="EmailSending">Envoi de l'email (<i>&#xe8cd;</i>)</button>
            </li>
    	    </ul>
	</section>
	
	<section class="contenu demi invisible selectionDossier">
		<a href="#" class="revenirEvenement">&#xe813;</a>
		
		<h4>Sélectionnez un dossier pour le lier</h4>
		
		<ul class="listeDesDossiers">
    		    <li class="dossier ajoutDossier">
    		        <strong>Créer un nouveau dossier</strong>
    		    </li>
    		    <?php $dossiers = Folder::all(); foreach ($dossiers as $dossier) : ?>
	            <li class="dossier choixDossier dossier-<?php echo $dossier['id']; ?>" data-dossier="<?php echo $dossier['id']; ?>">
	                <strong><?php echo $dossier['name']; ?></strong>
	                <em><?php echo $dossier['desc']; ?></em>
	            </li>
    		    <?php endforeach; ?>
		</ul>
	</section>
	
	<section class="contenu demi invisible creationDossier">
		<a href="#" class="revenirDossier">&#xe813;</a>
		
		<h4>Création d'un nouveau dossier</h4>
		
		<ul class="formulaire">
    		    <li>
    		        <label class="small">Nom du dossier</label>
    		        <span class="form-icon objet"><input type="text" name="creationDossierNom" id="creationDossierNom"></span>
    		    </li>
    		    <li>
    		        <label class="small">Description</label>
    		        <span class="form-icon description"><textarea name="creationDossierDesc" id="creationDossierDesc"></textarea></span>
    		    </li>
    		    <li>
    		        <button class="creerDossier">Créer ce dossier</button>
    		    </li>
		</ul>
	</section>
</div>


<!-- Formulaires en overlay -->
<div id="ajoutCoordonnees" class="overlayForm">
	<form id="ajoutDeCoordonnees" method="post" action="ajax.php?script=coordonnees–ajout">
		<input type="hidden" name="fiche" id="idFiche" value="<?php echo $_GET['contact']; ?>">
		<a class="fermetureOverlay" href="#">&#xe813;</a>
		<h3>Ajout d'un moyen de contact</h3>
		<ul>
			<li>
				<label>Type de coordonnées</label>
				<div class="radio"><input class="selectionType" data-type="email" type="radio" name="type" id="ajoutCoordonneesEmail" value="email" required><label for="ajoutCoordonneesEmail"><span><span></span></span>Adresse email</label></div>
				<div class="radio"><input class="selectionType" data-type="telephone" type="radio" name="type" id="ajoutCoordonneesMobile" value="mobile" required><label for="ajoutCoordonneesMobile"><span><span></span></span>Téléphone mobile</label></div>
				<div class="radio"><input class="selectionType" data-type="telephone" type="radio" name="type" id="ajoutCoordonneesFixe" value="fixe" required><label for="ajoutCoordonneesFixe"><span><span></span></span>Téléphone fixe</label></div>
			</li>
			<li class="detail-critere detail-critere-email affichageOptionnel">
				<label for="form-modifier-email">Adresse email</label>
				<input type="email" name="email" id="form-ajout-email" autocomplete="off">
			</li>
			<li class="detail-critere detail-critere-telephone affichageOptionnel">
				<label for="form-modifier-email">Numéro de téléphone</label>
				<input type="text" name="numero" id="form-ajout-telephone" autocomplete="off">
			</li>
			<li>
				<input type="submit" value="Ajouter l'information">
			</li>
		</ul>
	</form>
</div>


<?php 
	if (!empty($address['reel']) || !empty($address['officiel'])) : 
	$postal = People::postal($data->get('id'));
	if (!empty($address['reel'])) {
    	    $postal = $postal['reel'];
	} else {
    	    $postal = $postal['officiel'];
	}
?>
<script>
	// Mise en place de la map
	var map = L.map('mapbox-contact');
	
	// Sélection du tile layer OSM
	L.tileLayer('http://otile3.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png').addTo(map);

	// On récupère sur le Nominatim OSM les coordonnées de la rue en question
	var data = {
		format: 'json',
		email: 'tech@leqg.info',
		country: '<?php echo $postal['country']; ?>',
		city: "<?php echo $postal['city']; ?>",
		street: "<?php echo $postal['building'] . ' ' . $postal['street']; ?>"
	}
	
	// On récupère le JSON contenant les coordonnées de la rue
	$.getJSON('https://nominatim.openstreetmap.org', data, function(data) {
		// On récupère uniquement les données du premier résultat
		data = data[0];
		
		// On prépare la boundingbox
		var loc1 = new L.LatLng(data.boundingbox[0], data.boundingbox[2]);
		var loc2 = new L.LatLng(data.boundingbox[1], data.boundingbox[3]);
		var bounds = new L.LatLngBounds(loc1, loc2);
		
		// On fabrique une vue qui contient l'ensemble du secteur demandé
		map.fitBounds(bounds, { maxZoom: 17 });
		
		// On ajoute un marker au milieu de la rue
		L.marker([data.lat, data.lon], {
			clicable: false,
			title: "<?php echo $postal['building'] . ' ' . $postal['street'] . ' ' . $postal['zipcode'] . ' ' . $postal['city'] . ' ' . $postal['country']; ?>"
		}).addTo(map);
	});
</script>
<?php endif; ?>

<?php Core::loadFooter(); ?>