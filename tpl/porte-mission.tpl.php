<?php
	$parcours = $mission->chargement($_GET['mission']);
	$immeubles = explode(',', $parcours['immeubles']);
	
	$electeurs = explode(',', $parcours['a_faire']);
	$immeubles_a_faire = array();
	$building = array(); // tableau de correspondance des électeurs et des bâtiments
	
	foreach ($electeurs as $electeur) {
		$query = 'SELECT immeuble_id FROM contacts WHERE contact_id = ' . $electeur;
		$sql = $db->query($query);
		$row = $sql->fetch_assoc();
		
		$building[$row['immeuble_id']][] = $electeur;
		
		if (!in_array($row['immeuble_id'], $immeubles_a_faire)) $immeubles_a_faire[] = $row['immeuble_id'];
	}
?>
<section id="fiche">
	<header class="porte">
		<h2>
			<span>Porte-à-porte</span>
			<span>Mission <?php echo $parcours['id']; ?></span>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Ville</span>
			<p><?php echo $carto->afficherVille($parcours['ville_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Rue</span>
			<p><?php echo $carto->afficherRue($parcours['rue_id']); ?></p>
		</li>
		<li>
			<span class="label-information">Immeubles</span>
			<ul class="listeEncadree">
				<?php  foreach ($immeubles as $immeuble) : ?>
				<a href="<?php $core->tpl_go_to('porte', array('action' => 'mission', 'mission' => $_GET['mission'], 'immeuble' => $immeuble)); ?>">
					<li class="immeuble <?php echo (in_array($immeuble, $immeubles_a_faire)) ? 'afaire' : 'fait'; ?>">
						<strong><?php $carto->afficherImmeuble($immeuble); ?> <?php $carto->afficherRue($parcours['rue_id']); ?></strong>
					</li>
				</a>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</section>

<?php if (isset($_GET['immeuble'])) : ?>
	<aside>
		<div>
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('porte', array('action' => 'mission', 'mission' => $_GET['mission'])); ?>">Quitter l'adresse</a>
			</nav>
			
			<?php $immeuble = $carto->immeuble($_GET['immeuble']); $electeurs = $building[$immeuble['id']]; ?>
			<h6><?php echo $immeuble['numero']; ?> <?php $carto->afficherRue($immeuble['rue_id']); ?></h6>
			
			<form action="ajax.php?script=porte-reporting&mission=<?php echo $_GET['mission']; ?>" method="post">
				<ul class="listeEncadree">
					<?php foreach ($electeurs as $electeur) : ?>
					<li class="electeur">
						<strong><?php $fiche->nomByID($electeur); ?></strong>
						<ul class="boutonsRadio"><!--
						 --><label for="vu-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="2" id="vu-electeur-<?php echo $electeur; ?>"> Vu</li><!--
						 --><label for="absent-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="1" id="absent-electeur-<?php echo $electeur; ?>"> Absent</li><!--
						 --><label for="afaire-electeur-<?php echo $electeur; ?>"><li><input type="radio" name="<?php echo $electeur; ?>" value="0" id="afaire-electeur-<?php echo $electeur; ?>" checked> À faire</li><!--
					 --></ul>
					</li>
					<?php endforeach; ?>
				</ul>
				<ul class="deuxColonnes" style="padding-left: 0px;">
					<li class="submit">
						<input type="submit" value="Valider le rapport">
					</li>
				</ul>
			</form>
		</div>
	</aside>
<?php else: ?>
	<aside>
		<div>
			<nav class="navigationFiches">
				<a class="retour" href="<?php $core->tpl_go_to('porte', array('action' => 'missions')); ?>">Retour aux missions</a>
			</nav>
		</div>
	</aside>	
<?php endif; ?>