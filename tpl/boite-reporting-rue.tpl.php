<?php Core::tpl_header(); $mission = Boite::informations($_GET['mission'])[0]; ?>

    <h2 class="titre" data-mission="<?php echo md5($mission['mission_id']); ?>">Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

	<section id="mapbox-mission"></section>

    <section class="mission-porte">        
    		<?php if (Boite::nombreImmeubles($mission['mission_id'])) : ?>
    		    <?php $rues = Boite::liste($mission['mission_id']); foreach ($rues as $rue => $immeubles) : if (count($immeubles)) : if ($rue == $_GET['rue']) : ?>
    		    <h4><?php $nomRue = Carto::afficherRue($rue, true); echo $nomRue; ?></h4>
                
                <table class="reporting">
        		        <thead>
            		        <tr>
                		        <th>Immeuble</th>
                		        <th class="petit">Boîtage réalisé</th>
                		        <th class="petit">Boîtage impossible</th>
            		        </tr>
        		        </thead>
        		        
        		        <tbody>
            		        <?php
                		        $idImmeubles = array();
                        		foreach ($immeubles as $key => $immeuble) {
                        			$immeubles[$key] = Carto::afficherImmeuble($immeuble, true);
                        			$idImmeubles[$immeubles[$key]] = $immeuble;
                        		}
                        		natsort($immeubles);
                        		foreach ($immeubles as $immeuble) :
                        ?>
                        <tr class="ligne-immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>">
                		        <td><?php echo $immeuble; ?></span> <?php echo $nomRue; ?></td>
                		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="2" type="radio" id="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-a" name="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>" value="1"><label for="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-a" data-immeuble="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="2"><span><span></span></span></label></div></td>
                		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="1" type="radio" id="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-o" name="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>" value="0"><label for="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-o" data-immeuble="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="1"><span><span></span></span></label></div></td>
                        </tr>
                        <?php endforeach; ?>
        		        </tbody>
                </table>

    		    <?php endif; endif; endforeach; ?>
        <?php else : ?>
        Aucun immeuble à visiter dans cette mission
        <?php endif; ?>
        
        <?php if (User::auth_level() > 5) : ?>
        <a href="<?php Core::tpl_go_to('boite', array('mission' => $_GET['mission'])); ?>" class="nostyle"><button>Revenir à la mission</button></a>
        <?php else : ?>
        <a href="<?php Core::tpl_go_to('boite', array('action' => 'voir', 'mission' => $mission['mission_id'])); ?>" class="nostyle"><button>Revenir à la mission</button></a>
        <?php endif; ?>
    </section>
    
	<?php
		if (Boite::nombreImmeubles($mission['mission_id'])) :
		
	    	$rues = Boite::liste($mission['mission_id']);
	    	
	    	foreach ($rues as $rue => $immeubles) :
	    	
	    		if (count($immeubles)) :
	    		
	    			if ($rue == $_GET['rue']) :
	    			
	    				$ville = Carto::ville(Carto::villeParRue($rue));
	?>
					    <script>
							// Mise en place de la map
							var map = L.map('mapbox-mission');
							
							// Sélection du tile layer OSM
							L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);
						
							<?php foreach ($immeubles as $immeuble) : ?>
							// On récupère sur le Nominatim OSM les coordonnées de la rue en question
							var data = {
								format: 'json',
								email: 'tech@leqg.info',
								country: 'France',
								city: "<?php echo $ville['commune_nom']; ?>",
								street: "<?php Carto::afficherImmeuble($immeuble); echo $nomRue; ?>"
							}
							
							// On récupère le JSON contenant les coordonnées de la rue
							$.getJSON('https://nominatim.openstreetmap.org', data, function(data) {
								// On récupère uniquement les données du premier résultat
								data = data[0];
								
								// On prépare la boundingbox
								var loc1 = new L.LatLng(data.boundingbox[0], data.boundingbox[2]);
								var loc2 = new L.LatLng(data.boundingbox[1], data.boundingbox[3]);
								var bounds = new L.LatLngBounds(loc1, loc2);
								
								// On fabrique une vue qui contient l'ensemble du secteur demandé
								map.fitBounds(bounds, { maxZoom: 17 });
								
								// On ajoute un marker au milieu de la rue
								L.marker([data.lat, data.lon], {
									clicable: false,
									title: "<?php Carto::afficherImmeuble($immeuble); echo $nomRue; ?>"
								}).addTo(map);
							});
							<?php endforeach; ?>
						</script>
	<?php
					endif;
				endif;
			endforeach;
		endif;
	?>

    
<?php Core::tpl_footer(); ?>