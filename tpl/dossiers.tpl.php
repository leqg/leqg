<?php 
    // On protège la page
    User::protection(5);
    
    // On charge le template
    Core::loadHeader(); 
?>
	
	<h2>Dossiers en cours</h2>
	
	<div class="colonne demi gauche">
        <?php
        // On récupère la liste des dossiers ouverts
        $dossiers = Folder::all();
            
        if (count($dossiers) > 0) :
        ?>
       <section id="dossiers">
        <ul class="liste-dossiers">
        <?php
         // On fait une boucle des dossiers ouverts
        foreach ($dossiers as $dossier) :
        ?>
        <li>
    				<a href="<?php Core::goTo('dossier', array('dossier' => $dossier['id'])); ?>" class="nostyle"><h4><?php echo $dossier['name']; ?></h4></a>
    				<p><?php echo $dossier['desc']; ?></p>
        </li>
        <?php endforeach; ?>
        </ul>
       </section>
        <?php else: ?>
    	<section class="icone" id="aucunDossier">
    		<h3>Aucun dossier ouvert actuellement.</h3>
    	</section>
        <?php endif; ?>
	</div>
	
	<div class="colonne demi droite">
	    <section class="contenu demi">
    	    <h4>Recherche thématique</h4>
    	    
    	    <form action="<?php echo Core::goTo('recherche-thematique'); ?>" method="post">
        	    <ul class="formulaire">
            	    <li>
            	        <span class="form-icon decalage search">
                	        <input type="text" name="rechercheThematique" id="rechercheThematique" placeholder="Tapez ici un tag à rechercher">
            	        </span>
            	    </li>
            	    <li>
            	        <button type="submit" style="margin: .33em auto .15em;">Lancer la recherche</button>
            	    </li>
        	    </ul>
    	    </form>
	    </section>
	</div>
	
<?php Core::loadFooter(); ?>