<?php 
if (isset($_GET['mission']) && $porte->verification($_GET['mission'])) {
	$mission = $porte->informations($_GET['mission']);
} else {
	$core->goTo('porte', true);
}
$core->loadHeader(); ?>
	<h2>Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>
	
	<ul class="listeImmeubles">
		<?php 
		if ($porte->nombreVisites($mission['mission_id'])) :
		$rues = $porte->liste($mission['mission_id']);
		foreach ($rues as $rue => $immeubles) : 
		if (md5($rue) == $_GET['rue']) : $nomRue = $carto->afficherRue($rue, true);
		$idImmeubles = array();
		foreach ($immeubles as $key => $immeuble) {
			$immeubles[$key] = $carto->afficherImmeuble($immeuble, true);
			$idImmeubles[$immeubles[$key]] = $immeuble;
		}
		natsort($immeubles);
		foreach ($immeubles as $immeuble) : ?>
		<a class="nostyle" href="<?php $core->goTo('porte', array('mission' => $_GET['mission'], 'immeuble' => md5($idImmeubles[$immeuble]))); ?>">
			<li id="element-<?php echo md5($idImmeubles[$immeuble]); ?>">
				<span><?php if (!empty($immeuble)) { echo $immeuble; } else { echo '&nbsp;';	 } ?></span> <?php echo $nomRue; ?>
			</li>
		</a>
		<?php endforeach; endif; endforeach; else : ?>
		<li class="vide">
			<p>Aucun immeuble à visiter</p>
		</li>
		<?php endif; ?>
	</ul>
<?php $core->loadFooter(); ?>