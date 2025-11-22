# 📚 **Leaf — Library Management System**

**Application Symfony de gestion de bibliothèque professionnelle**  
Emprunts • Retours • Caution • Catalogue • Rôles • Sécurité renforcée

[![Symfony](https://img.shields.io/badge/Symfony-7.3-black.svg)](https://symfony.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-PHPUnit-green.svg)](https://phpunit.de)
[![Security](https://img.shields.io/badge/Security-9.0/10-brightgreen.svg)](#-sécurité)
[![Quality](https://img.shields.io/badge/Quality-8.8/10-success.svg)](#-qualité-du-code)

---

## ⭐ **Description**

**Leaf** est une application web moderne développée avec **Symfony 7.3** permettant de gérer une bibliothèque de manière professionnelle et sécurisée :

### 🎯 Fonctionnalités Principales
* 📚 Gestion complète des livres, catégories, états et statuts
* 🔄 Système d'emprunts / retours avec caution et pénalités
* 👥 Gestion des rôles utilisateurs (User, Librarian, Admin)
* 🌐 Interface publique catalogue + dashboard utilisateur personnalisé
* 🛠️ Interface d'administration CRUD complète
* 🔐 **Authentification sécurisée** avec rate limiting et password policy renforcée
* ✅ **Tests automatisés** avec PHPUnit
* 📊 **Architecture service-oriented** avec séparation des responsabilités

---

## 🏆 **Qualité & Sécurité**

### Note Globale : **8.8/10**

| Aspect | Score | Détails |
|--------|-------|---------|
| 🔒 **Sécurité** | 9.0/10 | RBAC, CSRF, Rate Limiting, Password Policy forte |
| 💎 **Qualité Code** | 9.0/10 | Service Layer, Tests unitaires, Clean Architecture |
| 🏗️ **Architecture** | 8.5/10 | MVC, Services, Event Subscribers, Exceptions métier |
| 💾 **Database** | 8.0/10 | Doctrine ORM, Migrations, Relations normalisées |

### ✅ Points Forts
- ✅ **Tests automatisés** : 17 tests unitaires (BorrowValidator, RegistrationService, BookAvailabilityService)
- ✅ **Rate limiting** : Protection brute-force (5 tentatives/15min par IP, 3/5min par email)
- ✅ **Password policy** : 12 caractères minimum avec complexité (maj, min, chiffre, spécial)
- ✅ **Exceptions personnalisées** : Gestion d'erreurs centralisée avec logging
- ✅ **Service Layer robuste** : Séparation business logic / entités
- ✅ **CSRF Protection** : Sur tous les formulaires sensibles
- ✅ **Email validation** : Contraintes strictes sur inscription

---

## 🚀 **1. Installation**

### 📦 **Prérequis**

* PHP 8.2+
* Composer 2.0+
* Node.js 18+ & npm
* MySQL 8.0+
* Symfony CLI (recommandé)

---

### 🛠️ **Installation du projet**

#### 1️⃣ Cloner le projet

```bash
git clone https://github.com/LucasRaffalli/Leaf-Library.git
cd leaf-library
```

#### 2️⃣ Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances JavaScript
npm install
```

#### 3️⃣ Configurer les variables d'environnement

Copier le fichier d'exemple :

```bash
cp .env.example .env.local
```

Puis remplir `.env.local` avec vos informations :

```env
### Leaf Library - Environment Example ###
### Copy this file to .env.local and fill the values ###

APP_ENV=dev
APP_SECRET=your-secret-key-here

DATABASE_URL="mysql://root:password@127.0.0.1:3306/leaf_library"

APP_TIMEZONE=Europe/Paris
```

#### 4️⃣ Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### 5️⃣ Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

> ⚠️ **Important** : Les fixtures créent un utilisateur admin par défaut.  
> Pensez à changer le mot de passe en production !

#### 6️⃣ Compiler les assets

```bash
npm run dev
```

#### 7️⃣ Lancer le serveur

```bash
# Serveur Symfony
symfony serve

# Assets en mode watch (terminal séparé)
npm run watch
```

🎉 L'application est accessible sur **http://localhost:8000**

---

## 🔐 **2. Sécurité**

### 🛡️ **Authentification & Autorisation**

#### **Système de Rôles (RBAC)**
- **USER** : Accès catalogue, emprunts, dashboard personnel
- **LIBRARIAN** : + Gestion des emprunts/retours (à venir)
- **ADMIN** : Accès complet au système et interface d'administration

#### **Protection CSRF**
Tous les formulaires sensibles sont protégés :
- Formulaires de suppression (livres, catégories)
- Retours d'emprunts avec tokens uniques
- Formulaire de connexion

#### **Password Policy Renforcée** ✨ NOUVEAU
Politique de mot de passe robuste depuis v2.0 :
- **12 caractères minimum** (au lieu de 6)
- Au moins **1 majuscule**
- Au moins **1 minuscule**
- Au moins **1 chiffre**
- Au moins **1 caractère spécial** (@$!%*?&#)

Exemples :
- ✅ `SecurePass123!`
- ✅ `MyP@ssw0rd2024`
- ❌ `password` (trop court, pas de complexité)

#### **Rate Limiting** ✨ NOUVEAU
Protection contre les attaques brute-force :
- **Par IP** : 5 tentatives maximum / 15 minutes
- **Par email** : 3 tentatives maximum / 5 minutes
- **Logging automatique** de toutes les tentatives
- Messages flash informatifs pour l'utilisateur

#### **Custom UserChecker**
Vérifie le statut actif des comptes avant authentification.

#### **Email Validation**
Contraintes strictes sur les adresses email (inscription et profil).

---

### 🔒 **Exceptions & Logging**

#### **Exceptions Métier Personnalisées** ✨ NOUVEAU
- `BookNotAvailableException` : Livre non disponible (archivé ou emprunté)
- `UserNotActiveException` : Compte utilisateur désactivé
- `BorrowLimitExceededException` : Limite d'emprunts atteinte (5 max)

#### **Gestion Centralisée**
- `ExceptionSubscriber` : Intercepte et logue toutes les exceptions métier
- Flash messages cohérents
- Logging avec contexte (user, IP, timestamp)

---

## 📚 **3. Architecture & Services**

### 🏗️ **Service Layer**

#### **Services Métier**
- `BookAvailabilityService` : Logique de disponibilité des livres
- `BorrowValidator` : Validation des règles métier d'emprunt
- `RegistrationService` : Encapsulation du processus d'inscription
- `RoleInitializerService` : Gestion des rôles système
- `DashboardStatsService` : Statistiques utilisateur/admin

#### **Event Subscribers**
- `ExceptionSubscriber` : Gestion centralisée des erreurs
- `LoginRateLimiterSubscriber` : Rate limiting sur connexion

#### **Security**
- `LoginFormAuthenticator` : Authentification personnalisée
- `UserChecker` : Vérification statut compte actif

---

### 🗄️ **Base de Données**

#### **Entités & Relations**
```
User (1) ↔ (N) UserRoles (N) ↔ (1) Role
User (1) → (N) Borrow (N) ← (1) Book
Book (N) ↔ (1) BookStatus (Disponible/Emprunté/En cours/En retard)
Book (N) ↔ (1) BookCondition (Neuf/Bon état/Usagé)
Book (N) ↔ (N) Category (Roman, SF, Histoire...)
Borrow (N) ↔ (1) BorrowStatus (En cours/En retard/Rendu)
```

#### **Migrations Doctrine**
Toutes les modifications de schéma sont versionnées via Doctrine Migrations.

---

## 🧪 **4. Tests & Qualité**

### ✅ **Tests Automatisés** ✨ NOUVEAU

#### **Configuration PHPUnit**
```bash
# Installer PHPUnit (si nécessaire)
composer require --dev phpunit/phpunit symfony/test-pack

# Lancer tous les tests
php vendor/bin/phpunit

# Avec couverture de code
php vendor/bin/phpunit --coverage-html coverage/
```

#### **Suites de Tests**
- `BorrowValidatorTest` : 7 tests (validation métier)
- `RegistrationServiceTest` : 3 tests (inscription utilisateur)
- `BookAvailabilityServiceTest` : 7 tests (disponibilité livres)

**Total : 17 tests unitaires** ✅

---

### 📊 **Linting & Qualité**

```bash
# Validation templates Twig
php bin/console lint:twig templates/

# Validation configuration YAML
php bin/console lint:yaml config/

# Vider le cache
php bin/console cache:clear
```

---

## 📋 **5. Fonctionnalités Détaillées**

### 🌐 **Interface Publique**

#### **Page d'Accueil** (`/`)
Présentation avec logo et navigation vers catalogue/connexion.

#### **Catalogue** (`/catalogue`, `/books`)
- Liste complète des livres
- **Filtres avancés** :
  - Recherche texte (titre/auteur)
  - Filtre par catégorie (dropdown)
  - Filtre par statut (Disponible/Emprunté)
- Affichage : Titre, auteur, statut, condition, catégories
- Actions : "Voir détail" + "Emprunter" (si disponible et connecté)

#### **Détail Livre** (`/catalogue/{id}`)
Vue complète avec montant de caution, description, disponibilité.

#### **Authentification**
- Inscription avec validation email et password policy forte
- Connexion sécurisée avec rate limiting
- Redirection automatique vers dashboard après connexion

---

### 👤 **Espace Utilisateur**

#### **Dashboard** (`/dashboard`)
- Vue d'ensemble personnalisée
- Statistiques : emprunts actifs, retards, historique
- Montant total des cautions en cours
- Liste des emprunts actifs avec dates de retour

#### **Mes Emprunts**
- Historique complet
- Détails par emprunt : livre, dates, statut, pénalités

#### **Catalogue avec Droits**
Boutons d'emprunt actifs sur livres disponibles.

---

### 🛠️ **Interface Administrateur**

Accès via `/admin` (rôle ADMIN requis).

#### **CRUD Complets**
- `/book` : Gestion livres (création, édition, archivage)
- `/category` : Gestion catégories
- `/book-condition` : Gestion états (Neuf, Bon état, Usagé)
- `/book-status` : Gestion statuts (Disponible, Emprunté)

#### **Gestion Utilisateurs**
```bash
# Attribution de rôles via CLI
php bin/console app:user:add-role user@example.com ADMIN
php bin/console app:user:add-role user@example.com LIBRARIAN
```

---

## 📦 **6. Stack Technique**

### **Backend**
- **Framework** : Symfony 7.3
- **ORM** : Doctrine 3.5
- **Validation** : Symfony Validator
- **Sécurité** : Symfony Security Bundle + Rate Limiter
- **Tests** : PHPUnit 11

### **Frontend**
- **Template Engine** : Twig 3
- **CSS Framework** : Tailwind CSS 4
- **Build Tool** : Webpack Encore 4
- **JavaScript** : Stimulus + Turbo (Hotwire)

### **Database**
- **SGBD** : MySQL 8.0+
- **Migrations** : Doctrine Migrations
- **Fixtures** : Doctrine Data Fixtures

### **DevOps**
- **Package Manager** : Composer 2 + npm
- **Server** : Symfony Local Web Server

---

## 🔧 **7. Commandes Utiles**

### **Base de Données**
```bash
# Créer la base
php bin/console doctrine:database:create

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

### **Assets**
```bash
npm run dev          # Compilation développement
npm run build        # Compilation production
npm run watch        # Mode surveillance (auto-recompilation)
```

### **Tests**
```bash
# Tous les tests
php vendor/bin/phpunit

# Suite spécifique
php vendor/bin/phpunit tests/Service/BorrowValidatorTest.php

# Avec couverture
php vendor/bin/phpunit --coverage-html coverage/
```

### **Debug**
```bash
# Routes
php bin/console debug:router

# Services
php bin/console debug:container

# Configuration
php bin/console debug:config framework

# Rate limiters
php bin/console debug:config framework rate_limiter
```

---

## 🎨 **8. Design & UI**

### **Tailwind CSS 4**
- Configuration personnalisée avec variables CSS
- Design responsive mobile-first
- Classes utilitaires : `text-color-900`, `bg-color-500`

### **Composants Twig Réutilisables**
- `components/logo.html.twig` : Logo SVG paramétrable
- `components/button.html.twig` : Boutons variants (fill, outline)
- `components/footer.html.twig` : Footer avec logo intégré
- `components/navbar.html.twig` : Navigation responsive

### **Build System**
- **Webpack Encore** : Compilation ES6 + CSS
- **PostCSS** : Autoprefixer + Tailwind processing
- **Watch Mode** : Recompilation automatique

---

## 🎯 **9. Fixtures & Données de Test**

### **Rôles**
- USER, LIBRARIAN, ADMIN avec descriptions

### **Statuts & Conditions**
- **BookStatus** : "Disponible", "Emprunté"
- **BorrowStatus** : "En cours", "En retard", "Rendu"
- **BookCondition** : "Neuf", "Bon état", "Usagé"

### **Catégories**
Roman, Science-Fiction, Histoire, Biographie, Technique, Jeunesse

### **Livres d'Exemple** (5 livres)
- "Les Misérables" (Victor Hugo) - Roman
- "Dune" (Frank Herbert) - SF
- "Steve Jobs" (Walter Isaacson) - Biographie
- "Clean Code" (Robert C. Martin) - Technique
- "Le Petit Prince" (Antoine de Saint-Exupéry) - Roman + Jeunesse

### **Charger les Données**
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

---

## 🚧 **10. Roadmap**

### **Priorité Haute**
- [ ] HTTP Security Headers (NelmioSecurityBundle)
- [ ] Tests d'intégration (contrôleurs)
- [ ] PHPStan niveau 6+

### **Priorité Moyenne**
- [ ] 2FA (Authentification à 2 facteurs)
- [ ] Soft delete généralisé
- [ ] ENUM pour statuts (PHP 8.1+)
- [ ] API REST (API Platform)

### **Nice to Have**
- [ ] Notifications email (emprunts, retards)
- [ ] Export PDF des emprunts
- [ ] Dashboard analytics avancé
- [ ] Système de réservation

---

## 📚 **11. Documentation**

### **Guides**
- [Installation](./docs/installation.md)
- [Sécurité](./docs/security.md)
- [Architecture](./docs/architecture.md)
- [Tests](./docs/testing.md)

### **Audits**
- [Audit Qualité (8.8/10)](./docs/audit_apres_ameliorations.md)
- [Walkthrough Améliorations](./docs/walkthrough.md)
- [Security Hardening](./docs/security_walkthrough.md)

---

## 👤 **Auteur**

**Lucas Raffalli**  
Concepteur Web / Développeur Fullstack  
📧 Email: [contact](mailto:lucas.raffalli@example.com)  
🔗 GitHub: [LucasRaffalli](https://github.com/LucasRaffalli)

---

## 📄 **Licence**

Projet pédagogique — Tous droits réservés  
© 2024-2025 Lucas Raffalli

---

## 🙏 **Remerciements**

- Symfony Community
- Doctrine Project
- Tailwind CSS Team

---

**⭐ Si ce projet vous a plu, n'hésitez pas à laisser une étoile sur GitHub !**