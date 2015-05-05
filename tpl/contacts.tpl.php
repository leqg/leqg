<?php
	User::protection(5);
	Core::tpl_header();
?>

	<h2>Votre fichier contacts consolidé</h2>

	<form class="rechercheGlobale" action="index.php?page=recherche" method="post">
		<span class="search-icon">
			<input type="search" name="recherche" placeholder="Recherche d'un contact">
			<span class="annexesRecherche">
				<span class="iconeRecherche"></span>
				<input type="submit" class="lancementRecherche" value="&#xe8af;">
			</span>
		</span>
	</form>

	<div class="colonne demi gauche">
		<section class="contenu demi">
			<ul class="iconesActions">
				<a href="<?php Core::tpl_go_to('contact', array('operation' => 'creation')); ?>"><li class="new">Nouvelle fiche</li></a>
				<!--<a href="<?php Core::tpl_go_to('fiche', array('operation' => 'fusion')); ?>"><li class="merge">Fusion de fiches</li></a>-->
			</ul>
		</section>

		<section class="contenu demi">
			<h4>Critères multiples de tri</h4>

			<ul class="listeTris">
				<li class="tri ajoutTri premierAjoutTri" data-critere="bureau">Ajout d'un bureau de vote</li>
				<li class="tri ajoutTri" data-critere="rue">Ajout des électeurs d'une rue</li>
				<li class="tri ajoutTri" data-critere="ville">Ajout des électeurs d'une ville</li>
				<li class="tri ajoutTri" data-critere="votes">Participation à une élection</li>
				<li class="tri ajoutTri" data-critere="thema">Ajout d'un critère thématique</li>
				<li class="tri ajoutTri" data-critere="zipcode">Ajout d'un critère postal</li>
			</ul>
		</section>

		<section class="contenu demi">
			<h4>Critères généraux de tri</h4>

			<ul class="formulaire serre">
				<li>
					<label class="small" for="coordonnees-email">Email</label>
					<span class="form-icon email">
						<label class="sbox" for="coordonnees-email">
							<select name="coordonnees-email" id="coordonnees-email" class="selectionTri">
								<option value="1">Avec email uniquement</option>
								<option value="-1">Sans email uniquement</option>
								<option value="0" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="coordonnees-mobile">Téléphone mobile</label>
					<span class="form-icon mobile">
						<label class="sbox" for="coordonnees-mobile">
							<select name="coordonnees-mobile" id="coordonnees-mobile" class="selectionTri">
								<option value="1">Avec mobile uniquement</option>
								<option value="-1">Sans mobile uniquement</option>
								<option value="0" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="coordonnees-fixe">Téléphone fixe</label>
					<span class="form-icon telephone">
						<label class="sbox" for="coordonnees-fixe">
							<select name="coordonnees-fixe" id="coordonnees-fixe" class="selectionTri">
								<option value="1">Avec fixe uniquement</option>
								<option value="-1">Sans fixe uniquement</option>
								<option value="0" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
				<li>
					<label class="small" for="electeur">Liste électorale</label>
					<span class="form-icon utilisateur">
						<label class="sbox" for="coordonnees-electeur">
							<select name="coordonnees-electeur" id="coordonnees-electeur" class="selectionTri">
								<option value="1">Le contact est électeur</option>
								<option value="-1">Le contact n'est pas électeur</option>
								<option value="0" selected>Indifférent</option>
							</select>
						</label>
					</span>
				</li>
			</ul>
		</section>
	</div>

	<div class="colonne demi droite">
		<section class="contenu demi">
			<h4>Tâches à réaliser rapidement</h4>

			<?php $taches = Event::tasks(); if ($taches) : ?>
			<ul class="listeDesTaches">
				<?php
					foreach ($taches as $tache) :
						$event = new Event($tache['event']);
						$fiche = new People($event->get('people'));
				?>
				<a href="<?php Core::tpl_go_to('contact', array('contact' => $fiche->get('id'), 'evenement' => $event->get('id'))); ?>" class="transparent">
					<li class="tache loupeOver">
						<strong><?php echo $tache['task']; ?></strong>
						<em><?php echo User::get_login_by_ID($tache['user']); ?></em>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
			<?php else : ?>

			<?php endif; ?>
		</section>

        <?php $liste = Event::last(); if (count($liste)) : ?>
        <section class="contenu demi">
    	    <h4>Dernières interactions</h4>

    	    <ul class="listeDesEvenements">
        	    <?php foreach ($liste as $event) : $e = new Event($event['id']); $c = new People($e->get('people')); ?>
				<li class="evenement <?php echo $e->get('type'); ?> <?php if ($e->link()) { ?>clic<?php } ?>">
					<small><span><?php echo Event::display_type($e->get('type')); ?></span></small>
					<strong><a href="<?php echo Core::tpl_go_to('contact', array('contact' => $c->get('id'), 'evenement' => $e->get('id'))); ?>"><?php echo (!empty($e->get('objet'))) ? $e->get('objet') : 'Événement sans titre'; ?></a></strong>
					<ul class="infosAnnexes">
						<li class="contact"><a href="<?php echo Core::tpl_go_to('contact', array('contact' => $c->get('id'))); ?>"><?php echo $c->display_name(); ?></a></li>
						<li class="date"><?php echo date('d/m/Y', strtotime($e->get('date'))); ?></li>
					</ul>
				</li>
                <?php endforeach; ?>
    	    </ul>
	    </section>
	    <?php endif; ?>

		<section class="contenu demi invisible actionsFiches">
			<ul class="iconesActions">
				<li class="smsSelection">SMS&nbsp;groupé</li><!--
			 --><li class="emailSelection">Emailing</li><!--
			 --><li class="publiSelection">Publipostage</li><!--
			 --><li class="exportSelection">Export</li>
			</ul>
		</section>

		<section class="contenu demi invisible listeFiches">
			<h4>Liste des fiches selon le tri <span class="estimationDuNombreDeFichesTotales"></span></h4>
			<input type="hidden" id="nombreFiches" value="0">
			<input type="hidden" id="listeCriteresTri" value="">

			<ul class="listeContacts resultatTri"></ul>
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

		<section class="contenu demi selectionCritere-votes invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection d'une élection</h4>

			<button class="choixElection long" data-election="mun2008-1" data-clair="Municipales 2008">Municipales 2008</button>
			<button class="choixElection long" data-election="mun2014-1" data-clair="Municipales 2014 – tour 1">Municipales 2014 – tour 1</button>
			<button class="choixElection long" data-election="mun2014-2" data-clair="Municipales 2014 – tour 2">Municipales 2014 – tour 2</button>
			<button class="choixElection long" data-election="eur2014" data-clair="Européennes 2014">Européennes 2014</button>
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

		<section class="contenu demi selectionCritere-zipcode invisible">
			<a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Sélection par code postal</h4>

			<ul class="formulaire">
				<li>
					<label for="rechercheCodePostalDebut" class="small">Code postal de démarrage</label>
					<span class="form-icon decalage search"><input type="text" name="rechercheCodePostalDebut" id="rechercheCodePostalDebut" placeholder="Code postal de début de la séquence recherchée"></span>
				</li>
				<li>
					<label for="rechercheCodePostalFin" class="small">Code postal final</label>
					<span class="form-icon decalage search"><input type="text" name="rechercheCodePostalFin" id="rechercheCodePostalFin" placeholder="Code postal de fin de la séquence recherchée"></span>
				</li>
				<li>
					<button class="validerRechercheCodesPostaux">Ajouter le critère de tri</button>
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

		<section class="contenu demi invisible smsEnvoiCampagne">
			<a href="#" class="fermerColonneListe">&#xe813;</a>
			<input type="hidden" name="smsCriteresComplets" id="smsCriteresComplets" value="">

			<h4>Envoi d'une nouvelle campagne SMS</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="smsTitreCampagne">Titre de la campagne</label>
					<span class="form-icon decalage titre"><input type="text" name="smsTitreCampagne" class="smsTitreCampagne" id="smsTitreCampagne" placeholder="Titre de la campagne"></span>
				</li>
				<li>
					<label class="small" for="smsNombreDestinataire">Nombre de destinataire</label>
					<span class="form-icon decalage nombre"><input type="text" name="smsNombreDestinataire" class="smsNombreDestinataire" id="smsNombreDestinataire" disabled></span>
				</li>
				<li>
					<label class="small" for="smsMessageCampagne">Message à envoyer</label>
					<span class="form-icon decalage sms"><textarea name="smsMessageCampagne" class="smsMessageCampagne" id="smsMessageCampagne" placeholder="Message envoyé aux contacts sélectionnés"></textarea></span>
				</li>
				<li>
					<button class="smsValidationCampagne"><span>Envoi de la campagne ( <i>&#xe8cd;</i> <small class="smsEstimation">0&nbsp;&euro;</small> )</span></button>
				</li>
			</ul>
		</section>

		<section class="contenu demi invisible emailEnvoiCampagne">
			<a href="#" class="fermerColonneListe">&#xe813;</a>
			<input type="hidden" name="emailCriteresComplets" id="emailCriteresComplets" value="">

			<h4>Envoi d'une nouvelle campagne Email</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="emailTitreCampagne">Objet de la campagne</label>
					<span class="form-icon decalage titre"><input type="text" name="emailTitreCampagne" class="emailTitreCampagne" id="emailTitreCampagne" placeholder="Objet des emails de la campagne"></span>
				</li>
				<li>
					<label class="small" for="emailNombreDestinataire">Nombre de destinataire</label>
					<span class="form-icon decalage nombre"><input type="text" name="emailNombreDestinataire" class="emailNombreDestinataire" id="emailNombreDestinataire" disabled></span>
				</li>
				<li>
					<label class="small" for="emailMessageCampagne">Message à envoyer</label>
					<span class="form-icon decalage email"><textarea name="emailMessageCampagne" class="emailMessageCampagne long" id="emailMessageCampagne" placeholder="Message envoyé aux contacts sélectionnés"></textarea></span>
				</li>
				<li>
					<button class="emailValidationCampagne"><span>Envoi de la campagne ( <i>&#xe8cd;</i> )</span></button>
				</li>
			</ul>
		</section>

		<section class="contenu demi invisible publiEnvoiCampagne">
			<a href="#" class="fermerColonneListe">&#xe813;</a>
			<input type="hidden" name="publiCriteresComplets" id="publiCriteresComplets" value="">

			<h4>Préparation d'un nouveau publipostage</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="publiTitreCampagne">Titre de la campagne</label>
					<span class="form-icon decalage titre"><input type="text" name="publiTitreCampagne" class="publiTitreCampagne" id="publiTitreCampagne" placeholder="Titre de la campagne"></span>
				</li>
				<li>
					<label class="small" for="publiNombreDestinataire">Nombre de destinataire</label>
					<span class="form-icon decalage nombre"><input type="text" name="publiNombreDestinataire" class="publiNombreDestinataire" id="publiNombreDestinataire" disabled></span>
				</li>
				<li>
					<label class="small" for="publiDescriptionCampagne">Description de la campagne</label>
					<span class="form-icon decalage publi"><textarea name="publiDescriptionCampagne" class="publiDescriptionCampagne" id="publiDescriptionCampagne" placeholder="Description de la campagne envoyée"></textarea></span>
				</li>
				<li>
					<button class="publiValidationCampagne"><span>Préparation de la campagne</span></button>
				</li>
			</ul>
		</section>

		<section class="contenu demi invisible chargementEnCours">
    		    <p style="text-align: center">Chargement...</p>
    		    <p style="text-align: center">Ceci peut prendre plusieurs minutes.</p>
		</section>

		<section class="contenu demi invisible creationEnCours">
    		    <p style="text-align: center">Création en cours...</p>
    		    <p style="text-align: center">La création peut prendre plusieurs minutes.</p>
		</section>
	</div>

<?php Core::tpl_footer(); ?>
