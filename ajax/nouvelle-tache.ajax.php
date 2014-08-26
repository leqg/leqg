<?php

	// On recueille les informations
	$info = $_POST;
	
	// On retraite les infos
	if (empty($info['dossier'])) $info['dossier'] = null;
	if (empty($info['interaction'])) $info['interaction'] = null;
	$info['description'] = $core->securisation_string($info['description']);
	if (empty($info['destinataire'])) $info['destinataire'] = null;
	$date = explode('/', $info['deadline']); krsort($date);
	$info['deadline'] = implode('-', $date);
	
	$tache->creation($info);
	
	$core->tpl_go_to('fiche', array('id' => $_POST['fiche'], 'interaction' => $_POST['interaction']), true);

?>