<?php $d = $fiche->dossier($_GET['id']); ?>
<section id="fiche">
	<header class="dossier">
		<h2><span>Dossier</span><span id="titre-dossier"><?php echo $d['nom']; ?></span></h2>
		<a class="nostyle" id="config-icon" href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'], 'modifierInfos' => 'true')); ?>">&#xe855;</a>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information"><label for="description">Ouverture du dossier</label></span>
			<p><?php echo date('d / m / Y', strtotime($d['date_ouverture'])); ?></p>
		</li>
		<li>
			<span class="label-information"><label for="description">Description</label></span>
			<p><?php echo nl2br($d['description']); ?></p>
		</li>
		<li>
			<span class="label-information">Fichiers associés au dossier</span>
			<ul class="listeEncadree">
				<?php if ($fichier->nombreFichiers('dossier' , $d['id'])) : ?>
				<?php $fichiers = $fichier->listeFichiers('dossier' , $d['id']); foreach ($fichiers as $f) : ?>
				<?php if(!empty($f['url'])) { ?><a href="uploads/<?php echo $f['url']; ?>" target="_blank"><?php } ?>
					<li class="fichier <?php echo $fichier->extension($f['id']); ?>">
						<strong><?php echo $f['nom']; ?></strong>
						<?php if (!empty($f['reference']) || !empty($f['description'])) : ?>
						<p>
							<?php if (!empty($f['reference']) && empty($f['description'])) echo 'Référence <em>' . $f['reference'] . '</em>'; ?>
							<?php if (empty($f['reference']) && !empty($f['description'])) echo $f['description']; ?>
							<?php if (!empty($f['reference']) && !empty($f['description'])) echo 'Référence <em>' . $f['reference'] . '</em><br>' . $f['description']; ?>
						</p>
						<?php endif; ?>
					</li>
				<?php if(!empty($f['url'])) { ?></a><?php } ?>
				<?php endforeach; endif; ?>
				<a href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'], 'ajoutFichier' => 'true')); ?>">
					<li class="fichier ajoutFichier">
						<strong>Ajouter un nouveau fichier</strong>
					</li>
				</a>
			</ul>
		</li>
		<li>
			<span class="label-information">Fiches associées</span>
			<ul class="listeEncadree">
				<?php 
					$fiches = explode(',', $d['contacts']);
					$contacts = array();
					foreach($fiches as $key => $contact) { 
						$nom = $fiche->affichageNomByID($contact, '', true);
						$contacts[$key]['id'] = $contact;
						$contacts[$key]['nom'] = $nom;
					}
					
					$core->triParColonne($contacts, 'nom');
					
					foreach ($contacts as $contact) :
				?>
				<a href="<?php $core->tpl_go_to('fiche', array('id' => $contact['id'])); ?>">
					<li class="electeur">
						<strong><?php $fiche->nomByID($contact['id'], '', false); ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>

<aside>
	<?php if (isset($_GET['ajoutFichier'])) : ?>
		<div id="ajoutFichier">
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'])); ?>">Retour au dossier</a>
			</nav>
			
			<h6>Ajout d'un fichier au dossier</h6>
			
			<form action="ajax.php?script=dossier-fichier-envoi" method="post" enctype="multipart/form-data">
				<ul class="ficheInteraction deuxColonnes petit">
					<li>
						<span class="label-information"><label for="nom">Nom</label></span>
						<input type="text" id="form-nom" name="nom">
						<input type="hidden" id="form-contact" name="dossier" value="<?php echo $d['id']; ?>">
					</li>
					<li>
						<span class="label-information"><label for="reference">Référence</label></span>
						<input type="text" id="form-reference" name="reference" placeholder="selon votre système personnel (e.g. <?php echo date('Y-m') . '-' . rand(1,1000); ?>)">
					</li>
					<li>
						<span class="label-information"><label for="themas">Thématiques</label></span>
						<input type="text" id="form-themas" name="themas" placeholder="séparées par des virgules (logement, sport, etc.)">
						</li>
					<li>
						<span class="label-information"><label for="fichier">Fichier</label></span>
						<span class="bordure-form"><span class="bouton-upload">Choisir un fichier</span><span class="upload-file">Choisissez un fichier</span><input type="file" id="form-fichier" name="fichier"></span>
						<input type="hidden" name="MAX_FILE_SIZE" value="15728640">
					</li>
					<li>
						<span class="label-information"><label for="description">Description</label></span>
						<input type="text" id="form-description" name="description">
					</li>
					<li class="submit">
						<input type="submit" id="upload-fichier" name="upload-fichier" value="Envoyer les fichiers">
					</li>
				</ul>
			</form>
		</div>
	<?php else : ?>
	<div id="historique">
		<h6>Historique des interactions</h6>
		<?php $interactions = $historique->rechercheParDossier($d['id']); // On initialise la liste des interactions à afficher ?>
		
		<table id="historique-contact">
			<thead>
				<tr>
					<th>Type</th>
					<th>Date</th>
					<th>Électeur concerné <a class="add-historique" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>&nouvelleInteraction=true">&#xe816;</a></th>
					<!--<th>Thématiques</th>-->
				</tr>
			</thead>
			<tbody>
				<?php foreach ($interactions as $interaction) : ?>
				<tr>
					<td><?php $historique->returnType($interaction['type']); ?></td>
					<td><?php echo date('d/m/Y', strtotime($interaction['date'])); ?></td>
					<td><a href="<?php $core->tpl_go_to('fiche', array('id' => $interaction['contact_id'], 'interaction' => $interaction['id'])); ?>"><?php echo $fiche->affichageNomByID($interaction['contact_id']); ?></a></td>
					<!--<td class="liste-tags"><?php $historique->affichageThematiques($interaction['thematiques']); ?></td>-->
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div id="modifierInfos">
		<nav class="navigationFiches">
			<a class="retour" href="<?php $core->tpl_go_to('dossier', array('id' => $d['id'])); ?>">Retour au dossier</a>
		</nav>
		
		<h6>Modification des informations concernant ce dossier</h6>
		
		<form action="ajax.php?script=modification-dossier" method="post">
			<input type="hidden" name="dossier" value="<?php echo $d['id']; ?>">
			<ul class="deuxColonnes">
				<li>
					<span class="label-information">Nom du dossier</span>
					<input type="text" name="nom" id="form-nom" value="<?php echo $d['nom']; ?>">
				</li>
				<li>
					<span class="label-information">Description</span>
					<textarea name="description" id="form-description"><?php echo $d['description']; ?></textarea>
				</li>
				<li class="submit">
					<input type="submit" value="Enregistrer les modifications">
				</li>
			</ul>
		</form>
	</div>
	<?php endif; ?>
</aside>