# 🛍️ EShop - Plateforme E-Commerce Moderne

Une plateforme e-commerce moderne et sécurisée construite avec **PHP 8**, **MySQL** et un design **dark mode** élégant.

![License](https://img.shields.io/badge/license-MIT-green.svg)
![PHP Version](https://img.shields.io/badge/php-8.0+-blue.svg)
![Status](https://img.shields.io/badge/status-active-success.svg)

---

## 📸 Aperçu

EShop est une plateforme complète de commerce électronique permettant aux utilisateurs d'acheter et de vendre des articles en ligne. Le système dispose d'une interface utilisateur intuitive et d'un panneau administrateur puissant pour gérer tous les aspects de la plateforme.

---

## ✨ Fonctionnalités

### 👤 **Gestion Utilisateur**
- ✅ Inscription et connexion sécurisées (BCRYPT)
- ✅ Gestion de profil utilisateur
- ✅ Solde de portefeuille pour les transactions
- ✅ Historique des commandes

### 📦 **Gestion d'Articles**
- ✅ Création d'articles avec upload d'images
- ✅ Édition et suppression d'articles
- ✅ Gestion du stock en temps réel
- ✅ Descriptions détaillées avec images
- ✅ Recherche et filtrage d'articles

### 🛒 **Système de Panier & Commande**
- ✅ Ajout/suppression d'articles au panier
- ✅ Modification des quantités
- ✅ Validation de commande avec adresse de livraison
- ✅ Traitement des paiements via solde
- ✅ Génération de factures

### 👑 **Panneau Administrateur**
- ✅ Tableau de bord avec statistiques
- ✅ Gestion complète des articles (CRUD)
- ✅ Gestion complète des utilisateurs (CRUD)
- ✅ Attribution de rôles admin
- ✅ Suppression en cascade

### 🔐 **Sécurité Avancée**
- ✅ Hash des mots de passe (BCRYPT)
- ✅ Vérification du rôle en base de données (pas juste en session)
- ✅ Protection contre les accès non autorisés
- ✅ Validation des entrées utilisateur (htmlspecialchars)
- ✅ Préparation des requêtes SQL (PDO)
- ✅ Transactions de base de données
- ✅ Validation des uploads d'images (type MIME, taille)

### 🎨 **Design Moderne**
- ✅ Interface dark mode élégante
- ✅ Responsive design (Mobile, Tablette, Desktop)
- ✅ Animations fluides
- ✅ Thème violet moderne

---

## 🚀 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- WAMP/LAMP/MAMP (ou équivalent)

### Étapes d'installation

#### 1. Cloner ou télécharger le projet
```bash
cd c:\wamp64\www\
```

#### 2. Configurer la base de données
```bash
mysql -u root -p < config/db.sql
```

Ou importer manuellement `config/db.sql` via phpMyAdmin.

#### 3. Configurer les paramètres de connexion
Éditer `config/database.php` :
```php
$host = 'localhost';
$db = 'ecommerce';
$user = 'root';
$password = '';
```

#### 4. Créer le dossier uploads
```bash
mkdir assets/uploads
chmod 755 assets/uploads
```

#### 5. Démarrer le serveur
```bash
php -S localhost:8000
```
Accédez à `http://localhost:8000`

---

## 📁 Structure du Projet

```
E-Commerce/
├── assets/
│   ├── style.css              # Styles CSS (dark mode)
│   ├── uploads/               # Images uploadées
│   └── default.jpg            # Image par défaut
├── admin/                      # Panneau administrateur
│   ├── index.php              # Dashboard
│   ├── articles.php           # Gestion articles
│   ├── users.php              # Gestion utilisateurs
│   ├── edit_article.php       # Édition article
│   ├── edit_user.php          # Édition utilisateur
│   ├── delete_article.php     # Suppression article
│   ├── delete_user.php        # Suppression utilisateur
│   └── actions/
├── actions/                    # Actions utilisateur
│   ├── login.php              # Traitement login
│   ├── logout.php             # Déconnexion
│   ├── add_money.php          # Ajout de solde
│   ├── add_to_cart.php        # Ajout au panier
│   ├── remove_from_cart.php   # Suppression du panier
│   ├── update_cart.php        # Mise à jour panier
│   └── process_order.php      # Traitement commande
├── config/
│   ├── database.php           # Configuration BD
│   └── db.sql                 # Schéma BD
├── includes/
│   ├── header.php             # Navigation
│   └── auth_helper.php        # Fonctions sécurité
├── index.php                  # Accueil
├── login.php                  # Page login
├── register.php               # Page inscription
├── detail.php                 # Détail article
├── cart.php                   # Panier
├── cart_validate.php          # Validation commande
├── account.php                # Profil utilisateur
├── sell.php                   # Vendre un article
├── edit.php                   # Éditer son article
├── delete_article.php         # Supprimer son article
└── README.md                  # Ce fichier
```

---

## 🔍 Guide d'Utilisation

### Pour les Utilisateurs

#### 1️⃣ **Créer un compte**
- Cliquer sur "S'inscrire"
- Remplir le formulaire avec email, username et mot de passe
- Vous êtes connecté automatiquement

#### 2️⃣ **Acheter des articles**
- Parcourir les articles sur l'accueil
- Utiliser la recherche pour filtrer
- Cliquer sur un article pour voir les détails
- Ajouter au panier et modifier les quantités
- Valider la commande (solde requis)

#### 3️⃣ **Vendre des articles**
- Aller sur "Vendre"
- Remplir le formulaire : titre, description, prix, stock
- Upload une image (drag & drop ou clic)
- Publier l'annonce

#### 4️⃣ **Gérer vos articles**
- Accédez à votre compte
- Cliquez sur "Éditer" pour modifier un article
- Changez le titre, description, prix, stock ou image
- Ou supprimez l'article définitivement

#### 5️⃣ **Recharger votre solde**
- Allez sur votre profil
- Saisissez un montant et cliquez "Ajouter"

### Pour les Administrateurs

#### 1️⃣ **Accéder au panneau admin**
- Connectez-vous avec un compte admin
- Cliquez sur "👑 Admin" dans la navigation

#### 2️⃣ **Voir les statistiques**
- Dashboard avec : Articles, Utilisateurs, Commandes, Chiffre d'affaires

#### 3️⃣ **Gérer les articles**
- Voir tous les articles avec détails
- Éditer titre, description, prix, stock, image
- Supprimer un article

#### 4️⃣ **Gérer les utilisateurs**
- Voir tous les utilisateurs
- Éditer username, email, rôle, solde
- Supprimer un utilisateur (supprime articles et commandes)

---

## 🔐 Système de Sécurité

### Authentification
- Mots de passe hashés avec BCRYPT
- Sessions sécurisées avec `session_regenerate_id()`
- Vérification du rôle en base de données (pas juste en session)

### Validations
- Tous les inputs nettoyés avec `htmlspecialchars()`
- Requêtes SQL préparées avec PDO
- File upload validé (type MIME, taille max 5MB)

### Contrôle d'Accès
- Vérification utilisateur connecté
- Vérification du rôle à chaque action sensible
- Impossible de modifier les articles des autres (sauf admin)

### Transactions
- Les opérations critiques utilisent des transactions BD
- Rollback automatique en cas d'erreur

---

## 💻 Stack Technologique

### Backend
- **PHP 8.0+** - Langage serveur
- **MySQL** - Base de données
- **PDO** - Abstraction BD

### Frontend
- **HTML 5** - Structure
- **CSS 3** - Styles (dark mode)
- **JavaScript** - Interactivité (drag & drop, aperçu image)

### Sécurité
- **BCRYPT** - Hash des mots de passe
- **PDO Prepared Statements** - Protection SQL Injection
- **Input Validation** - htmlspecialchars, filter_input

---

## 📊 Base de Données

### Tables principales

**users**
```sql
id (INT, PK), username, email, password (BCRYPT), 
balance, role, profile_picture, created_at
```

**articles**
```sql
id (INT, PK), name, description, price, author_id (FK), 
image_link, publication_date
```

**stock**
```sql
id (INT, PK), article_id (FK), quantity
```

**cart**
```sql
id (INT, PK), user_id (FK), article_id (FK), quantity
```

**invoices**
```sql
id (INT, PK), user_id (FK), amount, billing_address, 
billing_city, billing_zip, transaction_date
```

---

## 🎨 Personnalisation

### Couleurs

Les couleurs sont définies en variables CSS dans `assets/style.css` :

```css
:root {
    --accent: #a855f7;        /* Violet */
    --success: #10b981;       /* Vert */
    --danger: #ef4444;        /* Rouge */
    --bg-dark: #0f172a;       /* Fond sombre */
}
```

Modifiez ces variables pour changer le thème.

### Logo & Branding

Changez le logo dans `includes/header.php` :
```php
<a href="index.php" class="logo">EShop</a>  <!-- Modifier "EShop" -->
```

---

## ⚡ Performance

- CSS minifié et optimisé
- Images uploadées compressées (max 5MB)
- Requêtes BD préparées et efficaces
- Caching des images

---

## 🐛 Troubleshooting

### "Impossible de télécharger l'image"
- Vérifiez que `assets/uploads/` existe
- Vérifiez les permissions : `chmod 755 assets/uploads`
- Vérifiez le type d'image (JPG, PNG, GIF, WebP)
- Vérifiez la taille (max 5MB)

### "Erreur de connexion BD"
- Vérifiez `config/database.php`
- Vérifiez que MySQL est lancé
- Vérifiez l'existence de la base `ecommerce`

### "Erreur 403 - Accès refusé"
- Vous n'êtes pas connecté
- Vous n'avez pas les permissions
- Votre rôle a peut-être changé (déconnectez-vous et reconnectez-vous)

---

## 📝 API Endpoints

### Utilisateur
- `POST /actions/logout.php` - Déconnexion
- `POST /actions/add_money.php` - Ajouter solde

### Panier
- `POST /actions/add_to_cart.php` - Ajouter article
- `GET /actions/remove_from_cart.php?id=X` - Supprimer article
- `POST /actions/update_cart.php` - Modifier quantité

### Commande
- `POST /actions/process_order.php` - Traiter commande

### Admin
- `POST /admin/actions/edit_article.php` - Éditer article
- `GET /admin/actions/delete_article.php?id=X` - Supprimer article
- `POST /admin/actions/edit_user.php` - Éditer utilisateur
- `GET /admin/actions/delete_user.php?id=X` - Supprimer utilisateur

---

## 🚧 Améliorations Futures

- [ ] Paiement par carte bancaire (Stripe, PayPal)
- [ ] Notifications par email
- [ ] Système de notation/avis
- [ ] Wishlist/favoris
- [ ] Filtres avancés (catégories, prix)
- [ ] Dashboard vendeur
- [ ] API REST
- [ ] Authentification 2FA
- [ ] Dark mode toggle
- [ ] Multi-langue

---

## 📄 Licence

Ce projet est sous licence **MIT**. Vous êtes libre de l'utiliser, le modifier et le redistribuer.

---

## 💬 Support

Pour toute question ou problème, contactez le développeur ou consultez la section Troubleshooting.

---

## 👨‍💻 Auteur

Créé avec ❤️ pour une plateforme e-commerce moderne et sécurisée.

**Version** : 1.0.0  
**Dernière mise à jour** : 24 Février 2026

---

## 🙏 Remerciements

- Design inspired par les meilleures pratiques modernes
- Sécurité basée sur les standards OWASP
- Merci à la communauté PHP

---

<div align="center">

### ⭐ Si ce projet vous plaît, n'oubliez pas de le star !

**[Retour vers le haut](#-eshop---plateforme-e-commerce-moderne)**

</div>
