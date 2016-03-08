<?php

namespace App;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Storage;
use Carbon\Carbon;
use Zipper;

/**
 *
 * @author Sylvain
 *
 */
class BaseUpload
{

// Propriétés

    /**
     * Nom de l'espace de stockage
     * @var string
     * @path WEB_ROOT/laravel/storage/$storage
     */
    protected $storage = 'app/';

    /**
     * Nom du dossier de stockage
     * @var string
     * @path WEB_ROOT/laravel/storage/app/$directory
     */
    protected $directory = 'app';

    /**
     * Fonction pour obtenir le chemin d'un dossier
     * À changer si le stockage se situe dans le dossier public par exemple : 'public_path'
     * @var string
     */
    protected $pathFunction = 'storage_path';

    /**
     * Chemin du dossier qui recevra les fichiers téléchargés
     * @var string
     */
    protected $uploadPath = '';

    /**
     * Constructeur de la classe
     * Tous les paramètres sont facultatifs
     * @param string $storage       Nom de l'espace de stockage
     * @param string $directory     Nom du dossier de stockage
     * @param string $pathFunction  Fonction pour obtenir le chemin d'un dossier
     * @param string $uploadPath    Chemin du dossier qui recevra les fichiers téléchargés
     */
    public function __construct($storage = 'app/', $directory = 'app', $pathFunction = 'storage_path', $uploadPath = '') {
        $this->setStorage($storage);
        $this->setDirectory($directory);
        $this->setPathFunction($pathFunction);
        $this->setUploadPath($uploadPath);
    }

// Fichiers - Mutateurs

    /**
     * Créer un nouveau dossier dans le dossier courant ($this->directory)
     * @param  string $slug Nom du nouveau dossier
     * @return Boolean      Statut de la création
     */
    public function makeDir($slug) {
        return Storage::disk($this->directory)->makeDirectory("/$slug");
    }

    /**
     * Créer un dossier dans d'un dossier parent
     * @param string  $slug   Nom du dossier parent
     * @param string  $name   Le nom du nouveau fichier (avec .extension) OU le nom du dossier
     * @return boolean        true si la création a réussie, false sinon
     */
    public function add($slug, $name) {
        return $this->hasExtension($name) ? Storage::disk($this->directory)->put("/$slug/$name", "") : Storage::disk($this->directory)->makeDirectory("/$slug/$name");
    }

    /**
     * Supprimer un dossier
     * @param  string $slug  identifiant du dossier
     * @return Boolean       Statut de la suppression
     */
    public function delete($slug) {
        return $this->isFile("/$slug") ? Storage::disk($this->directory)->delete("/$slug") : Storage::disk($this->directory)->deleteDirectory("/$slug");
    }

    /**
     * Dupliquer un dossier
     * @param  string $oldSlug   Slug du dossier existant
     * @param  string $newSlug   Slug du nouveau dossier
     * @return Boolean           Statut de la duplication
     */
    public function copy($oldSlug, $newSlug) {
        if($this->isFile("/$oldSlug"))
            return Storage::disk($this->directory)->copy("$oldSlug", "$newSlug");
            else
                $filesyst = new Filesystem();
                return $filesyst->copyDirectory(storage_path("$this->storage/$this->directory")."/$oldSlug", storage_path("$this->storage/$this->directory")."/$newSlug");
    }

    public function rename($oldSlug, $newSlug) {
        return Storage::disk($this->directory)->move("/$oldSlug", "/$newSlug");
    }

    /**
     * Ajoute une numéro unique à un chemin d'accès
     * @param  string  $path   Le chemin qui conduit au document
     * @return string          Le chemin jusqu'au document maintenant unique
     */
    public function appendRandIdToPath($path) {
        if($this->isFile("/$path")){
            $tempSlug = explode(".", $path);
            $tempSlug[count($tempSlug) - 2] = $tempSlug[count($tempSlug) - 2] . rand(10,1000);
            $tempSlug = implode("." ,$tempSlug);
        } else {
            $tempSlug = $path . rand(10,1000);
        }
        return $tempSlug;
    }

    /**
     * Télécharge et sauvegarde un fichier
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile $file   L'instance de la classe UploadedFile, suite de la séquence Request::file('name')
     * @param  string                                              $name   Nouveau nom du fichier (FACULTATIF)
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile         L'instance UploadedFile du fichier fraîchement téléchargé
     */
    public function upload(\Symfony\Component\HttpFoundation\File\UploadedFile $file, $name = null) {
        $func = $this->pathFunction;
        $filename = $name != null ? $name.$file->getClientOriginalExtension() : $file->getClientOriginalName();
        $this->datePathRenew();
        return $file->move($func($this->storage.$this->directory).$this->uploadPath, $filename);
    }

