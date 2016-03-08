# Database Stack Manager for Laravel

Database Stack Manager est un petit addon Laravel permettant de gérer plus facilement les bases de données secondaires. Cette classe permet aussi d'utiliser plus facilement le Query Builder fournit avec Laravel.

**Fonctionne uniquement sur Laravel 5 et versions supérieures.**

### Version
1.0.2

### Installation

##### 1. La classe DatabaseStackManager
Télécharger le .zip de la branche master.
Déplacer le fichier DatabaseStackManager.php vers le dossier `LaravelRoot/app`.
`laravelRoot` correspond à la racine du projet Laravel.

##### 2. Création de l'alias
Aller dans le fichier `LaravelRoot/config/app.php` et ajouter l'alias suivant dans le tableau des alias :

```php
'Stack' => App\DatabaseStackManager::class,
```
Attention, en fonction des versions, il se peut que l'ajout de l'alias écrit comme précédemment déclenche une erreur. Si c'est le cas, il suffit alors de l'écrire comme ceci :
```php
'Stack' => 'App\DatabaseStackManager',
```
Vous pouvez renommer l'alias en fonction de vos besoins afin d'éviter les conflits avec d'autres packages.

##### 3. Exemple de fichier database.php
Aller dans le fichier `LaravelRoot/config/database.php` et ajouter ajouter les informations des bases de données dans le tableau `connections` :
```php
'connections' => [
    'primeDB' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST', 'localhost'),
        'database'  => env('DB_DATABASE', 'primedb'),
        'username'  => env('DB_USERNAME', 'user'),
        'password'  => env('DB_PASSWORD', 'pwd'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'strict'    => false,
    ],

    'secondDB' => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'seconddb',
        'username'  => 'user',
        'password'  => 'pwd',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'strict'    => false,
    ],
],
```

Vous pouvez aussi utiliser d'autres drivers en fonction de votre SGBD.

### Utilisation
#### Exemple
```php
use Stack;

$stack = new Stack('secondDB');
$nbrows = $stack->table('tablename')->count();
```

#### Explication
La classe Stack doit d'abord être instanciée pour pouvoir être utilisée. Le constructeur attend en paramètre le nom de la base de données tel qu'elle a été enregistrée dans le fichier de configuration.
```php
$stack = new Stack('secondDB');
```

Une fois instancié, l'objet `$stack` représente un objet de la classe `DB`. Il suffit alors de suivre la documentation Laravel : [Laravel 5 - Query Builder](https://laravel.com/docs/5.1/queries)

### Utilisation de manière statique
#### Exemple
```php
use Stack;

$nbrows = Stack::reach('secondDB')->table('tablename')->count();
```
La méthode statique `Stack::reach($dname)` retourne un objet de la classe `DB`. Il suffit alors d'utiliser les méthodes du Query Builder.

### Les méthodes
#### Normales
| Nom de la méthode    	| Description                                                         	| Paramètres               	| Type de retour 	|
|----------------------	|---------------------------------------------------------------------	|--------------------------	|----------------	|
| `getTables()`        	| Retourne la liste des colonnes de la base de données                	|                          	| array          	|
| `getColumns($table)` 	| Retourne une liste des colonnes disponibles dans la base de données 	| string : Nom de la table 	| array          	|
| `getSlug()`          	| Retourne l'identifiant de la BDD actuellement utilisée              	|                          	| string         	|

#### Statiques
| Nom de la méthode 	| Description                                               	| Paramètres                     	| Type de retour 	|
|-------------------	|-----------------------------------------------------------	|--------------------------------	|----------------	|
| `all()`           	| Retourne toutes les bases enregistrées sauf celles qui sont dans le tableau des exceptions 	|                                	| array          	|
| `reach($db)`      	| Atteindre de manière statique une base de données         	| string : Identifiant de la BDD 	| DB             	|
| `exist($slug)`    	| Vérifie si la base de données est enregistrée             	| string : Identifiant de la BDD 	| boolean        	|
Le tableau des exceptions permet d'exclure des bases de données de la liste lors de l'utilisation de la méthode `all()`. Pour le compléter, il suffit de suivre cet exemple :
```php
protected static $excepted = [
    'primeDB',
    'anotherDB'
];
```
