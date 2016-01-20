<?php 
if (isset($_GET['mission']) && $porte->verification($_GET['mission'])) {
    $mission = $porte->informations($_GET['mission']);
} else {
    $core->goTo('porte', true);
}
$core->loadHeader(); ?>
	<h2>Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>
	
	<ul class="listeMissions">
    <?php 
    if ($porte->nombreVisites($mission['mission_id'])) :
        $rues = $porte->liste($mission['mission_id']);
        foreach ($rues as $rue => $immeubles) :  ?>
        <a href="<?php $core->goTo('porte', array('mission' => $_GET['mission'], 'rue' => md5($rue))); ?>" class="nostyle">
         <li class="rue">
          <h4><?php $carto->afficherRue($rue); ?></h4>
          <p><strong><?php echo count($immeubles); ?></strong> immeubles à visiter.</p>
         </li>
        </a>
        <?php endforeach; else : ?>
		<li class="vide">
			<p>Aucun immeuble à visiter</p>
		</li>
        <?php endif; ?>
	</ul>
<?php $core->loadFooter(); ?>