<?php
	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::goTo('porte', true);
	
	// On récupère tous les items de la rue et la rue en question et la ville concernée
	$rue = Carto::rue($_GET['rue']);
	$items = $data->items($_GET['rue']);
	
	if (!$items) Core::goTo('reporting', array('mission' => $_GET['mission']), true);
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::loadHeader();
?>

	<h2>Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>
	
	<ul class="listeImmeubles">
    	<?php
        	if ($data->get('mission_type') == 'porte') :
                // Pour chaque immeuble trouvé, on regarde quel est son réel numéro
                $numeros = array();
                $numeros_sauv = array();
                foreach ($items as $immeuble => $electeurs) {
                    $infos = Carto::immeuble($immeuble);
                    $numeros[$immeuble] = preg_replace('#[^0-9]+#', '', $infos['immeuble_numero']);
                    $numeros_sauv[$immeuble] = $infos['immeuble_numero'];
                }
                
                // On tri les immeubles
                asort($numeros);
            
                // On fait la boucle des immeubles
                foreach ($numeros as $immeuble => $numero) :
            ?>
        		<a class="nostyle" href="<?php Core::goTo('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $immeuble)); ?>">
        			<li id="element-<?php echo $immeuble; ?>">
        				<span><?php if (!empty($numeros_sauv[$immeuble])) { echo $numeros_sauv[$immeuble]; } else { echo '&nbsp;'; } ?></span> <?php echo $rue['rue_nom']; ?>
        			</li>
        		</a>
            <?php
                endforeach;
            else :
                // Pour chaque immeuble trouvé, on regarde quel est son réel numéro
                $numeros = array();
                $numeros_sauv = array();
                foreach ($items as $immeuble) {
                    $infos = Carto::immeuble($immeuble['immeuble_id']);
                    $numeros[$immeuble['immeuble_id']] = preg_replace('#[^0-9]+#', '', $infos['immeuble_numero']);
                    $numeros_sauv[$immeuble['immeuble_id']] = $infos['immeuble_numero'];
                }
                
                // On tri les immeubles
                asort($numeros);
            
                // On fait la boucle des immeubles
                foreach ($numeros as $immeuble => $numero) :
            ?>
        		<a class="nostyle" href="<?php Core::goTo('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $immeuble)); ?>">
        			<li id="element-<?php echo $immeuble; ?>">
        				<span><?php if (!empty($numeros_sauv[$immeuble])) { echo $numeros_sauv[$immeuble]; } else { echo '&nbsp;'; } ?></span> <?php echo $rue['rue_nom']; ?>
        			</li>
        		</a>
            <?php
                endforeach;  
            endif;
            ?>
	</ul>
	
	<a href="<?php Core::goTo('mission', array('code' => $_GET['code'])); ?>" class="bouton nostyle">Retour à la mission</a>

<?php Core::loadFooter(); ?>