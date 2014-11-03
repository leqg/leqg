<?php 
    // On ouvre la mission
    $mission = new Rappel($_GET['mission']);
    
    // On charge le header du template
    Core::tpl_header(); 
?>

    <h2 class="titre" data-mission="<?php echo $mission->get('md5'); ?>"><?php echo (!empty($mission->get('nom'))) ? $mission->get('nom') : 'Cliquez ici pour ajouter un titre.'; ?></h2>
    
    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Argumentaire – fil conducteur</h4>
            
            <ul class="formulaire">
                <li>
                    <span class="form-icon notes">
                        <textarea name="argumentaire" id="argumentaire" class="long" placeholder="Tapez ici l'argumentaire d'appel présenté aux militants"><?php echo $mission->get('argumentaire'); ?></textarea>
                    </span>
                </li>
            </ul>
        </section>
    </div>
    
    <div class="colonne demi droite">
        <section class="contenu demi">
            <h4>Statistiques sur la mission</h4>
            <?php if ($mission->get('nombre') > 0) : ?>
            <p>Cette mission comporte <strong><?php echo $mission->get('nombre'); ?></strong> numéro<?php echo ($mission->get('nombre') > 1) ? 's' : ''; ?> à contacter.</p>
            <?php else : ?>
            <p>Cette mission ne comporte <strong>aucun</strong> numéro à appeler.</p>
            <?php endif; ?>
            
            <button class="ajouterNumeros">Ajouter des numéros</button>
        </section>
        
        <section class="contenu demi invisible changerNom">
            <a href="#" class="fermerColonne">&#xe813;</a>

            <h4>Changement du nom</h4>
            
            <ul class="formulaire">
                <li>
                    <span class="form-icon nom">
                        <input type="text" name="nomMission" id="nomMission" placeholder="Nom de la mission" value="<?php echo $mission->get('nom'); ?>">
                    </span>
                </li>
                <li>
                    <button class="validerNomMission">Valider le changement de nom</button>
                </li>
            </ul>
        </section>
        
        <section class="contenu demi invisible criteresAjout">
            <a href="#" class="fermerColonne">&#xe813;</a>
            
            <h4>Critères de sélection des fiches à contacter</h4>
            
            <ul class="formulaire">
                <li class="estimation">
                    <label class="small">Estimation du nombre d'appels</label>
                    <p>Ce choix représente <strong>0</strong> nouveaux numéros.</p>
                </li>
                <li>
                    <label class="small">Résumé des critères</label>
                    <input type="hidden" name="choixCritereAge" id="choixCritereAge">
                    <input type="hidden" name="choixCritereBureaux" id="choixCritereBureaux">
                    <input type="hidden" name="choixCritereThema" id="choixCritereThema">
                    <ul class="listeCriteres"></ul>
                </li>
                <li>
                    <button class="critereAge">Sélectionner un âge</button>
                    <button class="critereBureaux">Sélectionner un bureau de vote</button>
                    <button class="critereThema">Sélectionner une thématique</button>
                </li>
                <li>
                    <button class="deleting razCriteres">Remettre à zéro les critères</button>
                </li>
            </ul>
        </section>
        
        <section class="contenu demi invisible selectionCritereAge">
            <a href="#" class="revenirArriere">&#xe813;</a>

            <h4>Discrimination par âge</h4>
            
            <ul class="formulaire">
                <li>
                    <label class="small" for="ageMin">Âge minimal</label>
                    <span class="form-icon naissance">
                        <label class="sbox" for="ageMin">
                            <select name="ageMin" id="ageMin">
                                <?php $i = 18; while ($i <= 100) : ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> ans</option>
                                <?php $i++; endwhile; ?>
                            </select>
                        </label>
                    </span>
                </li>
                <li>
                    <label class="small" for="ageMax">Âge maximal</label>
                    <span class="form-icon naissance">
                        <label class="sbox" for="ageMax">
                            <select name="ageMax" id="ageMax">
                                <?php $i = 18; while ($i <= 100) : ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == 65) { echo 'selected'; } ?>><?php echo $i; ?> ans</option>
                                <?php $i++; endwhile; ?>
                            </select>
                        </label>
                    </span>
                </li>
                <li>
                    <button class="validerCritereAge">Valider le critère d'âge</button>
                </li>
                <li>
                    <button class="deleting retraitCritereAge">Retirer le critère d'âge</button>
                </li>
            </ul>
        </section>
    </div>
    
<?php Core::tpl_footer(); ?>