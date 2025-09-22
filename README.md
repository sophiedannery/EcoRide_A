# EcoRide

[EcoRide](https://www.ecoride-app.fr/) est une application web de covoiturage pensée pour promouvoir une mobilité économique et écologique. Conçue pour offrir une expérience fluide à ses utilisateurs, la plateforme permet de rechercher, proposer et réserver des trajets en voiture tout en valorisant les trajets réalisés avec des véhicules électriques.
L'application intègre des fonctionnalités telles que l'authentification sécurisée, un système de réservation avec gestion des crédits, des filtres dynamiques, une modération des avis, ainsi qu'un tableau de bord administrateur.

## Pré-requis

### Stack technique

- Front-end : HTML5 / CSS3 / Javascript avec Bootstrap
- Back-end : [PHP](https://www.php.net/downloads.php) >= 8.1 + avec le framework Symfony
- Gestion des dépendances : [Composer](https://getcomposer.org/download/)
- Base de données relationnelle : [MySQL](link)
- Base de données NoSQL : [MongoDB](link)
- Déploiement local : [Symfony CLI](https://symfony.com/download)
- Contrôle de version : [Git](https://git-scm.com/downloads)

### Extensions utiles pour [VS Code](https://code.visualstudio.com/) :

- PHP Intelephense
- PHP Getters & Setters
- Twig Language 2
- YAML
- dotenv
- SonarQube

## Installation

1. Cloner le dépôt

```bash
git clone  https://github.com/<ton-utilisateur>/Ecoride_A.git
cd Ecoride_A
```

Remplace <ton-utilisateur> par ton nom d'utilisateur GitHub ou modifie le lien avec l'URL réelle du dépôt.

2. Installer les dépendances PHP avec Composer

```bash
composer install
```

3. Configurer les variables d'environnement
   Duplique le fichier .env :

```bash
cp .env .env.local
```

Puis configure les variables suivantes dans .env.local selon ton environnement :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/ecoride"
MONGODB_URL="mongodb+srv://<username>:<password>@<cluster>.mongodb.net/ecoride"
APP_ENV=dev
APP_SECRET=your_app_secret
MAILER_DSN=smtp://login:password@mailtrap.io
```

4. Créer la base de données MySQL et lancer les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Charger les données de test(optionnel)

```bash
php bin/console doctrine:fixtures:load
```

Ou, si tu veux exécuter les fichiers SQL manuellement :

```bash
mysql -u <user> -p ecoride < data.sql
```

## Utilisation

Lancer le serveur Symfony avec la commande `symfony server:start`

Puis accède à l'application via [http://localhost:8000](http://localhost:8000)

## Structure du projet

| Dossiers      | Description                                                   |
| ------------- | ------------------------------------------------------------- |
| public/       | Fichiers accessibles publiquement (CSS, images, index.php...) |
| src/          | Code source PHP (contrôleurs, entités, repository...)         |
| templates/    | Vues Twig                                                     |
| migrations/   | Fichiers de migration Doctrine                                |
| .env/         | Fichier d'environnement                                       |
| composer.json | Dépendances PHP                                               |
| README.md     | Documentation projet                                          |

## Fonctionnalités principales

Résumé des User Stories / fonctionnalités implémentées :

- Recherche d'itinéraires et filtres dynamiques
- Réservation avec gestion des crédits
- Création d'un compte passager ou chauffeur
- Espace utilisateur avec historique et préférences
- Gestion d'avis
- Tableau de bord administrateur (statistiques, gestion des comptes)

## Gitflow et organisation du code

### Structures des branches

- **master** : branche principale contenant les versions stables et déployées en production.
- **dev** : branche de développement intégrant les fonctionnalités testées, en attente de déploiement.
- **feature/nom-de-la-fonctionnalité** : branche créée pour chaque nouvelle fonctionnalité.
- **fix/nom-du-bug** : branche dédiée à la correction d'un bug spécifique.

### Processus de développement :

1. Création d'une nouvelle fonctionnalité : branche `feature/...` à partir de `dev`.
2. Développement local et commits fréquents : (`feat:`, `fix:`...).
3. Tests manuels.
4. Merge vers `dev` une fois la fonctionnalité testée et validée.
5. Merge de `dev` vers `master` uniquement lors d'un déploiement.

## Visuel de l'application

## Sécurité et bonnes pratiques

- Authentification sécurisée avec mot de passe hashé (bcrypt)
- Protection CSRD (Symfony)
- Requêtes sécurisées avec Doctrine (Anti-SQLi)
- Validation côté serveur des formulaires
- RGPD : mentions légales / gestion des données / https

## Tests

Test manuels :

- création et connexion utilisateur
- réservation avec gestion de crédits
- démarrer/arrêter/valider un trajet
- affichage conditionnel selon les rôles
- modération des avis
- annulation d'un covoiturage

_Des tests automatisés vont être ajoutés_

## Déploiement

L'application **EcoRide** est déployée sur la plateforme [Heroku](https://www.heroku.com/)

### URL de production

https://ecoride-app.fr

### Étapes de déploiement

1. Création de l'application Heroku

```bash
heroku login
heroku create ecoride-app
```

2. Définition du Procfile
   `web: heroku-php-apache2 public/`

3. Configuration des variables d'environnement

```bash
heroku config:set APP_ENV=prod
heroku config:set APP_SECRET=your_app_secret
heroku config:set MAILER_DSN=smtp://user:pass@mailtrap.io
heroku config:set MONGODB_URL="mongodb+srv://..."
```

4. Connexion à la base MySQL via JawsDB

```bash
heroku addons:create jawsdb:kitefin
heroku config:set DATABASE_URL=$(heroku config:get JAWSDB_URL)
```

5. Exécution des migrations Doctrine
   `php bin/console doctrine:migrations:migrate`

6. Déploiement du code
   `git push heroku master`

### Sécurité en production

- HTTPS activé automatiquement (Let's Encrypt via Heroku)
- Redirection forcée vers l'URL sécurisée https://ecoride-app.fr
- Variables sensibles stockées en dehors du code source
- Auncun fichier `.env` versionné grâce au `.gitignore`

### Domaine personnalisé

Le nom de domaine _ecoride-app.fr_ a été acheté chez [Gandi](https://www.gandi.net/fr) et configuré pour pointer vers **Heroku** via :

- un enregistrement **CNAME**
- un enregistrement **ALIAS**

Toutes les requêtes sont redirigées vers l'URL https://ecoride-app.fr.

## Fichiers SQL

### schema.sql

Le fichier [schema.sql](https://github.com/sophiedannery/EcoRide_A/blob/master/db/schema.sql) contient **le script de création des tables** nécessaires au fonctionnement de l'application.

Tables créées : `user`, `vehicule`, `trajet`, `reservation`, `avis`, `suspension`, `transaction`

**Commande pour exécuter :**

```bash
mysql -u root -p ecoride < schema.sql
```

### data.sql

Le fichier [data.sql](https://github.com/sophiedannery/EcoRide_A/blob/master/db/data.sql) contient **un jeu de données** permettant de tester l'application. Il comprend notamment :

- des utilisateurs avec différents rôles (admin, employé, utilisateur)
- des véhicules et trajets (écologiques ou non)
- des réservations à venir et terminées
- des avis modérés (validés, refusés, en attente)
- une suspension
- des transactions détaillées

**Commande pour insérer les données :**

```bash
mysql -u root -p ecoride < data.sql
```

Il est possible d'adapter les identifiants, mot de passe, crédits et autre selon vos besoins.

## Identifiants de test

Les identifiants de test sont disponibles dans le [Manuel d'utilisation](https://github.com/sophiedannery/EcoRide_A/blob/master/docs/Manuel%20d'utilisation/EcoRide%20-%20Manuel%20d'utilisation.pdf).

## Ressources supplémentaires

- 🎨 [Charte graphique](https://github.com/sophiedannery/EcoRide_A/blob/master/docs/EcoRide%20-%20Charte%20Graphique.pdf)
- 🧩 [Diagramme de séquence](https://github.com/sophiedannery/EcoRide_A/blob/master/docs/Diagramme%20de%20s%C3%A9quence.pdf)
- 🧩 [Diagramme de cas d'utilisation](https://github.com/sophiedannery/EcoRide_A/blob/master/docs/Diagramme%20d'utilisation.pdf)
- 🧩 [Diagramme de classe](https://github.com/sophiedannery/EcoRide_A/blob/master/docs/Diagramme%20de%20classe.pdf)
- 🛠️ [Documentation technique](link)
