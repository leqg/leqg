<?php $destination = (isset($_GET['destination'])) ? $_GET['destination'] : null; ?>
<form action="<?php $core->tpl_go_to('resultats', array('destinataire' => $destination)); ?>" method="post" class="form-simple">
	<label for="form-recherche">Contact recherché</label>
	<input type="search" name="recherche" id="form-recherche" placeholder="Nom et prénoms">
	<input type="submit" value="Rechercher">
</form>