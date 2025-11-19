# 📚 **Leaf — Library Management System**

**Application Symfony de gestion de bibliothèque**
Emprunts • Retours • Caution • Pénalités • Paiements • Catalogue • Rôles

---

## ⭐ **Description**

**Leaf** est une application web développée avec **Symfony** permettant de gérer une bibliothèque de manière simple et robuste :

* Gestion complète des livres et catégories
* Emprunts / retours
* Gestion de la caution et pénalités
* Suivi des paiements
* Rôles (User / Admin) et permissions
* Interface admin + interface utilisateur

Pensé pour être propre, scalable et pédagogique.

---

# 🚀 **1. Installation**

## 📦 **Prérequis**

* PHP 8.2+
* Composer
* MySQL 8+
* Symfony CLI

---

## 🛠️ **Installation du projet**

### 1️⃣ Cloner le projet

```bash
git clone https://github.com/ton-compte/leaf-library.git
cd leaf-library
```

### 2️⃣ Installer les dépendances

```bash
composer install
```

### 3️⃣ Configurer les variables d’environnement

Copie le fichier d’exemple :

```bash
cp .env.example .env.local
```

Puis remplis `.env.local` avec tes informations.

### 4️⃣ Créer la base de données

```bash
php bin/console doctrine:database:create
```

---

# 🔐 **2. Variables d’environnement**

Leaf n’inclut **aucune information sensible** dans le dépôt.

### ✔️ Fichier présent dans le repo

* `.env.example` → modèle sans données

### 📌 Exemple de `.env.example`

```env
### Leaf Library - Environment Example ###
### Copy this file to .env.local and fill the values ###

APP_ENV=dev
APP_SECRET=

DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/leaf"

APP_TIMEZONE=Europe/Paris
```

---


### 📦 **Stack technique**

* Symfony 7
* Doctrine ORM
* Twig
* Bootstrap / Tailwind
* Symfony Security

---

# 📚 **4. Fonctionnalités**

## 👤 Utilisateur

* Voir les livres
* Filtrer par catégorie / état
* Emprunter un livre
* Voir ses emprunts
* Rendre un livre
* Voir ses paiements / pénalités

## 🛠 Administrateur

* CRUD livres / catégories
* Gestion des statuts et conditions
* Valider un retour
* Définir l’état du livre (bon / abîmé / perdu)
* Gérer les cautions
* Gérer les paiements

---



# 🧪 **8. Tests**

Lancer les tests :

```bash
php bin/phpunit
```

---

# 🚀 **9. Lancer le serveur**

```bash
symfony serve
```


# 👤 **Auteur**

Développé par **Lucas Raffalli**
Projet scolaire — Concepteur Web / Développeur Fullstack

