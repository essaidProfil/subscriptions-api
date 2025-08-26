# 📌 Subscriptions API (Symfony 7.3 + Docker)

Ce projet fournit une **API REST** pour gérer des utilisateurs, des produits, des options tarifaires et des abonnements.  
Il est structuré en deux sous-dossiers dans le même dépôt :

- **subscription-app** : Application Symfony 7.3 (API REST).
- **subscription-infra** : Infrastructure Docker (PHP-FPM, Nginx, MySQL, PhpMyAdmin).

---

## 🚀 Fonctionnalités

- **Utilisateurs**
  - Authentification via JWT
  - Rôles : `ROLE_USER`, `ROLE_ADMIN`
  - Un utilisateur ne peut voir **que ses propres abonnements**

- **Produits & Pricing Options**
  - Un produit peut avoir **plusieurs options tarifaires** (ex. mensuel, annuel, premium)
  - Exemple : `Premium BASIC`, `Premium PRO`, `Musique YEARLY`

- **Abonnements**
  - Un utilisateur peut s’abonner à un ou plusieurs produits
  - Un abonnement est actif pour une période donnée
  - Un abonnement peut être annulé mais reste valide jusqu’à `endsAt`

---

## 📦 Prérequis

- Docker & Docker Compose
- PHP ≥ 8.2 (si usage hors Docker)
- Composer (si usage hors Docker)

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/<ton-user>/subscriptions-api.git
cd subscriptions-api
```

Arborescence attendue :

```
subscriptions-api/
  subscription-app/
  subscription-infra/
```

---

### 2. Lancer les conteneurs

Depuis `subscription-infra/` :

```bash
docker-compose up -d --build
```

Services exposés :

- **API Symfony** : [http://localhost:8000](http://localhost:8000)  
- **PhpMyAdmin** : [http://localhost:8081](http://localhost:8081)

---

### 3. Initialiser la base

Depuis le conteneur backend :

```bash
docker-compose exec backend bash

# Drop et création de la base
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures (users, rôles, produits, options…)
php bin/console doctrine:fixtures:load --no-interaction
```

---

## 🔐 Authentification

L’API utilise **LexikJWTAuthenticationBundle**.

1. **Login**

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "adminpass"
}
```

2. **Réponse**

```json
{ "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..." }
```

3. **Utilisation**

Dans Postman ou autre client HTTP :  
```
Authorization: Bearer <token>
```

---

## 📡 Endpoints principaux

### 🔑 Auth
- `POST /api/auth/login` → Connexion et récupération d’un JWT

### 👤 Utilisateurs
- `POST /api/adduser` → Créer un utilisateur (ROLE_ADMIN requis)

### 🛍️ Produits
- `GET /api/products` → Liste les produits avec options tarifaires
- `POST /api/product` → Ajouter un produit avec ses options (ROLE_ADMIN requis)

### 📃 Abonnements
- `GET /api/subscriptions` → Liste les abonnements de l’utilisateur courant
- `POST /api/subscribe` → Créer un abonnement  
  Exemple body :
  ```json
  {
    "priceOptionId": 131,
    "endsAt": "2025-12-31T23:59:59Z",
    "autoRenew": true,
    "note": "Offre spéciale Black Friday"
  }
  ```
- `POST /api/subscriptions/cancel` → Annuler l’abonnement actif (reste valide jusqu’à `endsAt`)
- *(alternative REST)* : `DELETE /api/subscriptions/{id}`

---

## 📖 Démo avec Postman

Une collection Postman est fournie dans :  
👉 `docs/SubscriptionsAPI.postman_collection.json`

Elle contient :
- Login
- Liste des produits
- Créer un abonnement
- Mes abonnements
- Annuler un abonnement

---

Posibilités :
- Creation d'utilisateurs et produits,
- Assigner un abonnement a un utilisateur,
- Anuler l'abonnement,
- Afficher tous les abonnements inscrits.
- ---
## 🧪 Règles métier

- Un utilisateur peut avoir **plusieurs abonnements actifs**
- Un produit peut avoir **plusieurs tarifs**
- Un abonnement **annulé** reste valide jusqu’à sa date de fin
- Un utilisateur ne peut voir **que ses propres abonnements**
- Les **admins** peuvent gérer les utilisateurs et produits

---

## 🛠️ Services inclus (Docker)

- `backend` → PHP-FPM + Symfony 7.3
- `nginx` → Proxy HTTP
- `database` → MySQL 8
- `phpmyadmin` → Interface d’administration MySQL

---

## 🚀 Commandes utiles

```bash
# Verifier la version Symfony
# Depuis /subscription-app 
php bin/console --version

symfony serve                        # Lancement symfony
./vendor/bin/phpunit                 # T.U/U.T - Test Unitaire/Unit Tests

php bin/console about                # Infos sur le projet et l'environnement
php bin/console debug:router         # Liste toutes les routes
php bin/console debug:container      # Voir les services dispo
php bin/console debug:autowiring     # Vérifier les classes dispo pour l'autowiring
php bin/console debug:config doctrine # Vérifier la config Doctrine

```

```bash
# Depuis /subscription-infra
# Vérifier les routes
docker-compose exec backend php bin/console debug:router

# Vérifier les entités
docker-compose exec backend php bin/console doctrine:mapping:info

# Rejouer migrations
docker-compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 👤 Création d’un administrateur

Les fixtures créent par défaut :

```yaml
email: admin@example.com
password: adminpass
roles: [ROLE_ADMIN, ROLE_USER]
```

### Générer un mot de passe hashé manuellement

Si tu veux créer un utilisateur directement en base ou dans des fixtures, tu peux générer un mot de passe hashé :

```bash
docker-compose exec backend php bin/console security:hash-password
```

Exemple :

```
--- Symfony Password Hasher ---

 Type in your password to hash: adminpass
 ------------------------------
 Hashed password: $2y$13$5Xfg3DhP8Oai5HzYAnjP6OhQp0XeW1pD5gDXtVQ0i1p4J5vA6O2du
```

Tu pourras ensuite mettre ce hash dans ta fixture ou directement dans la base.

---
