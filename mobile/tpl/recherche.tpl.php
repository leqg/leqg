<?php $destination = (isset($_GET['destination'])) ? $_GET['destination'] : null; ?>
<form action="<?php $core->tpl_go_to('resultats', array('destinataire' => $destination)); ?>" method="post" class="form-simple">
	<h2 style="margin-bottom: 2em;">Recherche de contact</h2>
	<input type="search" name="recherche" id="form-recherche" placeholder="Nom et prénoms" autocomplete="off">
	<input type="submit" value="Rechercher">
</form>