<div id="nouveauEmail">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID())); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Envoi d'un courrier électronique au contact</h6>
	
	<form action="ajax.php?script=email-envoi" method="post">
		<input type="hidden" name="contact" value="<?php $fiche->the_ID(); ?>">
		<input type="hidden" name="email" value="<?php $fiche->infos('email'); ?>">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Destinataire</span>
				<ul class="listeEncadree">
					<li class="email vide">
						<strong><?php $fiche->affichage_nom(); ?></strong>
						<p><?php $fiche->infos('email'); ?></p>
					</li>
				</ul>
			</li>
			<li>
				<span class="label-information"><label for="form-objet">Objet du message</label></span>
				<input type="text" name="objet" id="form-objet" placeholder="Objet de l'email">
			</li>
			<li>
				<span class="label-information"><label for="form-email">Texte du message</label></span>
				<textarea id="form-email" name="message" placeholder="Contenu de l'email"></textarea>
			</li>
			<li>
				<span class="label-information">Astuce</span>
				<p>N'oubliez pas de signer votre courrier électronique pour garantir à l'expéditeur la provenance de celui-ci.</p>
			</li>
			<li class="submit">
				<input type="submit" value="Envoyer l'email">
			</li>
		</ul>
	</form>
</div>