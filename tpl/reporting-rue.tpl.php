<?php
    // On ouvre la mission
    $data = new Mission($_GET['mission']);
    
    // On vérifie que la mission a bien été ouverte
if ($data->err) { Core::tpl_go_to('porte', true); 
}
    
    // On récupère tous les items de la rue et la rue en question et la ville concernée
    $rue = Maps::street_data($_GET['rue']);
    $ville = Maps::city_data($rue['city']);
    $items = $data->items($_GET['rue']);
    
if (!$items) { Core::tpl_go_to('reporting', array('mission' => $_GET['mission']), true); 
}
    
    // typologie
    $typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
    Core::tpl_header();
?>
<a href="<?php Core::tpl_go_to('reporting', array('mission' => $data->get('mission_hash'))); ?>" class="nostyle"><button class="gris" style="float: right; margin-top: 0em;">Retour à la mission</button></a>	
<h2 id="titre-mission" class="titre" data-mission="<?php echo $data->get('mission_hash'); ?>">Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

<section id="mapbox-mission"></section>

<form action="<?php Core::tpl_go_to('reporting', array('action' => 'envoi', 'mission' => $data->get('mission_hash'), 'rue' => $_GET['rue'])); ?>" method="post">
    <section class="contenu">
        <?php
            // Affichage spécial s'il s'agit d'un porte à porte
        if ($data->get('mission_type') == 'porte') :
            // Pour chaque immeuble trouvé, on regarde quel est son réel numéro
            $numeros = array();
            $numeros_sauv = array();
            foreach ($items as $immeuble => $electeurs) {
                $infos = Maps::building_data($immeuble);
                $numeros[$immeuble] = preg_replace('#[^0-9]+#', '', $infos['building']);
                $numeros_sauv[$immeuble] = $infos['building'];
            }
                
            // On tri les immeubles
            asort($numeros);
            
            // On fait la boucle des immeubles
            foreach ($numeros as $immeuble => $numero) :
                $electeurs = $items[$immeuble];
        ?>
        <h4><?php echo $numero; ?> <?php echo $rue['street']; ?></h4>
            		        
        <table class="reporting">
         <thead>
          <tr>
              <th>Électeur</th>
              <th class="petit">Absent</th>
              <th class="petit">Ouvert</th>
              <th class="petit">Procuration</th>
              <th class="petit">À contacter</th>
              <th class="petit">NPAI</th>
          </tr>
         </thead>
         <tbody>
            <?php foreach ($electeurs as $electeur) : $contact = new People($electeur); ?>
    		        <tr class="ligne-electeur-<?php echo md5($contact->get('id')); ?>">
        		        <td><?php echo $contact->display_name(); ?></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo $contact->get('id'); ?>" data-val="1" type="radio" id="electeur-<?php echo $contact->get('id'); ?>-a" name="electeur-<?php echo $contact->get('id'); ?>" value="1"><label for="electeur-<?php echo $contact->get('id'); ?>-a" data-contact="<?php echo md5($contact->get('id')); ?>" data-val="1"><span><span></span></span></label></div></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo $contact->get('id'); ?>" data-val="2" type="radio" id="electeur-<?php echo $contact->get('id'); ?>-o" name="electeur-<?php echo $contact->get('id'); ?>" value="2"><label for="electeur-<?php echo $contact->get('id'); ?>-o" data-contact="<?php echo md5($contact->get('id')); ?>" data-val="2"><span><span></span></span></label></div></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo $contact->get('id'); ?>" data-val="3" type="radio" id="electeur-<?php echo $contact->get('id'); ?>-p" name="electeur-<?php echo $contact->get('id'); ?>" value="3"><label for="electeur-<?php echo $contact->get('id'); ?>-p" data-contact="<?php echo md5($contact->get('id')); ?>" data-val="3"><span><span></span></span></label></div></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo $contact->get('id'); ?>" data-val="4" type="radio" id="electeur-<?php echo $contact->get('id'); ?>-c" name="electeur-<?php echo $contact->get('id'); ?>" value="4"><label for="electeur-<?php echo $contact->get('id'); ?>-c" data-contact="<?php echo md5($contact->get('id')); ?>" data-val="4"><span><span></span></span></label></div></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input data-contact="<?php echo $contact->get('id'); ?>" data-val="-1" type="radio" id="electeur-<?php echo $contact->get('id'); ?>-n" name="electeur-<?php echo $contact->get('id'); ?>" value="-1"><label for="electeur-<?php echo $contact->get('id'); ?>-n" data-contact="<?php echo md5($contact->get('id')); ?>" data-val="-1"><span><span></span></span></label></div></td>
    		        </tr>
            <?php endforeach; ?>
         </tbody>
        </table>
        <button type="submit" style="font-size: 1em;">Enregistrer les modifications</button>
        <?php
            endforeach;
            
            // Affichage s'il s'agit d'un boîtage
            else :
                // Pour chaque immeuble trouvé, on regarde quel est son réel numéro
                $numeros = array();
                $numeros_sauv = array();
                foreach ($items as $immeuble) {
                    $infos = Maps::building_data($immeuble['immeuble_id']);
                    $numeros[$immeuble['immeuble_id']] = preg_replace('#[^0-9]+#', '', $infos['building']);
                    $numeros_sauv[$immeuble['immeuble_id']] = $infos['building'];
                }
                
                // On tri les immeubles
                asort($numeros);
                
                // On fait la boucle des immeubles
        ?>
            <h4>Reporting de la mission de boîtage</h4>
            		        
            <table class="reporting">
    	        <thead>
    		        <tr>
        		        <th style="font-size: .95em;">Immeuble</th>
        		        <th class="petit" style="font-size: .95em;">Non&nbsp;boîté</th>
        		        <th class="petit" style="font-size: .95em;">Boîté</th>
    		        </tr>
    	        </thead>
    	        <tbody>
                <?php foreach ($numeros as $immeuble => $numero) : ?>
    		        <tr class="ligne-electeur-<?php echo $immeuble; ?>">
        		        <td><?php echo $numeros_sauv[$immeuble]; ?> <?php echo $rue['street']; ?></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input  type="radio" id="electeur-<?php echo $immeuble; ?>-a" name="electeur-<?php echo $immeuble; ?>" value="1"><label for="electeur-<?php echo $immeuble; ?>-a" data-contact="<?php echo md5($immeuble); ?>" data-val="1"><span><span></span></span></label></div></td>
        		        <td class="petit"><div class="radio bouton-reporting"><input  type="radio" id="electeur-<?php echo $immeuble; ?>-o" name="electeur-<?php echo $immeuble; ?>" value="2"><label for="electeur-<?php echo $immeuble; ?>-o" data-contact="<?php echo md5($immeuble); ?>" data-val="2"><span><span></span></span></label></div></td>
    		        </tr>
                <?php endforeach; ?>
    	        </tbody>
            </table>
            <button type="submit" style="font-size: 1em;">Enregistrer les modifications</button>
            <?php endif; ?>
    </section>
</form>

<script>
	// Mise en place de la map
	var map = L.map('mapbox-mission');
	
	// Sélection du tile layer OSM
	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);

    <?php foreach ($numeros as $immeuble => $numero) : ?>
	// On récupère sur le Nominatim OSM les coordonnées de la rue en question
	var data = {
		format: 'json',
		email: 'tech@leqg.info',
		country: 'France',
		city: "<?php echo $ville['city']; ?>",
		street: "<?php echo $numeros_sauv[$immeuble] . ' ' . $rue['street']; ?>"
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
			title: "<?php echo $numeros_sauv[$immeuble] . ' ' . $rue['street']; ?>"
		}).addTo(map);
	});
    <?php endforeach; ?>
</script>    