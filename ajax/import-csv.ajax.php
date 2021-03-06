<?php
/**
 * Import des contacts présents dans un CSV
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère le nom du fichier
$file = $_POST['fichier'];

// On lance la lecture du fichier
$data = $csv->lectureFichier('csv/' . $file . '.csv');

// On lance le calcul du nombre de lignes
$row = 0;

// On lance l'analyse de chaque ligne
foreach ($data as $line) :
    /*
     * [0] => bureau de vote
     * [1] => numéro d'électeur
     * [2] => nom de famille
     * [3] => nom d'usage
     * [4] => prénoms
     * [5] => sexe ( M / F )
     * [6] => date de naissance
     * [7] => département de naissance
     * [8] => ville de naissance
     * [9] =>
     * [10] => adresse, ligne 1
     * [11] => adresse, ligne 2
     * [12] => adresse, ligne 3
     * [13] => code postal et ville
     */

    // On analyse l'adresse pour extraire le contenu dans un tableau $adresse
    $bureau = $line[0];
    $rue = explode(' ', $line[10], 2);
    $ville = explode(' ', $line[13], 2);
    $adresse['immeuble'] = $rue[0];
    $adresse['rue'] = $core->transformText($rue[1]);
    $adresse['code_postal'] = $ville[0];
    $adresse['ville'] = $ville[1];

    // On commence d'abord par récupérer l'ID de la ville en question
    $query = 'SELECT *
              FROM communes
              WHERE commune_nom
              LIKE "' . $core->formatageRecherche($adresse['ville']) . '"
              LIMIT 0,1';
    $sql = $db->query($query); $row = $sql->fetch_assoc();
    $code['ville'] = $row['commune_id'];

    // On recherche si le bureau de vote existe déjà
    $query = 'SELECT *
              FROM bureaux
              WHERE bureau_numero = ' . $bureau . '
              AND commune_id = ' . $code['ville'];
    $sql = $db->query($query);

    // S'il existe déjà un bureau de vote dans la base de données,
    // on en récupère l'identifiant
    if ($sql->num_rows == 1) {
        $row = $sql->fetch_assoc();
        $code['bureau'] = $row['bureau_id'];
    } else {
        // On rajoute le bureau de vote dans la base de données
        $query = 'INSERT INTO bureaux (`commune_id`, `bureau_numero`)
                  VALUES (' . $code['ville'] . ', ' . $bureau . ')';
        $db->query($query);
        $code['bureau'] = $db->insert_id;
    }

    // On continu en vérifiant si la rue existe déjà
    $query = 'SELECT *
              FROM rues
              WHERE commune_id = ' . $code['ville'] . '
              AND rue_nom LIKE "' . $adresse['rue'] . '"
              LIMIT 0,1';
    $sql = $db->query($query);

    // S'il existe déjà une rue dans la base de données,
    // on récupère l'identifiant
    if ($sql->num_rows == 1) {
        $row = $sql->fetch_assoc();
        $code['rue'] = $row['rue_id'];
    } else {
        // On rajoute la rue dans la base de données
        $query = 'INSERT INTO rues (commune_id, rue_nom)
                  VALUES (' . $code['ville'] . ', "' . $adresse['rue'] . '")';
        $db->query($query);
        $code['rue'] = $db->insert_id;
    }

    // On regarde ensuite si l'immeuble existe déjà dans cette rue
    $query = 'SELECT *
              FROM immeubles
              WHERE rue_id = ' . $code['rue'] . '
              AND immeuble_numero LIKE "' . $adresse['immeuble'] . '"
              LIMIT 0,1';
    $sql = $db->query($query);

    // S'il existe déjà un immeuble dans la base de données,
    // on récupère l'identifiant
    if ($sql->num_rows == 1) {
        $row = $sql->fetch_assoc();
        $code['immeuble'] = $row['immeuble_id'];
    } else {
        // On rajoute l'immeuble dans la base de données
        $query = 'INSERT INTO immeubles (bureau_id, rue_id, immeuble_numero)
                  VALUES (' . $code['bureau'] . ',
                          ' . $code['rue'] . ',
                          "' . $adresse['immeuble'] . '")';
        $db->query($query);
        $code['immeuble'] = $db->insert_id;
    }

    // On traite ensuite la date de naissance
    // pour la mettre dans le format demandé par la BDD
    $naissance = explode('/', $line[6]);
    $birthdate = array($naissance[2], $naissance[1], $naissance[0]);
    $birth['day'] = implode('-', $birthdate);


    // On cherche le code d'identification de la commune de naissance
    $birth['ville'] = $line[8];
    $birth['departement'] = $line[7];

    // On cherche le titre des villes et du département corrigé
    $donnees = $fiche->renommerVille($birth['ville'], $birth['departement']);
    $query = 'SELECT *
              FROM communes
              WHERE departement_id LIKE "' . $donnees[1] . '"
              AND commune_nom LIKE "%' . $donnees[0] . '%"
              LIMIT 0,1';
    $sql = $db->query($query); $nb = $sql->num_rows;

    // Si le département n'est pas à l'étranger et qu'il y a une commune trouvée,
    // on note son code
    if ($nb == 1) {
        $row = $sql->fetch_assoc();
        $birth['ville'] = $row['commune_id'];
    } else {
        $birth['ville'] = '';
    }

    // On rajoute les informations de naissance au tableau $code
    $code['birth'] = $birth;

    $code = array_merge($adresse, $code);

    // On formate les noms et prénoms
    $etatCivil = array(
        'nom' => htmlentities(utf8_encode($line[2]), ENT_HTML5, 'UTF-8'),
        'nomUsage' => htmlentities(utf8_encode($line[3]), ENT_HTML5, 'UTF-8'),
        'prenoms' => htmlentities(utf8_encode($line[4]), ENT_HTML5, 'UTF-8')
    );

    // On va préparer l'ajout du contact à la base de données
    $query = 'INSERT INTO contacts (immeuble_id,
                                    contact_nom,
                                    contact_nom_usage,
                                    contact_prenoms,
                                    contact_naissance_date,
                                    contact_naissance_commune_id,
                                    contact_sexe,
                                    contact_electeur,
                                    contact_electeur_numero)
              VALUES (' . $code['immeuble'] . ',
                      "' . $etatCivil['nom'] . '",
                      "' . $etatCivil['nomUsage'] . '",
                      "' . $etatCivil['prenoms'] . '",
                      "' . $code['birth']['day'] . '",
                      "' . $code['birth']['ville'] . '",
                      "' . $line[5] . '",
                      "1", "' . $line[1] . '")';

    // On ajoute le contact à la base de données
    $db->query($query);

endforeach;

// On renomme le dossier pour ne pas le relancer
rename('csv/' . $file . '.csv', 'csv/' . $file . '.traite.csv');
