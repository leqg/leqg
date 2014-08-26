<?php
$query = 'SELECT * FROM reglages WHERE nom = "sms-expediteur"';
$sql = $db->query($query);
$expediteur = $sql->fetch_assoc();
$expediteur = $expediteur['valeur'];
?>
<section id="fiche">
	<header class="reglages">
		<h2>
			Réglages du module SMS
		</h2>
	</header>
	
	<form action="ajax.php?script=sms-reglages" method="post">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="expediteur">Nom de l'expéditeur</label></span>
				<input type="text" name="expediteur" id="expediteur" value="<?php echo $expediteur; ?>" placeholder="11 caractères max." maxlength="11">
			</li>
			<li class="submit">
				<input type="submit" value="Sauvegarder les préférences">
			</li>
		</ul>
	</form>
</section>