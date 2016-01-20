<?php
    // On protège la page
    User::protection(5);
    
    // On commence par vérifier qu'il existe bien une mission, et si oui on l'affiche
if (isset($_GET['mission']) && Boite::verification($_GET['mission'])) {
    $mission = Boite::informations($_GET['mission'])[0];
        
    // On ouvre la mission
    $data = new Mission($_GET['mission']);
        
    Core::loadHeader();
} else {
    Core::goTo('boite', true);
}
?>

<h2 id="titre-mission" class="titre" data-mission="<?php echo md5($mission['mission_id']); ?>">Boîtage &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
    <section id="boitage-vide" class="contenu demi icone rue <?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; 
   } ?>">
    	<h3>Aucun immeuble à visiter actuellement.</h3>
    	<div class="coteAcote">
    		<button class="ajouterRue">Ajouter une rue</button>
    		<button class="ajouterBureau">Ajouter un bureau</button>
    	</div>
    </section>
    
    <section id="boitage-afaire" class="contenu demi <?php if (!Boite::nombreImmeubles($mission['mission_id'], 0)) { echo 'invisible'; 
   } ?>">
    	<div class="coteAcote haut">
    		<button class="ajouterRue">Ajouter une rue</button>
    		<button class="ajouterBureau">Ajouter un bureau</button>
    	</div>
    
    	<h4>Rues au sein de cette mission</h4>
    	
        <?php $rues = Boite::liste($mission['mission_id']); ?>
    	<ul class="form-liste" id="listeDesRues">
        <?php
        // On met en place un tri des rues à partir de leur nom
        $indexRues = array();
        foreach ($rues as $rue => $immeubles) { $indexRues[$rue] = Carto::afficherRue($rue, true); 
        }
        natsort($indexRues);
                
        foreach ($indexRues as $rue => $nom) {
        ?>
       <li id="immeubles-rue-<?php $rue; ?>">
        <button class="voirRue gris" data-rue="<?php echo $rue; ?>" data-nom="<?php echo $nom; ?>">Consulter</button>
        <span><?php echo $nom; ?></span>
        <span><?php Carto::afficherVille(Carto::villeParRue($rue)); ?></span>
       </li>
        <?php } ?>
    	</ul>
    </section>

	<section class="contenu demi">
    	<button class="deleting long supprimerMission" style="margin: .25em auto .15em;">Clôture de la mission</button>
	</section>
</div>

<div class="colonne demi droite">
    <?php if (Boite::nombreImmeubles($mission['mission_id'], 1)) { ?>
    	<section id="boitage-statistiques" class="contenu demi">
    <?php
                $nombre['attente']     = $data->nombre_immeubles(0);
                $nombre['impossible']  = $data->nombre_immeubles(1);
                $nombre['realise']     = $data->nombre_immeubles(2);
                $nombre['total']       = array_sum($nombre);
                $nombre['fait']        = $nombre['total'] - $nombre['attente'];
                
    function pourcentage( $actu , $total ) 
    {
        $pourcentage = $actu * 100 / $total;
        $pourcentage = str_replace(',', '.', $pourcentage);
        return $pourcentage;
    }
    ?>
			
			<h4>Avancement de la mission</h4>
			<div id="avancementMission"><!--
			 --><div class="ouvert" style="width: <?php echo pourcentage($nombre['realise'], $nombre['total']); ?>%;"><span>Boîtage&nbsp;réalisé</span></div><!--
			 --><div class="npai" style="width: <?php echo pourcentage($nombre['impossible'], $nombre['total']); ?>%;"><span>Boîtage&nbsp;impossible</span></div><!--
		 --></div>
    		
    		<h4>Statistiques</h4>
    		<ul class="statistiquesMission">
    			<li>Mission réalisée à <strong><?php echo ceil(pourcentage($nombre['fait'], $nombre['total'])); ?></strong>&nbsp;%</li>
    			<li><strong><?php echo number_format($nombre['total'], 0, ',', ' '); ?></strong>&nbsp;immeubles concernés par cette mission</li>
    			<li>Il reste <strong><?php echo number_format($nombre['attente'], 0, ',', ' '); ?></strong>&nbsp;immeubles à boiter.</li>
    		</ul>
    	</section>
    <?php } else { ?>
    	<section id="boitage-statistiques" class="contenu demi icone fusee">
    		<h3>La mission n'a pas été commencée.</h3>
        <?php if (Boite::nombreImmeubles($mission['mission_id'], 0)) { ?>
    			<h5>Il existe <span><?php echo Boite::estimation($mission['mission_id']); ?></span> électeurs à boiter.</h5>
        <?php } ?>
    	</section>
    <?php } ?>
	
	<section class="contenu demi">
		<h4>Militants inscrits à cette mission</h4>
		
		<ul class="listeContacts">
    <?php $comptes = Porte::inscriptions($mission['mission_id']); if (count($comptes)) : foreach($comptes as $compte) : ?>
			<li class="contact homme"><?php echo User::get_login_by_ID($compte['user_id']); ?></li>
    <?php endforeach; else : ?>
			<li class="contact homme">Aucune inscription actuellement.</li>
    <?php endif; ?>
		</ul>
	</section>
    
    <section id="ajoutRue" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Recherchez une rue</label>
    			<span class="form-icon street"><input type="text" name="rechercheRue" id="rechercheRue" placeholder="rue du Marché"></span>
    		</li>
    	</ul>
    	<ul class="form-liste invisible" id="listeRues"></ul>
    </section>
    
    <section id="ajoutBureau" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Recherchez un bureau de vote</label>
    			<span class="form-icon street"><input type="text" name="rechercheBureau" id="rechercheBureau" placeholder="103 ou École des Champs"></span>
    		</li>
    	</ul>
    	<ul class="form-liste invisible" id="listeBureaux"></ul>
    </section>
    
    <section id="choixImmeuble" class="contenu demi invisible">
    	<ul class="formulaire">
    		<li>
    			<label>Sélectionnez des immeubles</label>
    			<span class="form-icon street"><input type="text" name="rueSelectionImmeuble" id="rueSelectionImmeuble" value=""></span>
    		</li>
    	</ul>
    	
    	<ul class="form-liste">
    		<li>
    			<button class="ajouterLaRue" id="rueEntiere" data-rue="" data-mission="<?php echo $_GET['mission']; ?>">Ajouter</button>
    			<span class="immeuble-numero">Ajouter tous les immeubles de la rue</span>
    			<span class="nombre-electeurs"></span>
    		</li>
    	</ul>
    </section>
    
    <section id="listeImmeublesParRue" class="contenu demi invisible">
    	<h4 class="nomRue">Immeubles restant à boiter <strong><span></span></strong></h4>
    	
    	<ul class="form-liste"></ul>
    </section>
</div>