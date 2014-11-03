/**
 * Ferme les onglets ouverts en colonne droite pour revenir à l'affichage initial
 */

function fermerColonne(  )
{
    $('.droite section').hide();
    $('.droite section:not(.invisible)').fadeIn();
}

/**
 * Ferme les onglets ouverts en colonne droite pour ouvrir un onglet demandé
 */

function ouvrirOnglet( onglet )
{
    $('.droite section').hide();
    $('.' + onglet).fadeIn();
}


/**
 * Sauvegarde l'argumentaire
 *
 * Cette méthode permet de sauvegarder l'argumentaire dans la base de données
 *
 */
 
function sauvArgumentaire()
{
    var argumentaire = $('#argumentaire').val();
    var mission = $('.titre').data('mission');
    
    // On lance la sauvegarde
    $.post('ajax.php?script=rappels-argumentaire', { mission: mission, argumentaire: argumentaire });
}


/**
 * Sauvegarder le nouveau nom
 */

function sauvNomMission()
{
    var mission = $('.titre').data('mission');
    var nom = $('#nomMission').val();
    
    // On lance la sauvegarde
    $.post('ajax.php?script=rappels-nom', { mission: mission, nom: nom }, function() {
        // On modifie le nom sur la fiche
        if (nom.length > 0)
        {
            $('.titre').html(nom);
        }
        else
        {
            $('.titre').html('Cliquez ici pour ajouter un titre.');
        }
        
        // On ferme la colonne
        fermerColonne();
    });
}


/**
 * Estime le nombre de numéros de téléphone correspondant aux critères demandés
 */

function estimation()
{
    // On récupère les critères
    var age = $('#choixCritereAge').val();
    var bureaux = $('#choixCritereBureaux').val();
    var thema = $('#choixCritereThema').val();
    
    // On lance l'estimation
    $.post('ajax.php?script=rappels-estimation', { age: age, bureaux: bureaux, thema: thema }, function(data) {
        // On affiche l'estimation
        $('.estimation strong').html(data);
    });
}


/**
 * Valide le critère d'âge entré et effectue une estimation du nombre de fiches
 */

function validerCritereAge()
{
    // On récupère le critère entré
    var ageMin = $('#ageMin').val();
    var ageMax = $('#ageMax').val();
    
    // On l'ajoute à la liste des critères
    $('#choixCritereAge').val(ageMin + ':' + ageMax);
    
    // On ajoute le critère à la liste
    if ($('.listeCriteres li').hasClass('age'))
    {
        $('.listeCriteres li.age').html('Fiches entre ' + ageMin + ' et ' + ageMax + ' ans');
    }
    else
    {
        $('.listeCriteres').append('<li class="age">Fiches entre ' + ageMin + ' et ' + ageMax + ' ans</li>');
    }
    
    // On lance l'estimation
    estimation();
    
    // On ferme le choix de critère
    ouvrirOnglet('criteresAjout');
}


/**
 * On retire le critère d'âge
 */

function retraitCritereAge()
{
    // On retire le critère du formulaire
    $('#choixCritereAge').val('');
    
    // On retire la liste expliquant le critère
    $('.listeCriteres li.age').remove();
    
    // On revient aux choix de critères
    ouvrirOnglet('criteresAjout');
}


var rappels = function() {
    // On ferme l'onglet ouvert si demandé
    $('.fermerColonne').click(function() { fermerColonne(); return false; });
    
    // On affiche le formulaire de changement de nom
    $('.titre').click( function() { ouvrirOnglet('changerNom'); } );
    
    // On affiche le formulaire de choix des critères de sélection de numéros
    $('.ajouterNumeros').click( function() { ouvrirOnglet('criteresAjout'); } );
    
    // On revient en arrière
    $('.revenirArriere').click( function() { ouvrirOnglet('criteresAjout'); return false; } );
    
    // On sauvegarde le nom de la mission dès que validé
    $('.validerNomMission').click(sauvNomMission);
    
    // On sauvegarde l'argumentaire chaque fois qu'il est modifié
    $('#argumentaire').blur(sauvArgumentaire);
    
    // On ouvre le volet de choix d'un critère à rajouter
    $('.critereAge').click(function() { ouvrirOnglet('selectionCritereAge'); });
    
    // On valide le critère d'âge
    $('.validerCritereAge').click(validerCritereAge);
    
    // On retire le critère d'âge
    $('.retraitCritereAge').click(retraitCritereAge);
};

// On déclenche les fonctions JS/jQuery au chargement de la page
$(document).ready(rappels);