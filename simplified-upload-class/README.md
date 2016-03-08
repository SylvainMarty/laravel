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
* Supprimer un dossier
* Dupliquer un dossier
* Télécharger et sauvegarder un fichier vers la destination souhaitée sur le serveur
* Extraire les fichiers d'un fichier compressé (.zip)
* Lister les fichiers et/ou les dossiers de manière récurcive ou non
* Télécharger un document et manipuler ses données sans le stocker

##### Prérequis
* [Laravel 5](https://laravel.com/docs/master) et versions supérieures
* [Chumper Zipper](https://github.com/Chumper/Zipper) (si besoin de la fontionnalité de dézippage)
* [Carbon](http://carbon.nesbot.com/)

### Installation

##### 1. La classe BaseUpload
Télécharger le .zip de la branche master.
Déplacer le fichier BaseUpload.php vers le dossier `LaravelRoot/app`.
`laravelRoot` correspond à la racine du projet Laravel.

##### 2. L'extension Zipper de Laravel
Allez dans votre composer.json et ajoutez la ligne suivante dans le tableau `require` : `"chumper/zipper": "0.6.x"`

Exemple :
```jsonp
"require": {
    "php": ">=5.5.9",
    "laravel/framework": "5.1.*",
    ...
    "chumper/zipper": "0.6.x"
},
```


### Utilisation
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
