<?php 
if (isset($_GET['mission']) && $porte->verification($_GET['mission'])) {
    $mission = $porte->informations($_GET['mission']);
} else {
    $core->tpl_go_to('porte', true);
}
$core->tpl_header(); ?>
	<h2>Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>
	
	<ul class="listeImmeubles">
    <?php 
    if ($porte->nombreVisites($mission['mission_id'])) :
        $electeurs = $porte->electeurs($_GET['mission'], $_GET['immeuble']);
        foreach ($electeurs as $electeur) : ?>
        <li id="element-<?php echo md5($electeur['contact_id']); ?>">
            <?php echo strtoupper($electeur['contact_nom']) . ' ' . strtoupper($electeur['contact_nom_usage']) . ' ' . ucwords(strtolower($electeur['contact_prenoms'])); ?>
         <button class="choix" data-type="porte" data-electeur="<?php echo md5($electeur['contact_id']); ?>">&#xe908;</button>
         <button class="report report-<?php echo md5($electeur['contact_id']); ?>" data-statut="2" data-type="porte" data-electeur="<?php echo md5($electeur['contact_id']); ?>" data-mission="<?php echo $_GET['mission']; ?>">&#xe812;</button>
         <button class="report report-<?php echo md5($electeur['contact_id']); ?>" data-statut="1" data-type="porte" data-electeur="<?php echo md5($electeur['contact_id']); ?>" data-mission="<?php echo $_GET['mission']; ?>">&#xe813;</button>
        </li>
        <?php endforeach; else : ?>
		<li class="vide">
			<p>Aucun électeur à visiter</p>
		</li>
        <?php endif; ?>
	</ul>
<?php $core->tpl_footer(); ?>