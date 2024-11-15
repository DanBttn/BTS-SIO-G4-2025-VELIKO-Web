# Projet Veliko

## Installation en mode production

### Etape 1 : Creation du fichier .env

Créer un fichier `.env` à la racine du projet et ajouter la configuration du projet en 
prenant comme exemple le fichier `.env.example`.

Modifier les variables d'environnement pour la production :   
APP_ENV=prod   
APP_DEBUG=0  
DATABASE_URL="mysql://<username>:<password>@<host>:<port>/<database_name>?serverVersion=mariadb-<version>"


### Etape 2 : Installation des dépendances

Installation des dépendances avec composer (vendor).  
A la racine du projet, faire la commande suivante :
```bash
composer install --no-dev --optimize-autoloader
```

### Etape 3 : Implémentation de la base de données

A la racine du projet, faire la commande suivante :
```bash
php bin/console doctrine:migrations:migrate
```

### Etape 4 : Vider le cache

A la racine du projet, faire la commande suivante :
```bash
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```

