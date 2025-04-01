# Projet Veliko

## Installation

### Etape 1 : Cloner le projet

Sur un terminal git à l'endroit où vous souhaitez cloner le projet, faire la commande suivante  :

```
git clone https://github.com/ort-montreuil/BTS-SIO-G4-2025-VELIKO-Web.git
```

### Etape 2 : Configuration du projet

Créer un fichier `.env` à la racine du projet et ajouter la configuration du projet.
Prendre comme exemple le fichier `.env.example`

### Etape 3 : Installation des dépendances

Installation des dépendances avec composer (vendor).  
A la racine du projet, faire la commande suivante :
```bash
composer install
```

### Etape 4 : Lancer la base de données (avec Docker)

Ouvrir Docker et à la racine du projet, faire la commande suivante :
```bash
docker compose up -d
```
### Etape 5 : Implémenter la base de données

A la racine du projet, faire la commande suivante :
```bash
symfony console doctrine:migrations:migrate
```

### Etape 6 : Insérer les données de test dans la base de données

A la racine du projet, faire la commande suivante :
```bash
symfony console doctrine:fixtures:load
```

Des utilisateurs, un administrateur et des reservations seront insérés dans la base de données.

### Etape 7 : Lancer le serveur

A la racine du projet, faire la commande suivante :
```bash
symfony serve -d
```
Le terminal renverra l'adresse du serveur local pour accéder au projet.

Vous pourrrez vous connecter avec les identifiants suivants :
- **email** : user-1@veliko.com
- **mot de passe** : password

## Fermeture du projet

### Etape 1 : Arrêter les images docker 
``` bash
docker compose down 
```
### Etape 2 : Arrêter le serveur 
``` bash
symfony server:stop 
```


### Lien notion pour les issues :
https://www.notion.so/Issues-cb3fd228281c415799af3aac9261dad4?pvs=4
