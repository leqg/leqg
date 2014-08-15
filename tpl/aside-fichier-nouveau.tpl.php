<div id="nouveauFichier">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID(), 'interaction' => $historique->elementActuel(true))); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Envoi d'un nouveau fichier</h6>
	
	<form action="ajax.php?script=fichier-envoi" method="post" enctype="multipart/form-data">
		<ul class="ficheInteraction deuxColonnes petit">
			<li><!--
			 --><span class="label-information"><label for="nom">Nom</label></span><!--
			 --><input type="text" id="form-nom" name="nom"><!--
			 --><input type="hidden" id="form-interaction" name="interaction" value="<?php $historique->elementActuel(); ?>"><!--
			 --><input type="hidden" id="form-contact" name="contact" value="<?php $fiche->the_ID(); ?>"><!--
		 --></li>
			<li><!--
			 --><span class="label-information"><label for="reference">Référence</label></span><!--
			 --><input type="text" id="form-reference" name="reference" placeholder="selon votre système personnel (e.g. <?php echo date('Y-m') . '-' . rand(1,1000); ?>)"><!--
		 --></li>
			<li><!--
			 --><span class="label-information"><label for="themas">Thématiques</label></span><!--
			 --><input type="text" id="form-themas" name="themas" placeholder="séparées par des virgules (logement, sport, etc.)"><!--
		 --></li>
			<li><!--
			 --><span class="label-information"><label for="fichier">Fichier</label></span><!--
			 --><span class="bordure-form"><span class="bouton-upload">Choisir un fichier</span><span class="upload-file">Choisissez un fichier</span><input type="file" id="form-fichier" name="fichier"></span><!--
			 --><input type="hidden" name="MAX_FILE_SIZE" value="15728640"><!--
		 --></li>
			<li><!--
			 --><span class="label-information"><label for="description">Description</label></span><!--
			 --><input type="text" id="form-description" name="description"><!--
		 --></li>
			<li class="submit"><!--
			 --><input type="submit" id="upload-fichier" name="upload-fichier" value="Envoyer les fichiers"><!--
		 --></li>
		</ul>
	</form>
</div>