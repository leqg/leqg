<?php
    User::protection(5);
    Core::loadHeader();
    
    $destination = (isset($_GET['destination'])) ? $_GET['destination'] : null;
?>
<form action="<?php Core::goTo('resultats', array('destination' => $destination)); ?>" method="post" class="form-simple">
	<h2 style="margin-bottom: 2em;">Recherche de contact</h2>
	<input type="search" name="recherche" id="form-recherche" placeholder="Nom et prénoms" autocomplete="off">
	<input type="submit" value="Rechercher">
</form>
<?php Core::loadFooter(); ?>