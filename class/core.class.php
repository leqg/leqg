<?php
/**
 * System core
 *
 * PHP version 5
 *
 * @category Core
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * System core
 *
 * PHP version 5
 *
 * @category Core
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Core
{
    /**
     * Project debug method
     *
     * @param mixed   $objet element to debug
     * @param boolean $exit  end the page execution?
     *
     * @return void
     * @static
     */
    public static function debug($objet, $exit = true)
    {
        echo '<pre class="nowrap">';

        // On affiche le tableau préformaté s'il s'agit d'un tableau
        if (is_array($objet)) {
            print_r($objet);
        } else if (is_object($objet)) {
            print_r($objet);
        } else if (is_bool($objet)) {
            echo ucwords(gettype($objet)) .
                 '<br>(<span class="wrap">' .
                 var_dump($objet) . '</span>)';
        } else if (is_numeric($objet)) {
            echo 'Numeric<br>(<span class="wrap">' . $objet . '</span>)';
        } else {
            echo ucwords(gettype($objet)).'<br>(<span class="wrap">';
            echo $objet;
            echo '</span>)';
        }

        echo '</pre>';

        // On regarde si on arrête ou non l'exécution du script à la demande
        if ($exit) {
            exit;
        }
    }

    /**
     * Prepare an SQL query existing in a file (sql/ directory)
     *
     * @param string $query file to load
     * @param string $bdd   which database?
     *
     * @return object
     * @static
     */
    public static function query(string $query, $bdd = 'link')
    {
        // On récupère le lien vers la BDD
        $link = Configuration::read('db.' . $bdd);

        // On vérifie que la requête existe
        if (file_exists("sql/$query.sql")) {
            // On récupère la requête et on retourne la requête préparée
            $query = file_get_contents("sql/$query.sql");
            return $link->prepare($query);
        } else {
            exit;
        }
    }

    /**
     * String security method before database insertion
     *
     * @param string $string  string to check
     * @param string $charset charset
     *
     * @return     string
     * @deprecated
     * @static
     */
    public static function securisationString(string $string, $charset = 'utf-8')
    {
        // On transforme les caractères spéciaux en entités HTML
        $string = htmlentities($string, ENT_QUOTES, $charset);

        // On retourne la chaîne de caractères sécurisée
        return $string;
    }

    /**
     * Format a string for a database search
     *
     * @param string $string  string to format
     * @param string $charset charset
     *
     * @return     string
     * @deprecated
     * @see        Core::searchFormat()
     * @static
     */
    public static function formatageRecherche(string $string, $charset = 'utf-8')
    {
        // On vérifie que le texte entré est bien un champ texte
        if (!is_string($string)) {
            return false;
        }

        // On sécurise le contenu envoyé
        $string = self::securisationString($string, $charset);

        // On fait une liste de caractères spéciaux à remplacer
        // basiquement par des jokers
        $char = array(' ', '_', '.', ',');

        // On remplace cette liste de caractères dans la chaîne
        $string = str_replace($char, '%', $string);

        // On retire tous les caractères spéciaux
        $special = 'acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml';
        $string = preg_replace(
            '#&([A-za-z])(?:' . $special . ');#',
            '\1',
            $string
        );
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string);
        $string = preg_replace('#&[^;]+;#', '%', $string);

        // On retourne le contenu final près à une recherche
        return $string;
    }

    /**
     * Format a data for database col name format
     *
     * @param array $array data to format
     *
     * @return     array
     * @deprecated
     * @static
     */
    public static function formatageDonnees(array $array)
    {
        if (!is_array($array)) {
            return $array;
        }

        // On initialise le nouveau tableau
        $keys = array_keys($array);

        // On détecte quel est le premier segment BDD à retirer du nom de la clé
        $segment = explode('_', $keys[0]);
        $segment = $segment[0];

        foreach ($keys as $key) {
            // On détecte les segments de la clé entrée
            $seg = explode('_', $key);

            // Si le premier segment correspond au segment à retirer,
            // on le vire du tableau
            if ($seg[0] == $segment ) {
                unset($seg[0]);
                $new_key = implode('_', $seg);
            } else {
                $new_key = implode('_', $seg);
            }

            // On transforme la clé
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Execute an array sorting
     *
     * @param array  $arr array to sort
     * @param string $col sorting col
     * @param string $dir sorting direction (SORT_ASC or SORT_DESC)
     *
     * @return array
     * @static
     */
    public static function triParColonne(array &$arr, string $col, $dir = SORT_ASC)
    {
        // On prépare le tableau de tri
        $sort_col = array();

        // On effectue une sélection des colonnes à trier
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        // On effectue le tri multidimensionnel
        array_multisort($sort_col, $dir, $arr);
    }

    /**
     * Execute a multidimentional sorting
     *
     * @param array  $arr array to sort
     * @param string $col sorting col
     * @param string $dir sorting direction (SORT_ASC or SORT_DESC)
     *
     * @return array
     * @static
     */
    public static function triMultidimentionnel(
        array &$arr,
        string $col ,
        $dir = SORT_ASC
    ) {
        // On prépare le tableau de tri
        $sort_col = array();

        // On effectue une sélection des colonnes à trier
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        // On effectue le tri multidimensionnel
        array_multisort($sort_col, $dir, $arr);
    }

    /**
     * Format a content before displaying it
     *
     * @param string $texte text to format & display
     *
     * @return void
     * @static
     */
    public static function sortie(string $texte)
    {
        // On retraite le contenu envoyé pour l'affichage
        $texte = stripslashes($texte);

        // On l'affiche
        echo $texte;
    }

    /**
     * Load a template file
     *
     * @param string $slug template name
     * @param string $nom  template submodule
     *
     * @return void
     * @static
     */
    public static function loadTemplate(string $slug, $nom = null)
    {
        if (empty($nom)) {
            include 'tpl/' . $slug . '.tpl.php';
        } else {
            include 'tpl/' . $slug . '-' . $nom . '.tpl.php';
        }
    }

    /**
     * Load header template file
     *
     * @param string $nom specific header version
     *
     * @return void
     * @static
     */
    public static function loadHeader($nom = null)
    {
        self::loadTemplate('header', $nom);
    }

    /**
     * Load footer template file
     *
     * @param string $nom specific footer version
     *
     * @return void
     * @static
     */
    public static function loadFooter($nom = null)
    {
        self::loadTemplate('footer', $nom);
    }

    /**
     * Load aside template file
     *
     * @param string $nom specific aside version
     *
     * @return void
     * @static
     */
    public static function loadAside($nom = null)
    {
        $this->loadTemplate('aside', $nom);
    }

    /**
     * Do a redirection to another page
     *
     * @param string $page     target page
     * @param string $valeur   attr value
     * @param string $attribut attr name
     *
     * @return     void
     * @deprecated
     * @see        Core::goPage()
     * @static
     */
    public static function redirect($page = null, $valeur = null, $attribut = null)
    {
        if (!empty($page) && !empty($attribut) && !empty($valeur)) {
            header(
                'Location: index.php?page=' .
                $page . '&' . $attribut . '=' . $valeur
            );
        } else if (!empty($page) && !empty($valeur)) {
            header(
                'Location: index.php?page=' .
                $page . '&id=' . $valeur
            );
        } else if (!empty($page)) {
            header(
                'Location: index.php?page=' .
                $page
            );
        } else {
            header('Location: index.php');
        }
    }

    /**
     * Redirect to an another page
     *
     * @param string|boolean $page      target page
     * @param array|boolean  $arguments args array
     * @param boolean        $redirect  return link (false) or do redirection
     *
     * @return string|void
     * @static
     */
    public static function goPage($page = null, $arguments = [], $redirect = false)
    {
        // Si $page == true, on demande une redirection immédiate
        // vers la page d'accueil
        if (is_bool($page) && $page === true) {
            header('Location: index.php');
        }

        // Si $arguments == true, on va directement vers la page demandée
        if (!empty($page) && is_bool($arguments) && $arguments === true) {
            header('Location: index.php?page=' . $page);
        }

        // On vérifie que les arguments sont bien sous la forme d'un tableau
        if (!is_array($arguments)) {
            return false;
        }

        if (!empty($page) ) {
            // On prépare l'adresse de la page d'arrivée
            $adresse = 'index.php?page=' . $page;

            // On fait une boucle selon le nombre d'arguments
            foreach ($arguments as $key => $value) {
                $adresse .= '&' . $key . '=' . $value;
            }

            // On lance la redirection
            if ($redirect) {
                header('Location: ' . $adresse);
            } else {
                echo $adresse;
            }
        } else {
            if ($redirect) {
                header('Location: index.php');
            } else {
                echo 'index.php';
            }
        }
    }

    /**
     * Return URI of an asked page
     *
     * @param string $page      target page
     * @param string $valeur    first arg value
     * @param string $attribut  first arg name
     * @param string $valeur2   second arg value
     * @param string $attribut2 second arg name
     *
     * @return     string
     * @deprecated
     * @see        Core::goPage()
     * @static
     */
    public static function returnUri(
        $page = null,
        $valeur = null,
        $attribut = null,
        $valeur2 = null,
        $attribut2 = null
    ) {
        if (!empty($page)
            && !empty($attribut)
            && !empty($valeur)
            && !empty($attribut2)
            && !empty($valeur2)
        ) {
            $url = 'index.php?page=' .
                    $page .
                    '&' .
                    $attribut .
                    '=' .
                    $valeur .
                    '&' .
                    $attribut2 .
                    '=' .
                    $valeur2;
        } else if (!empty($page)
            && !empty($attribut)
            && !empty($valeur)
            && empty($attribut2)
            && empty($valeur2)
        ) {
            $url = 'index.php?page=' .
                    $page .
                    '&' .
                    $attribut .
                    '=' .
                    $valeur;
        } else if (!empty($page)
            && is_null($attribut)
            && !empty($valeur)
            && empty($attribut2)
            && empty($valeur2)
        ) {
            $url = 'index.php?page=' .
                    $page .
                    '&id=' .
                    $valeur;
        } else if (!empty($page)
            && is_null($attribut)
            && is_null($valeur)
            && empty($attribut2)
            && empty($valeur2)
        ) {
            $url = 'index.php?page=' .
                    $page;
        } else {
            $url = 'index.php';
        }

        return $url;
    }

    /**
     * Display an asked page URI
     *
     * @param string $page      asked page
     * @param string $valeur    first arg value
     * @param string $attribut  first arg name
     * @param string $valeur2   second arg value
     * @param string $attribut2 second arg name
     *
     * @return     void
     * @deprecated
     * @see        Core::goPage()
     * @static
     */
    public static function getUrl(
        $page = null,
        $valeur = null,
        $attribut = null,
        $valeur2 = null,
        $attribut2 = null
    ) {
        echo $this->returnUri($page, $valeur, $attribut, $valeur2, $attribut2);
    }

    /**
     * Return this site domain
     *
     * @return     string
     * @deprecated use a $_ENV for this method
     * @static
     */
    public static function returnDomain()
    {
        $domain = 'http://' . $this->url;
        return $domain;
    }

    /**
     * Display this site domain
     *
     * @return     void
     * @deprecated use a $_ENV for this method
     * @static
     */
    public static function getDomain()
    {
        echo self::returnDomain();
    }

    /**
     * Format a telephone number and display it
     *
     * @param string $numero phone number
     *
     * @return void
     * @static
     */
    public static function formatPhone($numero)
    {
        if (!empty($numero)) {
            echo self::getFormatPhone($numero);
        }
    }

    /**
     * Format and return a phone number
     *
     * @param string $numero phone number
     *
     * @return string
     * @static
     */
    public static function getFormatPhone( $numero )
    {
        if (!empty($numero)) {
            return $numero{0} . $numero{1} . ' ' .
                   $numero{2} . $numero{3} . ' ' .
                   $numero{4} . $numero{5} . ' ' .
                   $numero{6} . $numero{7} . ' ' .
                   $numero{8} . $numero{9} ;
        }
    }

    /**
     * Return an event type in French
     *
     * @param string $type event type
     *
     * @return string
     * @static
     */
    public static function eventType(string $type)
    {
        // On prépare le tableau de traduction
        $types = [
            'contact' => 'Entrevue',
            'telephone' => 'Entretien téléphonique',
            'courriel' => 'Échange électronique',
            'courrier' => 'Correspondance',
            'sms' => 'Envoi SMS',
            'email' => 'Envoi d\'un email',
            'publi' => 'Publipostage',
            'porte' => 'Porte-à-porte',
            'boite' => 'Boîtage',
            'rappel' => 'Rappel',
            'autre' => 'Autre'
        ];

        // On cherche le texte correspondant au type d'événement entré
        return $types[$type];
    }
    /**
     * Format a text for a better comprehension
     *
     * @param string $affichage text to format
     *
     * @return string
     *
     * @static
     */
    public static function transformText($affichage)
    {

        $affichage = strtolower($affichage);

        // Avant les majuscules, on décale juste les lettre apostrophes
        $affichage = str_replace(' l\'', ' l\' ', $affichage);
        $affichage = str_replace(' d\'', ' d\' ', $affichage);

        // On mets en place des majuscules automatiques
        $affichage = ucwords($affichage);

        // On ajoute un espace au début pour les rues sans numéro
        $affichage = ' ' . $affichage;

        // On remplace certaines abbréviations par leur signification
        $affichage = str_replace(' Pce ', ' place ', $affichage);

        // On retire certaines majuscules
        $affichage = str_replace(' Rue ', ' rue ', $affichage);
        $affichage = str_replace(' Quai ', ' quai ', $affichage);
        $affichage = str_replace(' Pte ', ' petite ', $affichage);
        $affichage = str_replace(' Bd ', ' boulevard ', $affichage);

        $affichage = str_replace(' D ', ' du ', $affichage);
        $affichage = str_replace(' De ', ' de ', $affichage);
        $affichage = str_replace(' Du ', ' du ', $affichage);
        $affichage = str_replace(' Des ', ' des ', $affichage);
        $affichage = str_replace(' Aux ', ' aux ', $affichage);
        $affichage = str_replace(' Le ', ' le ', $affichage);
        $affichage = str_replace(' La ', ' la ', $affichage);
        $affichage = str_replace(' Les ', ' les ', $affichage);
        $affichage = str_replace(' L\' ', ' l\'', $affichage);
        $affichage = str_replace(' D\' ', ' d\'', $affichage);
        $affichage = str_replace(' Bv ', ' BV ', $affichage);

        // Lorsqu'on a un tiret suivi d'un espace, on retire l'espace
        $affichage = str_replace('- ', '-', $affichage);

        // On remplace certaines données pour la carte
        $affichage = str_replace('Vingt-deux', '22', $affichage);

        return $affichage;
    }

    /**
     * Format a string for a search
     *
     * @param string $chaine string to format
     *
     * @return string
     * @static
     */
    public static function searchFormat($chaine)
    {
        // On supprime tout ce qui n'est pas A-Z pour le remplacer par un joker
        $chaine = preg_replace('#[^A-Za-z]#', '%', $chaine);

        // On rajoute les jokers de début et de fin de chaîne
        $chaine = '%'.$chaine.'%';

        // On retourne la chaîne
        return $chaine;
    }
}
