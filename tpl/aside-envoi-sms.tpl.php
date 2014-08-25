<div id="nouveauSMS">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_ID())); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Envoi d'un SMS au contact</h6>
	
	<form action="ajax.php?script=sms-envoi" method="post">
		<input type="hidden" name="contact" value="<?php $fiche->the_ID(); ?>">
		<input type="hidden" name="numero" value="<?php $fiche->infos('mobile'); ?>">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Destinataire</span>
				<ul class="listeEncadree">
					<li class="numero vide">
						<strong><?php $fiche->affichage_nom(); ?></strong>
						<p><?php $core->tpl_phone($fiche->get_infos('mobile')); ?></p>
					</li>
				</ul>
			</li>
			<li>
				<span class="label-information"><label for="form-message">Texte du message</label></span>
				<textarea id="form-sms" name="message"></textarea>
			</li>
			<li>
				<span class="label-information">Facturation</span>
				<p>Ce message sera facturé comme <strong class="gras" id="estimation-sms">1</strong> SMS</p>
			</li>
			<li class="submit">
				<input type="submit" value="Envoyer le SMS">
			</li>
		</ul>
	</form>
</div>