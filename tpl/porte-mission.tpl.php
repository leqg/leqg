<?php
    // On protège la page
    User::protection(5);
    
    // On commence par vérifier qu'il existe bien une mission, et si oui on l'affiche
if (isset($_GET['mission']) && Porte::verification($_GET['mission'])) {
    $mission = Porte::informations($_GET['mission'])[0];
        
    // On ouvre la mission
    $data = new Mission($_GET['mission']);
        
    Core::loadHeader();
} else {
    Core::goPage('porte', true);
}
?>

<h2 id="titre-mission" class="titre" data-mission="<?php echo md5($mission['mission_id']); ?>">Porte-à-porte &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

<!-- Blocs de mission vide -->
<div class="colonne demi gauche">
	<section id="porte-vide" class="icone rue contenu demi <?php if (Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; 
} ?>">
		<h3>Aucun immeuble à visiter actuellement.</h3>
		<div class="coteAcote">
			<button class="ajouterRue">Ajouter rue</button>
			<button class="ajouterBureau">Ajouter bureau</button>
		</div>
	</section>
	
	<section id="porte-afaire" class="contenu demi <?php if (!Porte::nombreVisites($mission['mission_id'], 0)) { echo 'invisible'; 
} ?>">
		<div class="coteAcote haut">
			<button class="ajouterRue">Ajouter rue</button>
			<button class="ajouterBureau">Ajouter bureau</button>
		</div>
	
		<h4>Rues au sein de cette mission</h4>
		
    <?php $rues = Porte::liste($mission['mission_id']);?>
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
	
    <?php if (count($indexRues) >= 20) { ?>
			<div class="coteAcote">
				<button class="ajouterRue">Ajouter rue</button>
				<button class="ajouterBureau">Ajouter bureau</button>
			</div>
    <?php } ?>
	</section>
	
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
	
	<section class="contenu demi">
    	<button class="deleting long supprimerMission" style="margin: .25em auto .15em;">Clôture de la mission</button>
	</section>
</div>

