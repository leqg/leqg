<?php 
if (isset($_GET['mission']) && $boitage->verification($_GET['mission'])) {
    $mission = $boitage->informations($_GET['mission']);
} else {
    $core->goTo('boitage', true);
}
$core->loadHeader(); ?>
	<h2>Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>
	
	<ul class="listeMissions">
    <?php 
    if ($boitage->nombreImmeubles($mission['mission_id'])) :
        $rues = $boitage->liste($mission['mission_id']);
        foreach ($rues as $rue => $immeubles) :  ?>
        <a href="<?php $core->goTo('boitage', array('mission' => $_GET['mission'], 'rue' => md5($rue))); ?>" class="nostyle">
         <li class="rue">
          <h4><?php $carto->afficherRue($rue); ?></h4>
          <p><strong><?php echo count($immeubles); ?></strong> immeubles à boîter.</p>
         </li>
        </a>
        <?php endforeach; else : ?>
		<li class="vide">
			<p>Aucun immeuble à visiter</p>
		</li>
        <?php endif; ?>
	</ul>
<?php $core->loadFooter(); ?>