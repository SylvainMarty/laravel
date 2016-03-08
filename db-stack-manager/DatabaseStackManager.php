<?php

namespace App;

use DB;

/**
 * @author Sylvain Marty
 */
class DatabaseStackManager
{
    /**
     * La connection à la base de données
     * @var DB
     */
    protected $db;

    /**
     * L'identifiant de la base de données
     * @var string
     */
    private $slug;

    /**
     * Configuration de la base de données
     * @var array
     */
    private $config;

    /**
     * Les bases de données ne devant pas être accessibles via la classe Stack
     * @var array
     */
    protected static $excepted = [];



    
    /**
     * Constructeur - instancie la connexion avec la base de données
     * @param string $slug l'identifiant de la base de données
     *
     * La base de données doit obligatoirement être enregistrée dans le fichier config/database.php dans le tableau 'connections'.
     */
    public function __construct($slug){
        $this->slug = $slug;
        $this->config = config("database.connections.$this->config");
        $this->db = DB::connection($this->slug);
    }

    /**
     * @OVERLOAD
     * Méthode magique permettant d'accéder aux méthodes de la classe DB
     * @param  mixed  $method       La methode de la classe DB à appeler
     * @param  mixed $params        les paramêtres
     * @return QueryBuilder         Le builder de requêtes
     */
    public function __call($method, $params) {
        return count($params) > 1 ? $this->db->$method($params) : $this->db->$method($params[0]);
    }

    /**
     * Retourne la liste des colonnes de la base de données
     * @return array            tableau contenant la liste des colonnes
     */
    public function getTables() {
        return $this->db->select('SHOW TABLES');
    }

    /**
     * Retourne une liste des colonnes disponibles dans la base de données
     * @param  string $table le nom de la table
     * @return array        liste des colonnes
     */
    public function getColumns($table) {
        return $this->db->getSchemaBuilder()->getColumnListing($table);
    }

    /**
     * Retourne l'identifiant de la base
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }





// STATIC METHODS

    /**
     * Retourne toutes les bases enregistrées sauf la principale
     * @return array       array de BDD
     */
    public static function all()
    {
        $collection = config('database.connections');
        return array_except($collection, self::$excepted);
    }

    /**
     * Atteindre de manière statique une base de données
     * @param  string $db       identifiant de la base de données
     * @return QueryBuilder     Le builder de requêtes SQL
     */
    public static function reach($db) {
        $stack = self::__construct($db);
        return $stack->db;
    }

    /**
     * Vérifie si la base de données est déjà enregistrée
     * @param  string $slug  identifiant de la base de données
     * @return boolean       TRUE => existe, FALSE => n'exsite pas
     */
    public static function exist($slug) {
        return in_array($slug, self::all());
    }
}