    /**
     * Extrait les fichiers d'un dossier compressé
     * @param  string $path Accès à l'archive à extraire
     * @param  string $to   Emplacement des fichiers extraits
     * @return bool         True si l'extraction est réussie, false sinon
     */
    public function unzip($path, $to) {
        return Zipper::make($path)->extractTo($to, array('__MACOSX','.DS_Store'), 2);
    }

    /**
     * Renouvelle le chemin d'enregistrement des fichiers uploadés
     * @return string Le nouveau chemin d'enregistrement des images
     */
    public function datePathRenew() {
        $this->uploadPath = '/'.Carbon::now()->format('Y').'/'.Carbon::now()->format('m').'/';
    }



// Fichiers - Accesseurs

    /**
     * Récupère le contenu du fichier en paramètre
     * @param  string $path     Chemin d'accès jusqu'au fichier
     * @return string           Le contenu du fichier
     */
    public function get($path) {
        return Storage::disk($this->directory)->get("/$path");
    }

    /**
     * Retourne les dossiers installé sur un seul niveau d'arborescence
     * @return array La liste des dossiers installés
     */
    public function nonRecursivesDirectories() {
        return Storage::disk($this->directory)->directories();
    }

    /**
     * Liste tous les fichiers que contient le lieu de stockage (de façon récurcive)
     * @return array            Le tableau contenant les différents fichiers
     */
    public function listAllFiles() {
        return Storage::disk($this->directory)->allFiles();
    }

    /**
     * Liste tous les fichiers que contient un dossier
     * @param  string $path     Le nom du dossier du dossiers dans lequel lister les fichiers
     * @return array            Le tableau contenant les différents fichiers
     */
    public function listAllFilesFromDirectory($path) {
        return Storage::disk($this->directory)->allFiles("/$path");
    }

    /**
     * Retourner le type MIME du fichier
     * @param  string $ext       Chemin relatif vers le fichier (à partir du dossier current du document)
     * @return string|false      Le type MIME du fichier
     */
    public function mimeType($filename) {
        return Storage::disk($this->directory)->mimeType($filename);
    }

    /**
     * Vérifie si le chemin est un fichier
     * @param  string  $path Chemin d'accès au fichier
     * @return boolean       true si fichier, false si non
     */
    public function isFile($path) {
        $func = $this->pathFunction;
        return \File::isFile($func($this->storage.$this->directory.$path));
    }

    /**
     * Vérifie si le nom du fichier possède une extension
     * @param  string  $name    Nom du fichier
     * @return boolean          true s'il a une extension, false si non
     */
    public function hasExtension($name) {
        $arr = explode(".", $name);
        return count($arr) > 1;
    }


// Fichiers - Méthodes statiques

  	/**
  	 * Upload un document et manipule ses données sans le stocker
  	 * @param1 $file	L'objet input correspondant au fichier (ex: Input::file('file'));
  	 * @param2 $mmode 	Le paramètre mode spécifie le type d'accès désiré au flux.
  	 * @return $handle	Retourne une ressource représentant le pointeur de fichier, ou FALSE si une erreur survient.
  	 * Voir documentation PHP -> fopen() : http://php.net/manual/fr/function.fopen.php
     * @author Joevin Castanié
     */
    public static function handle($file, $mode = "r") {
        // Ici, le fichier uploadé n'est que temporaire.
        return fopen($file->getRealPath(), $mode);
    }


// Propriétés - Accesseurs et mutateurs

    /**
     * @return string Retourne le contenu de la propriété $storage
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * Modifie le contenu de la propriété $storage
     * @param string $new La nouvelle valeur de la propriété
     */
    public function setStorage($new) {
        $this->storage = $new;
    }

    /**
     * @return string Retourne le contenu de la propriété $directory
     */
    public function getDirectory() {
        return $this->directory;
    }

    /**
     * Modifie le contenu de la propriété $directory
     * @param string $new La nouvelle valeur de la propriété
     */
    public function setDirectory($new) {
        $this->directory = $new;
    }

    /**
     * @return string Retourne le contenu de la propriété $pathFunction
     */
    public function getPathFunction() {
        return $this->pathFunction;
    }

    /**
     * Modifie le contenu de la propriété $pathFunction
     * @param string $new La nouvelle valeur de la propriété
     */
    public function setPathFunction($new) {
        $this->pathFunction = $new;
    }

    /**
     * @return string Retourne le contenu de la propriété $uploadPath
     */
    public function getUploadPath() {
        return $this->uploadPath;
    }

    /**
     * Modifie le contenu de la propriété $uploadPath
     * @param string $new La nouvelle valeur de la propriété
     */
    public function setUploadPath($new) {
        $this->uploadPath = $new;
    }

}
