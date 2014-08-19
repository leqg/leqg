<div id="changementNaissance">
	<nav class="navigationFiches">
		<a class="retour" href="<?php $core->tpl_go_to('fiche', array('id' => $fiche->get_the_id())); ?>">Retour à la fiche</a>
	</nav>
	
	<h6>Modification des informations de naissance</h6>
	
	<form action="ajax.php?script=modifier-naissance&fiche=<?php echo $_GET['id']; ?>" method="post">
	<input type="hidden" id="villeChoisieAuFinal" name="ville">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Date de naissance</span>
				<input type="text" name="dateNaissance" id="dateNaissance" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" placeholder="jj/mm/aaaa" value="<?php if ($fiche->is_info('naissance_date')) : $fiche->date_naissance('/'); endif; ?>">
			</li>
			<li>
				<span class="label-information">Ville de naissance</span>
				<input type="text" name="villeNaissance" id="villeNaissance">
			</li>
			<li class="choix" id="resultats">
				<span class="label-information">Choisir</span>
				<ul id="liste-villeNaissance" class="listeEncadree"></ul>
			</li>
			<li id="choixDeVilleDeNaissance">
				<span class="label-information">Ville choisie :</span>
				<ul id="villeChoisie" class="listeEncadree">
					<li class="ville">
						<strong id="nomVilleChoisie"></strong>
					</li>
				</ul>
			</li>
			<li class="submit">
				<input type="submit" value="Confirmer ces informations">
			</li>
		</ul>
	</form>
</div>
