<?php
	// On récupère les données POST
	$ville = $_POST['ville'];
	$rue = $_POST['rue'];
	$immeuble = $_POST['immeuble'];
	
	// On effectue la recherche
	$rues = $carto->recherche_rue($ville , $rue);
	
	foreach ($rues as $rue) :
?>
	<a href="<?php $core->tpl_go_to('fiche', array('id' => $_POST['fiche'])); ?>&modifierImmeuble=true&rue=<?php echo $rue['id']; ?>&ville=<?php echo $ville; ?>" class="nostyle"><li class="rue" data-ville="<?php echo $ville; ?>" data-rue="<?php echo $rue['id']; ?>" data-fiche="<?php echo $_POST['fiche']; ?>"><strong><?php echo $rue['nom']; ?></strong></li></a>
<?php endforeach; ?>