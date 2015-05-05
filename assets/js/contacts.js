var contacts = function() {

    function majListing( action ) {
        // On prépare le tableau des données à envoyer
        var data = [];

        $('.droite section').hide();
        $('.chargementEnCours').fadeIn();

        // On commence par supprimer un possible bouton d'affichage de la suite pour éviter les interférences
        $('.afficherSuite').html('chargement…');
        $('.afficherSuite').attr('disabled', 'disabled');
        $('.afficherSuite').css('background-color', '#E6E5E4');
        $('.afficherSuite').css('cursor', 'default');
        $('.afficherSuite').css('pointer-event', 'none');

        // On récupère les données de formulaire
        data["email"] = $('#coordonnees-email').val();
        data["mobile"] = $('#coordonnees-mobile').val();
        data["fixe"] = $('#coordonnees-fixe').val();
        data["electeur"] = $('#coordonnees-electeur').val();
        data["criteres"] = $('#listeCriteresTri').val();

        // Nombre de fiches déjà affichée
        if (action == 'debut')
        {
            var nombre = 0;
            $('#nombreFiches').val(0);
        }
        else
        {
            var nombre = $('#nombreFiches').val();
        }

        // On prépare les données qui vont être envoyées
        var data = {
            email: data["email"],
            mobile: data["mobile"],
            fixe: data["fixe"],
            electeur: data["electeur"],
            adresse: data["adresse"],
            criteres: data["criteres"],
            debut: nombre
        };

        // On effectue l'appel AJAX qui va retourner l'estimation du nombre de fiches concernées
        $.get('ajax.php?script=people-count', data, function(nombre) {
            // On affiche ce nombre dans le span prévu à cet effet
            $('.estimationDuNombreDeFichesTotales').html('');

            switch (nombre) {
                case 0:
                    $('.estimationDuNombreDeFichesTotales').html('(aucune fiche)');
                    break;

                case 1:
                    $('.estimationDuNombreDeFichesTotales').html('(une fiche)');
                    break;

                default:
                    $('.estimationDuNombreDeFichesTotales').html('(' + nombre + ' fiches)');
                    break;
            }
        });

        // On effectue l'appel AJAX qui va récupérer les x fiches correspondantes
        $.getJSON('ajax.php?script=people-listing', data, function(data) {
            // On vérifie si ce n'est pas la fin de la liste (suite demandée et aucune donnée retournée
            if (data == '' && action == 'suite') {
                // On supprime maintenant le bouton
                $('.afficherSuite').html('fin de la liste');
                $('.afficherSuite').attr('disabled', 'disabled');
                $('.afficherSuite').css('background-color', '#E6E5E4');
                $('.afficherSuite').css('color', '#FFFFFF');
                $('.afficherSuite').css('cursor', 'default');
                $('.afficherSuite').css('pointer-event', 'none');
            }

            // Sinon on traite ces nouvelles données
            else {
                if (action == 'debut')
                {
                    // On commence par vider la liste des résultats affichés
                    $('.resultatTri').html('');
                }
                else
                {
                    // On supprime maintenant le bouton
                    $('.afficherSuite').remove();
                }

                // On retire de l'affichage toutes les sections ouvertes à droite, pour afficher uniquement celle qui nous intéresse
                $('.droite section').hide();
                $('.droite .actionsFiches').fadeIn();
                $('.droite .listeFiches').fadeIn();

                // On va faire une boucle de toutes les fiches créées pour les afficher dans cette liste de contacts
                $.each(data, function(key, val){
                    // on détermine le sexe à afficher
                    if (val.sexe == 'H') {
                        var sexe = 'homme';
                    }
                    else if (val.sexe == 'F') {
                        var sexe = 'femme';
                    }
                    else {
                        var sexe = 'isexe';
                    }

                    $('.resultatTri').append('<a href="index.php?page=contact&contact=' + val.id + '" class="nostyle contact-' + val.id + '"><li class="contact ' + sexe + '"><strong></strong><p><span class="age"></span></p></li></a>');

                    // On ajoute demande le nom de la fiche
                    var nom;
                    if (val.nom_complet.length == 0) {
                        if (val.organisme.length == 0) {
                            nom = 'Contact sans nom';
                        } else {
                            nom = val.organisme;
                        }
                    } else {
                        nom = val.nom_complet;
                    }

                    // On affecte les données aux balises HTML
                    $('.resultatTri .contact-' + val.id + ' li strong').html(nom);
                    $('.resultatTri .contact-' + val.id + ' li p .age').html(val.age);
                });

                // On ajoute le bouton permettant d'afficher les 5 fiches suivantes
                var nombreFiches = parseInt(nombre) + 5;
                $('#nombreFiches').val(nombreFiches);
                $('.resultatTri').append('<li><button class="afficherSuite clair">Afficher la suite</button></li>');
            }
        });

        return true;
    };

    $('.selectionTri').change(function() {
        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });

    $('.resultatTri').on('click', '.afficherSuite', function() {
        majListing('suite');
    });


    // Script de fermeture de la colonne de droite
    $('.fermerColonne').click(function() {
        $('.droite section').hide();
        $('.droite section:not(.invisible)').fadeIn();

        // On vide certains formulaires automatiquement
        $('#choixCritereThema').val('');
        $('#rechercheBureauVote').val('');
        $('.listeDesBureaux').html('');

        return false;
    });


    // Script de fermeture de la colonne de droite
    $('.fermerColonneListe').click(function() {
        $('.droite section').hide();
        $('.droite .listeFiches').fadeIn();

        return false;
    });


    // Affichage du formulaire de sélection d'un critère de tri
    $('.ajoutTri').click(function() {
        // On récupère le type de critère choisi
        var type = $(this).data('critere');

        // On cache la colonne de droite pour afficher le formulaire
        $('.droite section').hide();
        $('.selectionCritere-' + type).fadeIn();
    });


    // Script de recherche des bureaux de vote
    $('#rechercheBureauVote').keyup(function() {
        // On récupère les informations tapées
        var recherche = $(this).val();

        // On effectue une recherche AJAX pour trouver le bureau
        $.getJSON('ajax.php?script=bureaux', { bureau: recherche }, function(data) {
            // On vide la liste des bureaux de vote affichés précédemment
            $('.listeDesBureaux').html('');

            // On fait la boucle de tous les éléments trouvés et on les affiche
            $.each(data, function(key, val) {
                // Pour chaque bureau, on créé une puce
                $('.listeDesBureaux').append('<li class="bureau-' + val.bureau_id + '"><span class="bureau-nom">Bureau ' + val.number + ' ' + val.name + '</span><span class="bureau-ville">' + val.city_name + '</span><button class="choisirBureau" data-bureau="' + val.id + '" data-numero="' + val.number + '">Choisir</button></li>');
            });
        });
    });


    // Script de recherche des rues
    $('#rechercheRue').keyup(function() {
        // On récupère les informations tapées
        var recherche = $(this).val();

        // S'il y a plus de trois caractères entrés
        if (recherche.length >= 3)
        {
            // On effectue une recherche AJAX pour trouver la rue
            $.getJSON('ajax.php?script=rues', { rue: recherche }, function(data) {
                // On vide la liste des rues affichés précédemment
                $('.listeDesRues').html('');

                // On fait la boucle de tous les éléments trouvés et on les affiche
                $.each(data, function(key, val) {
                    // Pour chaque rue, on créé une puce
                    $('.listeDesRues').append('<li class="rue-' + val.id + '"><span class="rue-nom"></span><span class="rue-ville"></span><button class="choisirRue" data-rue="' + val.id + '" data-nom="' + val.street + '">Choisir</button></li>');
                    $('.rue-' + val.id + ' .rue-nom').html(val.street);
                    $('.rue-' + val.id + ' .rue-ville').html(val.city_name);
                });
            });
        } else {
            $('.listeDesRues').html('');
        }
    });


    // Script de recherche des rues
    $('#rechercheVille').keyup(function() {
        // On récupère les informations tapées
        var recherche = $(this).val();

        // S'il y a plus de trois caractères entrés
        if (recherche.length >= 3)
        {
            // On effectue une recherche AJAX pour trouver la rue
            $.getJSON('ajax.php?script=villes', { ville: recherche }, function(data) {
                // On vide la liste des rues affichés précédemment
                $('.listeDesVilles').html('');

                // On fait la boucle de tous les éléments trouvés et on les affiche
                $.each(data, function(key, val) {
                    // Pour chaque rue, on créé une puce
                    $('.listeDesVilles').append('<li class="ville-' + val.id + '"><span class="ville-nom"></span><span class="ville-dept"></span><button class="choisirVille" data-ville="' + val.id + '" data-nom="' + val.city + '">Choisir</button></li>');
                    $('.ville-' + val.id + ' .ville-nom').html(val.city);
                    $('.ville-' + val.id + ' .ville-dept').html(val.country_name);
                });
            });
        } else {
            $('.listeDesVilles').html('');
        }
    });


    // Script à la validation d'un nouveau bureau
    $('.listeDesBureaux').on('click', '.choisirBureau', function() {
        // On récupère le critère demandé
        var bureau = $(this).data('bureau');
        var numero = $(this).data('numero');

        // On efface le formulaire et la liste des résultats
        $('#rechercheBureauVote').val('');
        $('.listeDesBureaux').html('');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'bureau:' + bureau + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri bureau" data-critere="bureau" data-valeur="' + bureau + '">Bureau ' + numero + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script à la validation d'une nouvelle rue
    $('.listeDesRues').on('click', '.choisirRue', function() {
        // On récupère le critère demandé
        var rue = $(this).data('rue');
        var nom = $(this).data('nom');

        // On efface le formulaire et la liste des résultats
        $('#rechercheRue').val('');
        $('.listeDesRues').html('');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'rue:' + rue + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri rue" data-critere="rue" data-valeur="' + rue + '">' + nom + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script à la validation d'une nouvelle rue
    $('.listeDesVilles').on('click', '.choisirVille', function() {
        // On récupère le critère demandé
        var ville = $(this).data('ville');
        var nom = $(this).data('nom');

        // On efface le formulaire et la liste des résultats
        $('#rechercheVille').val('');
        $('.listeDesVilles').html('');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'ville:' + ville + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri ville" data-critere="ville" data-valeur="' + ville + '">' + nom + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script à la validation d'un nouveau critère thématique
    $('.validerChoixCritereThema').click(function() {
        // On récupère le critère demandé
        var thema = $('#choixCritereThema').val();

        // On efface ce formulaire
        $('#choixCritereThema').val('');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'thema:' + thema + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri thema" data-critere="thema" data-valeur="' + thema + '">' + thema + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script à la validation d'un nouveau critère thématique
    $('.validerRechercheCodesPostaux').click(function() {
        // On récupère le critère demandé
        var zipcode_debut = $('#rechercheCodePostalDebut').val(),
            zipcode_fin = $('#rechercheCodePostalFin').val(),
            valeur = zipcode_debut + '&' + zipcode_fin;

        // On efface ce formulaire
        $('#rechercheCodePostalDebut').val('');
        $('#rechercheCodePostalFin').val('');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'zipcode:' + valeur + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri zipcode" data-critere="zicpode" data-valeur="' + valeur + '">' + zipcode_debut + ' – ' + zipcode_fin + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script à la validation d'un nouveau critère de naissance
    $('.choixElection').click(function() {
        // On récupère le critère demandé
        var thema = $(this).data('election');
        var clair = $(this).data('clair');

        // On ajoute ce critère à la liste des critères
        var criteres = $('#listeCriteresTri').val();
        var newCriteres = criteres + 'vote:' + thema + ';';

        // On met à jour la liste des critères
        $('#listeCriteresTri').val(newCriteres);

        // On ajoute le critère à la liste dans la colonne de gauche
        $('.premierAjoutTri').before('<li class="tri vote" data-critere="vote" data-valeur="' + thema + '">' + clair + '</li>');

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Script de suppression d'un critère
    $('.listeTris').on('dblclick', '.tri:not(.ajoutTri)', function() {
        // On récupère le type
        var type = $(this).data('critere');
        var valeur = $(this).data('valeur');
        var chaine = ';' + type + ':' + valeur + ';';

        // On récupère les critères
        var criteres = ';' + $('#listeCriteresTri').val();

        // On supprime des critères le critère demandé
        criteres = criteres.replace(chaine, ';');

        // On met à jour les critères
        $('#listeCriteresTri').val(criteres);

        // On fini par supprimer cette puce
        $(this).remove();

        // On met à zéro le nombre de fiches déjà affichées
        $('#nombreFiches').val(0);

        // On met à jour le listing
        majListing('debut');
    });


    // Lancement d'un export
    $('.exportSelection').click(function() {
        // On récupère les données
        var data = {
            'email': $('#coordonnees-email').val(),
            'mobile': $('#coordonnees-mobile').val(),
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'criteres': ';' + $('#listeCriteresTri').val()
        };

        // On lance l'export en AJAX
        $.get('ajax.php?script=export', data);

        // On affiche que l'export est en cours et qu'un email sera envoyé
        swal({
            title: 'Export réussi !',
            text: 'Vous allez recevoir le fichier demandé sur votre adresse email dès qu\'il sera disponible',
            type: 'success'
        });
    });


    // Lancement d'une campagne SMS
    $('.smsSelection').click(function() {
        // On récupère les données
        var data = {
            'email': $('#coordonnees-email').val(),
            'mobile': 1,
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'criteres': ';' + $('#listeCriteresTri').val()
        };

        // On ferme la colonne
        $('.droite section').hide();

        // On effectue l'estimation du nombre de fiches
        $.get('ajax.php?script=contacts-estimation', data, function(nombre) {
            // On affiche ce nombre dans le formulaire
            $('.smsNombreDestinataire').val('');

            if (nombre > 1) {
                $('.smsNombreDestinataire').val(nombre + ' contacts');
            } else {
                $('.smsNombreDestinataire').val(nombre + ' contact');
            }
        });

        // On ouvre ce formulaire
        $('.smsEnvoiCampagne').fadeIn();
    });


    // Script de calcul du coût de l'opération
    $('.smsMessageCampagne').keyup(function() {
        // On calcule le nombre de caractères
        var message = $('.smsMessageCampagne').val();
        var nombre = message.length;
        var cout = parseInt(8);
        var nombreMax = parseInt(160);
        var personnes = parseInt($('.smsNombreDestinataire').val());

        // On effectue le calcul du nombre de messages
        var messages = Math.ceil(nombre / nombreMax);

        // On calcule le prix du message en centimes pour tout l'envoi
        var prix = messages * cout * personnes;

        // On calcule le prix du message en euros
        var euros = prix / 100;

        // On affiche ce prix
        $('.smsEstimation').html(euros + '&nbsp;&euro;');
    });


    // Script d'envoi en masse d'une campagne SMS
    $('.smsValidationCampagne').click(function() {

        $('.droite section').hide();
        $('.creationEnCours').fadeIn();

        // On récupère les données
        var data = {
            'email': $('#coordonnees-email').val(),
            'mobile': 1,
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'criteres': ';' + $('#listeCriteresTri').val(),
            'titre': $('#smsTitreCampagne').val(),
            'message': $('#smsMessageCampagne').val()
        };

        $.get('ajax.php?script=sms-campagne', data, function(data) {
        	    var url = 'index.php?page=campagne&id=' + data;
        	    document.location.href = url;
        });
    });


    // Lancement d'une campagne Email
    $('.emailSelection').click(function() {

        $('.droite section').hide();
        $('.creationEnCours').fadeIn();

        // On récupère les données
        var data = {
            'email': 1,
            'mobile': $('#coordonnees-mobile').val(),
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'criteres': ';' + $('#listeCriteresTri').val()
        };

        // On effectue l'estimation du nombre de fiches
        $.get('ajax.php?script=campagne-nouveau-email', data, function (data) {
        	    var url = 'index.php?page=campagne&id=' + data;
        	    document.location.href = url;
        });
    });


    // Script d'envoi en masse d'une campagne Email
    $('.emailValidationCampagne').click(function() {
        // On récupère les données
        var data = {
            'email': 1,
            'mobile': $('#coordonnees-mobile').val(),
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'criteres': ';' + $('#listeCriteresTri').val(),
            'titre': $('#emailTitreCampagne').val(),
            'message': $('#emailMessageCampagne').val()
        };

        $.get('ajax.php?script=email-campagne', data, function() {
            // On affiche une alertbox pour prévenir que la mission a été créée
            swal({
                title: 'Envoi réussi !',
                text: 'Vous pouvez retrouver cette campagne dans le module Emailing',
                type: 'success'
            });

            // On revient à la situation initiale en vidant le formulaire
            $('.droite section').hide();
            $('#emailTitreCampagne').val('');
            $('#emailNombreDestinataire').val('');
            $('#emailMessageCampagne').val('');
            $('.droite section:not(.invisible)').fadeIn();
        });
    });


    // Lancement d'une campagne de Publipostage
    $('.publiSelection').click(function() {
        // On récupère les données
        var data = {
            'email': $('#coordonnees-email').val(),
            'mobile': $('#coordonnees-mobile').val(),
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'adresse': 1,
            'criteres': ';' + $('#listeCriteresTri').val()
        };

        // On ferme la colonne
        $('.droite section').hide();

        // On effectue l'estimation du nombre de fiches
        $.get('ajax.php?script=contacts-estimation', data, function(nombre) {
            // On affiche ce nombre dans le formulaire
            $('.publiNombreDestinataire').val('');

            if (nombre > 1) {
                $('.publiNombreDestinataire').val(nombre + ' contacts');
            } else {
                $('.publiNombreDestinataire').val(nombre + ' contact');
            }
        });

        // On ouvre ce formulaire
        $('.publiEnvoiCampagne').fadeIn();
    });


    // Script d'envoi en masse d'une campagne Email
    $('.publiValidationCampagne').click(function() {
        // On récupère les données
        var data = {
            'email': $('#coordonnees-email').val(),
            'mobile': $('#coordonnees-mobile').val(),
            'fixe': $('#coordonnees-fixe').val(),
            'electeur': $('#coordonnees-electeur').val(),
            'adresse': 1,
            'criteres': ';' + $('#listeCriteresTri').val(),
            'titre': $('#publiTitreCampagne').val(),
            'message': $('#publiDescriptionCampagne').val()
        };

        // On désactive le bouton
        $('.publiValidationCampagne').attr('disabled', true);
        $('.publiValidationCampagne span').html('Création en cours');

        $.get('ajax.php?script=publi-campagne', data, function() {
            // On affiche une alertbox pour prévenir que la mission a été créée
            swal({
                title: 'Préparation réussie !',
                text: 'Vous pouvez retrouver cette campagne dans le module Publipostage',
                type: 'success'
            });

            // On revient à la situation initiale en vidant le formulaire
            $('.droite section').hide();
            $('#publiTitreCampagne').val('');
            $('#publiNombreDestinataire').val('');
            $('#publiDescriptionCampagne').val('');
            $('.droite section:not(.invisible)').fadeIn();
            $('.publiValidationCampagne').attr('disabled', false);
            $('.publiValidationCampagne span').html('Préparation de la campagne');
        });
    });
};

$(document).ready(contacts);
