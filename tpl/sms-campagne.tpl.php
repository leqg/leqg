<?php
	$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['campagne'];
	$sql = $db->query($query);
	$campagne = $core->formatage_donnees($sql->fetch_assoc());
	
	$contacts = explode(',', $campagne['destinataire']);
	$nombre = count($contacts);
	$cout = $nombre * 0.1;
?>
<section id="fiche">
	<header class="sms">
		<h2>
			<span>Campagne SMS</span>
			<span><?php echo $campagne['titre']; ?></span>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Message</span>
			<p><?php echo html_entity_decode($campagne['texte']); ?></p>
		</li>
		<li>
			<span class="label-information">Heure d'envoi</span>
			<p><?php echo date('d/m/Y H:i', strtotime($campagne['time'])); ?></p>
		</li>
		<li>
			<span class="label-information">Coût de l'envoi</span>
			<p><?php echo number_format($cout, 2, ',', ' '); ?> &euro;</p>
		</li>
		<li>
			<span class="label-information">Destinataires</span>
			<ul class="listeEncadree">
				<?php foreach ($contacts as $contact) : ?>
				<a href="<?php $core->tpl_go_to('fiche', array('id' => $contact)); ?>">
					<li class="electeur">
						<strong><?php $fiche->affichageNomByID($contact); ?></strong>
						<p><?php echo $core->tpl_phone($fiche->contact('mobile', false, true, $contact)); ?></p>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>

<aside>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('sms', array('action' => 'historique')); ?>">Retour à l'historique</a>
	</nav>
</aside>