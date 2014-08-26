<?php
$query = 'SELECT * FROM reglages WHERE nom = "email-expediteur"';
$sql = $db->query($query);
$expediteur = $sql->fetch_assoc();
$expediteur = $expediteur['valeur'];

$query = 'SELECT * FROM reglages WHERE nom = "email-expediteur-adresse"';
$sql = $db->query($query);
$expediteur_adresse = $sql->fetch_assoc();
$expediteur_adresse = $expediteur_adresse['valeur'];
?>
<section id="fiche">
	<header class="reglages">
		<h2>
			Réglages du module Email
		</h2>
	</header>
	
	<form action="ajax.php?script=email-reglages" method="post">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information"><label for="expediteur">Nom de l'expéditeur</label></span>
				<input type="text" name="expediteur" id="expediteur" value="<?php echo $expediteur; ?>">
			</li>
			<li>
				<span class="label-information"><label for="expediteur-email">Adresse email de l'expéditeur</label></span>
				<input type="text" name="expediteur-email" id="expediteur-email" value="<?php echo $expediteur_adresse; ?>">
			</li>
			<li class="submit">
				<input type="submit" value="Sauvegarder les préférences">
			</li>
		</ul>
	</form>
</section>