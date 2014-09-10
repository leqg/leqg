<?php
	$query = 'SELECT * FROM envois WHERE envoi_id = ' . $_GET['campagne'];
	$sql = $db->query($query);
	$campagne = $core->formatage_donnees($sql->fetch_assoc());
	
	$contacts = explode(',', $campagne['destinataire']);
	$nombre = count($contacts);
	$cout = $nombre * 0.1;
?>
<section id="fiche">
	<header class="poste">
		<h2>
			<span>Campagne de publipostage</span>
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
			<span class="label-information">Destinataires</span>
			<ul class="listeEncadree">
				<?php foreach ($contacts as $contact) : ?>
				<a href="<?php $core->tpl_go_to('fiche', array('id' => $contact)); ?>">
					<li class="electeur">
						<strong><?php $fiche->affichageNomByID($contact); ?>
						<?php if ($fiche->is_adresse_fichier()) : ?><p><?php $fiche->adressePostale($fiche->get_immeuble(), ' '); ?></p><?php endif; ?>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>

<aside>
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('poste', array('action' => 'historique')); ?>">Retour à l'historique</a>
	</nav>
</aside>