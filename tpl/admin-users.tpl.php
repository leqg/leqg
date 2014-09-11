<section id="administration">
	<h2>Passez en revue toute votre équipe</h2>
	
	<ul class="listeUtilisateurs">
	<?php $utilisateurs = $user->liste(); foreach ($utilisateurs as $utilisateur) : ?><!--
	 --><li>
	 		<img src="<?php echo $user->gravatar($utilisateur['email'], 100, 404); ?>" alt="&#xe80a;" class="actif" title="Avatar de <?php echo $utilisateur['firstname'] . ' ' . $utilisateur['lastname']; ?>">
			<h3><?php echo $utilisateur['firstname'] . ' ' . $utilisateur['lastname']; ?></h3>
			<p><?php $user->status($utilisateur['auth']); ?></p>
			<ul></ul>
		</li><?php endforeach; ?><!--
	 --><li id="ajoutCompte">
	 		<span class="avatar">&#xe80c;</span>
	 		<h3>Ajouter un compte</h3>
	 	</li>
	</ul>
	
	<div id="creationCompte" class="overlayForm">
		<form method="post" action="ajax.php?script=user-creation">
			<a class="fermetureOverlay" href="#">&#xe813;</a>
			<h3>Ajout d'un nouveau utilisateur</h3>
			<ul>
				<li>
					<label for="form-firstname">Prénom</label>
					<input type="text" name="firstname" id="form-firstname" required autocomplete="off">
				</li>
				<li>
					<label for="form-lastname">Nom</label>
					<input type="text" name="lastname" id="form-lastname" required autocomplete="off">
				</li>
				<li>
					<label for="form-email">Adresse email</label>
					<input type="email" name="email" id="form-email" required autocomplete="off">
				</li>
				<li>
					<label>Autorisations</label>
					<div class="radio"><input type="radio" name="auth" id="auth-8" value="8"><label for="auth-8"><span><span></span></span>Administrateur</label></div>
					<div class="radio"><input type="radio" name="auth" id="auth-5" value="5"><label for="auth-5"><span><span></span></span>Équipe salariée</label></div>
					<div class="radio"><input type="radio" name="auth" id="auth-1" value="1"><label for="auth-1"><span><span></span></span>Militant</label></div>
				</li>
				<li>
					<input type="submit" value="Créer le compte">
				</li>
			</ul>
		</form>
	</div>
</section>