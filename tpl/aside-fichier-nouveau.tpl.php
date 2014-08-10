<div id="nouveauFichier">
	<h6>Envoi d'un nouveau fichier</h6>
	
	<form action="ajax.php?script=fichier-envoi" method="post" enctype="multipart/form-data">
		<ul class="ficheInteraction">
			<li><!--
			 --><label for="nom">Nom</label><!--
			 --><input type="text" id="form-nom" name="nom"><!--
			 --><input type="hidden" id="form-interaction" name="interaction" value="<?php $historique->elementActuel(); ?>"><!--
			 --><input type="hidden" id="form-contact" name="contact" value="<?php $fiche->the_ID(); ?>"><!--
		 --></li>
			<li><!--
			 --><label for="reference">Référence</label><!--
			 --><input type="text" id="form-reference" name="reference" placeholder="selon votre système personnel (e.g. <?php echo date('Y-m') . '-' . rand(1,1000); ?>)"><!--
		 --></li>
			<li><!--
			 --><label for="themas">Thématiques</label><!--
			 --><input type="text" id="form-themas" name="themas" placeholder="séparées par des virgules (logement, sport, etc.)"><!--
		 --></li>
			<li><!--
			 --><label for="fichier">Fichier</label><!--
			 --><input type="file" id="form-fichier" name="fichier"><!--
			 --><input type="hidden" name="MAX_FILE_SIZE" value="15728640"><!--
		 --></li>
			<li class="submit"><!--
			 --><input type="submit" id="upload-fichier" name="upload-fichier" value="Envoyer les fichiers"><!--
		 --></li>
		</ul>
	</form>
</div>