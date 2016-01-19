<?php
    // On protège l'accès aux administrateurs uniquement
    User::protection(5);
    
    // On charge le template de header
    Core::tpl_header();
?>
<h2>Nouvelle mission de porte-à-porte</h2>

<form action="ajax.php?script=porte-creation" method="post">
	<div class="colonne demi gauche">
		<section id="porte-nouveau" class="contenu demi">
			<ul class="formulaire">
				<li>
					<label for="form-nom">Quel est le nom de votre campagne ?</label>
					<span class="form-icon tag"><input type="text" name="nom" id="form-nom" placeholder="Qu'allez-vous diffuser ?"></span>
				</li>
				<li>
					<label for="form-nom">Quel est le responsable de cette mission ?</label>
					<span class="form-icon tag"><input type="text" name="resp" id="form-responsable" placeholder="Sélectionnez un compte leQG"><input type="hidden" id="responsable" name="responsable"></span>
				</li>
				<li>
					<label for="form-date">Quelle est la date limite de cette mission ?</label>
					<span class="form-icon tag"><input type="text" name="date" id="form-date" placeholder="format jj/mm/aaaa"></span>
				</li>
				<li>
					<input type="submit" value="Créer la mission" class="flat">
				</li>
			</ul>
		</section>
	</div>
	
	<div class="colonne demi droite">
		<section id="porte-aside" class="contenu demi invisible">
			<ul class="formulaire" id="choixResponsable">
				<li>
					<label>Faites le choix d'un responsable</label>
        <?php $comptes = User::all(5); foreach($comptes as $compte) : ?>
					<div class="radio"><input type="radio" name="selectResponsable" class="radioResponsable" id="selectResponsable-<?php echo $compte['id']; ?>" value="<?php echo $compte['id']; ?>" data-nom="<?php echo $compte['firstname']; ?> <?php echo $compte['lastname']; ?>"><label for="selectResponsable-<?php echo $compte['id']; ?>"><span><span></span></span><?php echo $compte['firstname']; ?> <?php echo $compte['lastname']; ?></label></div>
        <?php endforeach; ?>
				</li>
			</ul>
		</section>
	</div>
</form>
<?php Core::tpl_footer(); ?>