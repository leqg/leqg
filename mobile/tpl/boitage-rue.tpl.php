<?php 
if (isset($_GET['mission']) && $boitage->verification($_GET['mission'])) {
	$mission = $boitage->informations($_GET['mission']);
} else {
	$core->tpl_go_to('boitage', true);
}
$core->tpl_header(); ?>
	<h2>Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>
	
	<ul class="listeImmeubles">
		<?php 
		if ($boitage->nombreImmeubles($mission['mission_id'])) :
		$rues = $boitage->liste($mission['mission_id']);
		foreach ($rues as $rue => $immeubles) : 
		if (md5($rue) == $_GET['rue']) : $nomRue = $carto->afficherRue($rue, true);
		$idImmeubles = array();
		foreach ($immeubles as $key => $immeuble) {
			$immeubles[$key] = $carto->afficherImmeuble($immeuble, true);
			$idImmeubles[$immeubles[$key]] = $immeuble;
		}
		natsort($immeubles);
		foreach ($immeubles as $immeuble) : ?>
		<li id="element-<?php echo md5($idImmeubles[$immeuble]); ?>">
			<span><?php echo $immeuble; ?></span> <?php echo $nomRue; ?>
			<button class="choix" data-immeuble="<?php echo $idImmeubles[$immeuble]; ?>">&#xe908;</button>
			<button class="report report-<?php echo $idImmeubles[$immeuble]; ?>" data-statut="2" data-type="boitage" data-immeuble="<?php echo md5($idImmeubles[$immeuble]); ?>" data-mission="<?php echo $_GET['mission']; ?>">&#xe812;</button>
			<button class="report report-<?php echo $idImmeubles[$immeuble]; ?>" data-statut="1" data-type="boitage" data-immeuble="<?php echo md5($idImmeubles[$immeuble]); ?>" data-mission="<?php echo $_GET['mission']; ?>">&#xe813;</button>
		</li>
		<?php endforeach; endif; endforeach; else : ?>
		<li class="vide">
			<p>Aucun immeuble à visiter</p>
		</li>
		<?php endif; ?>
	</ul>
<?php $core->tpl_footer(); ?>