<?php

	// On charge le header de la page
	$core->tpl_header();
	
	// On charge les dernières interactions ayant eu lieu sur la page
	$core->tpl_load('contact', 'interactions');

	// On charge le aside de la page contacts
	$core->tpl_load('aside', 'contacts');

	// On charge le footer de la page
	$core->tpl_footer();
	
?>