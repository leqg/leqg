<?php
    Core::tpl_header();
    
    if (isset($_POST['rechercheThematique']))
    {
        $terme = $_POST['rechercheThematique'];
?>
    <h2>Recherche thématique <em>&laquo;&nbsp;<?php echo $terme; ?>&nbsp;&raquo;</em></h2>
    
    <section id="missions">
        <h4>Résultats de la recherche</h4>
        
        <?php $recherche = Contact::rechercheThematique($terme); if (count($recherche)) : ?>
        <ul class="listeResultats">
            <?php foreach ($recherche as $key => $val) : $contact = new Contact(md5($val)); ?>
            <a href="<?php Core::tpl_go_to('contact', array('contact' => md5($val))); ?>">
                <li class="contact">
                    <strong>
                        <?php
                            if (!empty($contact->noms(' ')))
                            {
                                echo $contact->noms(' ');
                            }
                            else if (!empty($contact->get('contact_organisme')))
                            {
                                echo $contact->get('contact_organisme');
                            }
                            else
                            {
                                echo 'Fiche sans nom'; 
                            }
                        ?>
                    </strong>
                    <?php $tags = $contact->get('tags'); if (count($tags)) : ?>
                    <ul class="listeDesTags">
                        <?php foreach ($tags as $tag) : ?>
                        <li class="tag"><?php echo $tag; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
            </a>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
        <h3>Aucune réponse à votre recherche</h3>
        <?php endif; ?>
    </section>
<?php
    }
    else
    {
        echo 'Erreur !';
    }
    
    Core::tpl_footer();
?>