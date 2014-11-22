<?php Core::tpl_header(); $mission = Boite::informations($_GET['mission'])[0]; ?>

    <h2 class="titre" data-mission="<?php echo md5($mission['mission_id']); ?>">Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

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
                		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="2"><input data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="2" type="radio" id="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-a" name="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>" value="1"><label for="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-a"><span><span></span></span></label></div></td>
                		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="1"><input data-contact="<?php echo md5($idImmeubles[$immeuble]); ?>" data-val="1" type="radio" id="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-o" name="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>" value="0"><label for="immeuble-<?php echo md5($idImmeubles[$immeuble]); ?>-o"><span><span></span></span></label></div></td>
                        </tr>
                        <?php endforeach; ?>
        		        </tbody>
                </table>

    		    <?php endif; endif; endforeach; ?>
        <?php else : ?>
        Aucun immeuble à visiter dans cette mission
        <?php endif; ?>
        
        <a href="<?php Core::tpl_go_to('boite', array('mission' => $_GET['mission'])); ?>" class="nostyle"><button>Revenir à la mission</button></a>
    </section>
    
<?php Core::tpl_footer(); ?>