# BaseUpload
## La classe Laravel pour simplifier les imports de fichiers et leur gestion
Cette classe est prévue pour être utilisée en tant que classe Mère afin de multiplier le nombre de configurations possibles.

### Version
1.0.0

### Fonctionnalités
##### Liste des fonctionnalités
BaseUpload propose un petit paquet de fonctionnalités très pratiques pour la gestion des fichiers et des importations
* **Version 1.0.0**
* Créer un nouveau dossier dans le dossier courant
* Créer un dossier dans d'un dossier parent
* Supprimer un dossier ou un fichier
* Dupliquer un dossier ou un fichier
* Télécharger et sauvegarder un fichier vers la destination souhaitée sur le serveur
* Extraire les fichiers d'un fichier compressé (.zip)
* Lister les fichiers et/ou les dossiers de manière récurcive ou non
* Télécharger un document et manipuler ses données sans le stocker

##### Prérequis
* [Laravel 5](https://laravel.com/docs/master) et versions supérieures
* [Chumper Zipper](https://github.com/Chumper/Zipper) (si besoin de la fontionnalité de dézippage)
* [Carbon](http://carbon.nesbot.com/) (Si archivage de documents téléchargés par dates)

### Installation

##### 1. La classe BaseUpload
Télécharger le .zip de la branche master.
Déplacer le fichier BaseUpload.php vers le dossier `LaravelRoot/app`.
`laravelRoot` correspond à la racine du projet Laravel.

##### 2. L'extension Zipper de Laravel
Aller dans le composer.json de votre projet Laravel et ajouter la ligne suivante dans le tableau `require` : `"chumper/zipper": "0.6.x"`.

Exemple :
```jsonp
"require": {
    "php": ">=5.5.9",
    "laravel/framework": "5.1.*",
    ...
    "chumper/zipper": "0.6.x"
},
```
Lancez ensuite un coup de `php composer.phar update` pour que composer prenne en compte l'ajout de l'extension.

### Configuration
##### Les propriétés de la classe
La classe BaseUpload possède 3 propriétés suivantes :
* **$directory** : Nom de l'espace de stockage (clé renseignée dans le tableau `disks` du fichier `config/filesystems.php`). Voir plus bas pour un exemple de configuration.
* **$pathFunction** : La méthode qui sera utilisée pour récupérer le chemin absolu jusqu'au dossier de stockage (ex: "storage_path" pour le dossier `WEB_ROOT/laravel/storage`, "public_path" pour `WEB_ROOT/laravel/public`, etc.). Voir [Laravel Master | Helpers](https://laravel.com/docs/master/helpers#paths)
* **$uploadPath** : Chemin du dossier qui recevra les fichiers téléchargés. Laisser vide si le dossier des fichiers téléchargés est le même que le reste des documents de l'espace de stockage.

Il est important de bien configurer ces propriétés si l'on souhaite profiter pleinement des possibilités offertes par Laravel.

##### Configuration d'un espace de stockage
Pour configurer les espaces de stockages, il suffit d'aller dans le fichier de configuration `filesystems.php` et de renseigner dans le tableau `disks` les informations du nouvel espace comme dans l'exemple ci-dessous.

Exemple :

```php
'disks' => [

    // Espace de stockage par défaut de Laravel
    'local' => [
        'driver' => 'local',
        'root'   => storage_path('app'),
    ],

    ...

    // Nouvel espace de stockage
    'uploads' => [
        'driver' => 'local',
        'root'   => base_path('../public/uploads'),
    ],
],
```


### Utilisation
#### Lister tous les fichiers de l'espace de stockage
```php
use App\BaseUpload;

class UploadsController extends Controller
{

    public function allFiles() {
        $list = BaseUpload('local');
        return $list->listAllFiles();
    }

}
```

#### Télécharger un fichier et manipuler ses données sans le stocker
Exemple avec la lecture d'un fichier CSV
```php
use Illuminate\Http\Request;
use App\BaseUpload;

class UploadsController extends Controller
{

    public function csvFileToArray(Request $request) {
        $handle = BaseUpload::handle($request::file('File'));
        return fgetcsv($handle, 0, ',');
    }

}
```