<div class="colonne demi droite">
    <?php if (Porte::nombreVisites($mission['mission_id'], 1)) { ?>
		<section id="porte-statistiques" class="contenu demi">
    <?php
                $nombre['attente']     = $data->contactsCount(0);
                $nombre['absent']      = $data->contactsCount(1);
                $nombre['ouvert']      = $data->contactsCount(2);
                $nombre['procuration'] = $data->contactsCount(3);
                $nombre['contact']     = $data->contactsCount(4);
                $nombre['npai']        = $data->contactsCount(-1);
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
			 --><div class="fait" style="width: <?php echo pourcentage($nombre['fait'], $nombre['total']); ?>%;"><span>Portion&nbsp;réalisée&nbsp;de&nbsp;la&nbsp;mission</span></div><!--
		 --></div>
			
			<h4>Détail des portes frappées</h4>
			<div id="avancementMission"><!--
			 --><div class="ouvert" style="width: <?php echo pourcentage($nombre['ouvert'], $nombre['fait']); ?>%;"><span>Portes&nbsp;ouvertes</span></div><!--
			 --><div class="procuration" style="width: <?php echo pourcentage($nombre['procuration'], $nombre['fait']); ?>%;"><span>Procurations&nbsp;demandées</span></div><!--
			 --><div class="contact" style="width: <?php echo pourcentage($nombre['contact'], $nombre['fait']); ?>%;"><span>Contact&nbsp;souhaité</span></div><!--
			 --><div class="absent" style="width: <?php echo pourcentage($nombre['absent'], $nombre['fait']); ?>%;"><span>Contact&nbsp;absent</span></div><!--
			 --><div class="npai" style="width: <?php echo pourcentage($nombre['npai'], $nombre['fait']); ?>%;"><span>Adresse&nbsp;erronée</span></div><!--
		 --></div>
			
			<h4>Statistiques</h4>
			<ul class="statistiquesMission">
				<li>Mission réalisée à <strong><?php echo ceil(pourcentage($nombre['fait'], $nombre['total'])); ?></strong>&nbsp;%</li>
				<li><strong><?php echo number_format($nombre['total'], 0, ',', ' '); ?></strong>&nbsp;électeurs concernés par cette mission</li>
				<li>Il reste <strong><?php echo number_format($nombre['attente'], 0, ',', ' '); ?></strong>&nbsp;électeurs à visiter.</li>
			</ul>
			
			<a href="<?php echo Core::goPage('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Reporting de la mission</button></a>
		</section>
    <?php } else { ?>
		<section id="porte-statistiques" class="icone fusee contenu demi">
			<h3>La mission n'a pas été commencée.</h3>
    <?php if (Porte::nombreVisites($mission['mission_id'], 0)) { ?>
				<h5>Il existe <span><?php echo number_format(Porte::estimation($mission['mission_id']), 0, ',', ' '); ?></span> électeurs à visiter.</h5>
    <?php } ?>
			
			<a href="<?php echo Core::goPage('porte', array('reporting' => md5($mission['mission_id']))); ?>" class="nostyle"><button class="long" style="margin: 2.5em auto .33em;">Reporting de la mission</button></a>
		</section>
    <?php } ?>
	
    <?php if ($data->procurationsNumber()) : ?>
	<section id="procurations" class="contenu demi">
		<h4>Électeurs demandant une procuration</h4>
		
		<ul class="listeContacts">
    <?php
                // On fait la liste des contacts concernés
                $contacts = $data->contactsList(3);
    foreach ($contacts as $contact) :
        // On ouvre la fiche du contact concerné
        $fiche = new Contact(md5($contact[0]));
                    
        if ($fiche->get('contact_sexe') == 'M') { $sexe = 'homme'; 
        }
        elseif ($fiche->get('contact_sexe') == 'F') { $sexe = 'femme'; 
        }
        else { $sexe = 'isexe'; 
        }
                    
        if (!empty($fiche->get('nom_affichage'))) { $nomAffichage = $fiche->get('nom_affichage'); 
        }
        elseif (!empty($fiche->get('contact_organisme'))) { $nomAffichage = $fiche->get('contact_organisme'); 
        }
        else { $nomAffichage = 'Fiche sans nom'; 
        }
    ?>
			<a href="<?php Core::goPage('contact', array('contact' => md5($fiche->get('contact_id')))); ?>" class="nostyle contact-<?php echo $fiche->get('contact_id'); ?>">
				<li class="contact <?php echo $sexe; ?>">
<strong><?php echo $nomAffichage; ?></strong>
				</li>
			</a>
    <?php endforeach; ?>
		</ul>
	</section>
    <?php endif; ?>
	
    <?php if ($data->newContactsNumber()) : ?>
	<section id="procurations" class="contenu demi">
		<h4>Électeurs souhaitant être recontactés</h4>
		
		<ul class="listeContacts">
    <?php
                // On fait la liste des contacts concernés
                $contacts = $data->contactsList(4);
    foreach ($contacts as $contact) :
        // On ouvre la fiche du contact concerné
        $fiche = new Contact(md5($contact[0]));
                    
        if ($fiche->get('contact_sexe') == 'M') { $sexe = 'homme'; 
        }
        elseif ($fiche->get('contact_sexe') == 'F') { $sexe = 'femme'; 
        }
        else { $sexe = 'isexe'; 
        }
                    
        if (!empty($fiche->get('nom_affichage'))) { $nomAffichage = $fiche->get('nom_affichage'); 
        }
        elseif (!empty($fiche->get('contact_organisme'))) { $nomAffichage = $fiche->get('contact_organisme'); 
        }
        else { $nomAffichage = 'Fiche sans nom'; 
        }
    ?>
			<a href="<?php Core::goPage('contact', array('contact' => md5($fiche->get('contact_id')))); ?>" class="nostyle contact-<?php echo $fiche->get('contact_id'); ?>">
				<li class="contact <?php echo $sexe; ?>">
<strong><?php echo $nomAffichage; ?></strong>
				</li>
			</a>
    <?php endforeach; ?>
		</ul>
	</section>
    <?php endif; ?>
	
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
		<h4 class="nomRue">Immeubles restant à visiter <strong><span></span></strong></h4>
		
		<ul class="form-liste"></ul>
	</section>
</div>