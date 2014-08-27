<section id="fiche">
	<header class="historique">
		<h2>
			Historique des campagnes de publipostage
		</h2>
	</header>
	
	<ul class="listeEncadree">
		<?php
			$query = 'SELECT * FROM envois WHERE envoi_type = "poste" ORDER BY envoi_time DESC';
			$sql = $db->query($query);
			
			while ($envoi = $sql->fetch_assoc()) : $envoi = $core->formatage_donnees($envoi); $contacts = explode(',', $envoi['destinataire']); $nombre = count($contacts);
		?>
		<a href="<?php $core->tpl_go_to('poste', array('action' => 'campagne', 'campagne' => $envoi['id'])); ?>">
			<li class="poste">
				<strong>Campagne <?php echo $envoi['titre']; ?></strong>
				<p>Envoyée le <?php echo date('d/m/Y H:i', strtotime($envoi['time'])); ?> à <?php echo $nombre; ?> contact<?php echo ($nombre > 1) ? 's' : ''; ?></p>
			</li>
		</a>
		<?php endwhile; ?>
	</ul>
</section>