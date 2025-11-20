# 📚 **Leaf — Library Management System**

**Application Symfony de gestion de bibliothèque**
Emprunts • Retours • Caution • Pénalités • Paiements • Catalogue • Rôles

---

## ⭐ **Description**

**Leaf** est une application web développée avec **Symfony** permettant de gérer une bibliothèque de manière simple et robuste :

* Gestion complète des livres et catégories
* Emprunts / retours avec système de caution
* Gestion des rôles utilisateurs (User, Librarian, Admin)
* Interface publique catalogue + dashboard utilisateur
* Interface d'administration complète
* Système d'authentification et sécurité

Pensé pour être propre, scalable et pédagogique.

---

# 🚀 **1. Installation**

## 📦 **Prérequis**

* PHP 8.2+
* Composer
* Node.js & npm
* MySQL 8+
* Symfony CLI

---

## 🛠️ **Installation du projet**

### 1️⃣ Cloner le projet

```bash
git clone https://github.com/LucasRaffalli/Leaf-Library.git
cd leaf-library
```

### 2️⃣ Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances JavaScript
npm install
```

### 3️⃣ Configurer les variables d'environnement

Copie le fichier d'exemple :

```bash
cp .env.example .env.local
```

Puis remplis `.env.local` avec tes informations.

### 4️⃣ Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5️⃣ Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### 6️⃣ Compiler les assets

```bash
npm run dev
```

---

# 🔐 **2. Variables d'environnement**

Leaf n'inclut **aucune information sensible** dans le dépôt.

### ✔️ Fichier présent dans le repo

* `.env.example` → modèle sans données

### 📌 Exemple de `.env.example`

```env
### Leaf Library - Environment Example ###
### Copy this file to .env.local and fill the values ###

APP_ENV=dev
APP_SECRET=your-secret-key-here

DATABASE_URL="mysql://root:password@127.0.0.1:3306/leaf_library"

APP_TIMEZONE=Europe/Paris
```

### 📦 **Stack technique**

* **Backend** : Symfony 7 + Doctrine ORM
* **Frontend** : Twig + Tailwind CSS 4
* **Build tools** : Webpack Encore + PostCSS
* **Base de données** : MySQL 8+
* **Authentification** : Symfony Security + LoginFormAuthenticator
* **Assets** : SVG icons, responsive design

---

# 📚 **3. Fonctionnalités**

## 🌐 **Interface publique**
* **Page d'accueil** (`/`) : Présentation avec logo et navigation
* **Catalogue** (`/catalogue`, `/books`) :
  - Liste tous les livres avec informations complètes
  - **Filtres avancés** : 
    - Recherche texte (titre/auteur)
    - Filtre par catégorie (dropdown)
    - Filtre par statut (Disponible/Emprunté)
  - **Affichage** : Titre, auteur, statut, condition, catégories
  - **Actions** : "Voir détail" + "Emprunter" (si disponible et connecté)
* **Détail livre** (`/catalogue/{id}`) : Vue complète avec infos de caution
* **Authentification** : Inscription, connexion avec redirection dashboard

## 👤 **Espace utilisateur** (après connexion)
* **Dashboard** (`/dashboard`) : Vue d'ensemble personnalisée
* **Navigation sécurisée** : Accès aux emprunts et profil
* **Catalogue avec droits** : Boutons d'emprunt actifs
* **Déconnexion** : Logout sécurisé

## 🛠 **Interface administrateur** (CRUD générés)
* **Gestion des livres** (`/book`) : CRUD complet avec relations
* **Gestion des catégories** : CRUD categories
* **Gestion des conditions** : CRUD book conditions  
* **Gestion des statuts** : CRUD book status
* **Attribution des rôles** : Commande CLI pour assigner USER/LIBRARIAN/ADMIN

## 🔐 **Système de rôles & sécurité**
* **USER** : Accès catalogue + emprunt + dashboard
* **LIBRARIAN** : + Gestion emprunts/retours (à venir)
* **ADMIN** : Accès complet système
* **Auto-création des rôles** : Service qui garantit l'existence des rôles de base
* **Relations Many-to-Many** : User ↔ Role avec table de jointure

---

# 🗄️ **4. Base de données & Fixtures**

## 📊 **Entités & Relations**
```
User (1) ↔ (N) UserRoles (N) ↔ (1) Role
Book (N) ↔ (1) BookStatus (Disponible/Emprunté)
Book (N) ↔ (1) BookCondition (Neuf/Bon état/Usagé)  
Book (N) ↔ (N) Category (Roman, SF, Histoire...)
```

## 🎯 **Données de test automatiques**
Les fixtures (`RoleFixtures` + `BookFixtures`) créent :

### **Rôles** :
- USER, LIBRARIAN, ADMIN avec descriptions

### **Statuts & Conditions** :
- **BookStatus** : "Disponible", "Emprunté"  
- **BookCondition** : "Neuf", "Bon état", "Usagé"
- **Categories** : Roman, Science-Fiction, Histoire, Biographie, Technique, Jeunesse

### **Livres d'exemple** (5 livres) :
- "Les Misérables" (Victor Hugo) - Roman, Disponible, Bon état
- "Dune" (Frank Herbert) - SF, Emprunté, Neuf  
- "Steve Jobs" (Walter Isaacson) - Biographie, Disponible, Neuf
- "Clean Code" (Robert C. Martin) - Technique, Disponible, Bon état
- "Le Petit Prince" (Antoine de Saint-Exupéry) - Roman+Jeunesse, Disponible, Usagé

### **Charger les données** :
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

---

# 🎨 **5. Design & Frontend**

## 🎨 **Tailwind CSS 4**
- **Configuration custom** : Couleurs personnalisées avec variables CSS
- **Mode responsive** : Mobile-first design
- **Classes utilitaires** : `text-color-900`, `bg-color-500`, etc.

## 🧩 **Composants Twig réutilisables**
- **Logo SVG** (`components/logo.html.twig`) : Paramétrable (taille, couleur)
- **Boutons** (`components/button.html.twig`) : Variants `fill` et `outline`
- **Footer** (`components/footer.html.twig`) : Avec logo intégré

## ⚙️ **Build System**
- **Webpack Encore** : Compilation ES6 + CSS
- **PostCSS** : Autoprefixer + Tailwind
- **Watch mode** : Recompilation automatique

---

# 🚀 **6. Développement & Commandes**

## 💻 **Lancement**
```bash
# Serveur Symfony
symfony serve

# Assets en mode watch (terminal séparé)
npm run watch
```

## 🔧 **Commandes utiles**

### **Base de données** :
```bash
php bin/console doctrine:database:create
php bin/console make:migration  
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --no-interaction
```

### **Assets** :
```bash
npm run dev          # Compilation dev
npm run build        # Compilation production  
npm run watch        # Mode surveillance
```

### **Gestion utilisateurs** :
```bash
php bin/console app:user:add-role user@example.com ADMIN
php bin/console app:user:add-role user@example.com LIBRARIAN
```

### **Debug** :
```bash
php bin/console debug:router                    # Voir toutes les routes
php bin/console debug:container RoleInitializer # Vérifier les services
php bin/console cache:clear                     # Vider le cache
```

---

# 🧪 **7. Tests & Qualité**

```bash
# Tests (à venir)
php bin/phpunit

# Standards de code
php bin/console lint:twig templates/
php bin/console lint:yaml config/
```

---

# 👤 **Auteur**

Développé par **Lucas Raffalli**  
Projet scolaire — Concepteur Web / Développeur Fullstack

GitHub : [LucasRaffalli/Leaf-Library](https://github.com/LucasRaffalli/Leaf-Library)