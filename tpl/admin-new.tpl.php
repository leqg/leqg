<?php
    // On protège la page
    User::protection(5);

    Core::loadHeader();
?>
<a href="<?php Core::goPage('administration'); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Retour à la liste</button></a>	

<h2>Création d'un nouveau compte</h2>

<form action="<?php Core::goPage('administration', array('action' => 'creation')); ?>" method="post">
	
	<div class="colonne demi gauche">
    	<section class="contenu demi">
        	<h4>Informations génériques</h4>
        	
        	<ul class="formulaire">
            	<li>
            	    <label class="small">Prénom</label>
            	    <span class="form-icon nom decalage"><input type="text" name="prenom"></span>
            	</li>
            	
            	<li>
            	    <label class="small">Nom</label>
            	    <span class="form-icon nom decalage"><input type="text" name="nom"></span>
            	</li>
            	
            	<li>
            	    <label class="small">Email</label>
             	    <span class="form-icon email decalage"><input type="text" name="email"></span>
                </li>
        	</ul>
	
            <button type="submit" style="margin-bottom: 1.15em;">Valider la création</button>
            
            <p style="color: rgb(125, 125, 125); margin-bottom: .15em; text-align: center;">Un mot de passe sera envoyé par mail à l'utilisateur.</p>
    	</section>
	</div>
	
	<div class="colonne demi droite">
    	<section class="contenu demi">
        	<h4>Choisir le niveau d'autorisation</h4>
        	
			<ul class="formulaire" id="choixAuth">
				<li>
					<div class="radio">
    					<input type="radio" name="selectAuth" class="selectAuth" id="selectAuth-admin" value="8" data-nom="Administrateur">
    					<label for="selectAuth-admin"><span><span></span></span>Administrateur</label>
                    </div>
					<div class="radio">
    					<input type="radio" name="selectAuth" class="selectAuth" id="selectAuth-militant" checked value="3" data-nom="Militant">
    					<label for="selectAuth-militant"><span><span></span></span>Militant</label>
                    </div>
				</li>
			</ul>
    	</section>
	</div>
	
</form>
<?php Core::loadFooter(); ?>