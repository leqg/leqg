<?php

	$info = $_GET;
	
	$tache->fermetureTache($info['tache']);
	
	$core->tpl_go_to('fiche', array('id' => $info['fiche'], 'interaction' => $info['interaction']), true);
	
?>