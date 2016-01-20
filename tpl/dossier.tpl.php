<?php
    // On protège l'accès
    User::protection(5);
    
    // On vérifie l'existance d'un élément dossier
if (!isset($_GET['dossier'])) { Core::goPage('dossier', true); 
}
    
    // On ouvre l'objet dossier
    $dossier = new Folder($_GET['dossier']);

    // On affiche l'entête
    Core::loadHeader();
?>
	
	<h2 class="titre" data-dossier="<?php echo $dossier->get('id'); ?>"><?php echo $dossier->get('name'); ?></h2>

	<div class="colonne demi gauche">
		<section class="contenu demi description">
			<h4>Description <small class="clicable modifierDescription">[modifier]</small></h4>
			<p><?php echo $dossier->get('description'); ?></p>
		</section>
		
		<section class="contenu demi contacts">
			<h4>Événements concernés par ce dossier</h4>
			
			<ul class="listeDesEvenements listing">
				<?php $evenements = $dossier->events(); foreach ($evenements as $evenement) : $e = new Event($evenement); $c = new People($e->get('people')); ?>
				<li class="objet <?php echo $e->get('type'); ?>">
					<small><span><?php echo Core::eventType($e->get('type')); ?></span></small>
					<strong><a href="<?php Core::goPage('contact', array('contact' => $e->get('people'), 'evenement' => $e->get('id'))); ?>"><?php echo $e->get('objet'); ?></a></strong>
					<ul class="infosAnnexes">
						<li class="date"><?php echo date('d/m/Y', strtotime($e->get('date'))); ?></li>
						<li class="contact"><a href="<?php Core::goPage('contact', array('contact' => $e->get('people'))); ?>"><?php echo $c->display_name(); ?></a></li>
					</ul>
				</li>
				<?php endforeach; ?>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Fichiers liés à ce dossier</h4>
			
			<ul class="listeDesFichiers">
				<?php $fichiers = $dossier->files(); if (count($fichiers)) : foreach ($fichiers as $fichier) : $e = new Event($fichier['id']); $c = new People($e->get('people')); ?>
				<a href="uploads/<?php echo $fichier['url']; ?>" target="_blank">
					<li class="fichier">
						<strong><?php echo $fichier['name']; ?></strong>
						<em><?php echo $fichier['desc']; ?></em>
						<ul class="infosAnnexes">
							<li class="contact sansChanger"><?php echo $c->display_name(); ?></li>
						</ul>
					</li>
				</a>
				<?php endforeach; else: ?>
				<li class="vide">
					<strong>Aucun fichier</strong>
				</li>
				<?php endif; ?>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		
		<section class="contenu demi notes">
			<h4>Notes</h4>
			<ul class="formulaire">
				<li>
					<span class="form-icon decalage notes"><textarea class="postit" id="modifierNotes" name="modifierNotes" style="height: 10em;" placeholder="Indiquez ici vos notes globales sur ce dossier"><?php echo $dossier->get('notes'); ?></textarea></span>
				</li>
			</ul>
		</section>
		
		<section class="contenu demi">
			<h4>Tâches liées à ce dossier</h4>
			
			<ul class="listeDesTaches">
				<?php $taches = $dossier->tasks(); if (count($taches)) : foreach ($taches as $tache) : $e = new Event($tache['event']); $c = new People($e->get('people')); ?>
				<li class="tache">
					<strong><?php echo $tache['task']; ?></strong>
					<ul class="infosAnnexes">
						<li class="contact sansChanger"><?php echo $c->display_name(); ?></li>
					</ul>
				</li>
				<?php endforeach; else: ?>
				<li class="vide">
					<strong>Aucune tâche</strong>
				</li>
				<?php endif; ?>
			</ul>
		</section>
		
		<section class="contenu demi invisible modifDescription">
    	    <a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Modification de la description</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="modificationDescription">Description</label>
					<span class="form-icon decalage notes"><textarea id="modificationDescription" name="modificationDescription"><?php echo $dossier->get('description'); ?></textarea></span>
				</li>
				<li>
					<button class="validerModificationDescription">Changer la description</button>
				</li>
			</ul>
		</section>
		
		<section class="contenu demi invisible modifTitre">
    	    <a href="#" class="fermerColonne">&#xe813;</a>

			<h4>Modification du titre du dossier</h4>

			<ul class="formulaire">
				<li>
					<label class="small" for="modificationTitre">Titre du dossier</label>
					<span class="form-icon decalage objet"><input type="text" id="modificationTitre" name="modificationTitre" value="<?php echo $dossier->get('name'); ?>"></span>
				</li>
				<li>
					<button class="validerModificationTitre">Changer le titre</button>
				</li>
			</ul>
		</section>
		
	</div>
	
<?php Core::loadFooter(); ?>