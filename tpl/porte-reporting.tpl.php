<?php Core::tpl_header(); $mission = Porte::informations($_GET['reporting'])[0]; ?>

    <h2 class="titre" data-mission="<?php echo md5($mission['mission_id']); ?>">Mission &laquo;&nbsp;<?php echo $mission['mission_nom']; ?>&nbsp;&raquo;</h2>

    <section class="mission-porte">
    		<?php if (Porte::nombreVisites($mission['mission_id'])) : ?>
    		    <?php $rues = Porte::liste($mission['mission_id']); foreach ($rues as $rue => $immeubles) : if (count($immeubles)) : ?>
    		    <h4><?php $nomRue = Carto::afficherRue($rue, true); echo $nomRue; ?></h4>

        		    <?php foreach ($immeubles as $immeuble) : ?>
        		    
        		        <h5><?php Carto::afficherImmeuble($immeuble); echo $nomRue; ?></h5>
        		        
        		        <table class="reporting">
            		        <thead>
                		        <tr>
                    		        <th>Électeur</th>
                    		        <th class="petit">Absent</th>
                    		        <th class="petit">Ouvert</th>
                    		        <th class="petit">À contacter</th>
                    		        <th class="petit">NPAI</th>
                		        </tr>
            		        </thead>
            		        <tbody>
             		        <?php $electeurs = Porte::electeurs(md5($mission['mission_id']), md5($immeuble)); foreach ($electeurs as $electeur) : ?>
               		        <tr class="ligne-electeur-<?php echo md5($electeur['contact_id']); ?>">
                    		        <td><?php echo mb_convert_case($electeur['contact_nom'], MB_CASE_UPPER) . ' ' . mb_convert_case($electeur['contact_nom_usage'], MB_CASE_UPPER) . ' ' . mb_convert_case($electeur['contact_prenoms'], MB_CASE_TITLE); ?></td>
                    		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($electeur['contact_id']); ?>" data-val="1"><input data-contact="<?php echo $electeur['contact_id']; ?>" data-val="1" type="radio" id="electeur-<?php echo $electeur['contact_id']; ?>-a" name="electeur-<?php echo $electeur['contact_id']; ?>" value="0"><label for="electeur-<?php echo $electeur['contact_id']; ?>-a"><span><span></span></span></label></div></td>
                    		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($electeur['contact_id']); ?>" data-val="2"><input data-contact="<?php echo $electeur['contact_id']; ?>" data-val="2" type="radio" id="electeur-<?php echo $electeur['contact_id']; ?>-o" name="electeur-<?php echo $electeur['contact_id']; ?>" value="1"><label for="electeur-<?php echo $electeur['contact_id']; ?>-o"><span><span></span></span></label></div></td>
                    		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($electeur['contact_id']); ?>" data-val="2"><input data-contact="<?php echo $electeur['contact_id']; ?>" data-val="2" type="radio" id="electeur-<?php echo $electeur['contact_id']; ?>-c" name="electeur-<?php echo $electeur['contact_id']; ?>" value="2"><label for="electeur-<?php echo $electeur['contact_id']; ?>-c"><span><span></span></span></label></div></td>
                    		        <td class="petit"><div class="radio bouton-reporting" data-contact="<?php echo md5($electeur['contact_id']); ?>" data-val="1"><input data-contact="<?php echo $electeur['contact_id']; ?>" data-val="1" type="radio" id="electeur-<?php echo $electeur['contact_id']; ?>-n" name="electeur-<?php echo $electeur['contact_id']; ?>" value="-1"><label for="electeur-<?php echo $electeur['contact_id']; ?>-n"><span><span></span></span></label></div></td>
                		        </tr>
                		        <?php endforeach; ?>
            		        </tbody>
        		        </table>

        		    <?php endforeach; ?>

    		    <?php endif; endforeach; ?>
        <?php else : ?>
        Aucun immeuble à visiter dans cette mission
        <?php endif; ?>
    </section>
    
<?php Core::tpl_footer(); ?>