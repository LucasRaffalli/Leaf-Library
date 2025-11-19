# Base Symfony prête à l'emploi

## Fonctionnalités incluses
- Symfony 7
- Support Twig (templates)
- Doctrine ORM (base de données)
- Configuration .env pour la connexion MySQL

## Configuration de la base de données

Modifiez le fichier `.env` pour adapter la connexion à votre base MySQL :

```
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_bdd?serverVersion=8.0.32&charset=utf8mb4"
```

Par défaut :
```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

Créez la base de données (après avoir adapté le .env) :

```
php bin/console doctrine:database:create
```

## Lancer le serveur de développement

```
symfony serve
```

Ou avec PHP :
```
php -S localhost:8000 -t public
```

## Générer une entité et migrer

```
php bin/console make:entity
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Accès Twig
Les templates sont dans le dossier `/templates`.

---

**NB :**
- Le mot de passe et le nom de la base sont à adapter à votre environnement.
- Pour installer le CLI Symfony : https://symfony.com/download
