# 🛍️ LilyShop — Projet E-Commerce Symfony

LilyShop est une application e-commerce développée avec Symfony 7, permettant la gestion complète de produits, catégories, sous-catégories, panier, avis clients, moteur de recherche et espace administrateur.

Projet réalisé dans le cadre de la formation *Full-Stack Developer — Interface3*.

---

## 🚀 Fonctionnalités principales

### 🗂️ Catalogue Produits
- Affichage des produits (pagination + grille responsive)
- Image, prix, description
- Page détaillée d’un produit

### 📁 Catégories & Sous-catégories
- Navigation dynamique dans la navbar  
- Filtrage des produits par sous-catégorie

### 🔍 Recherche interne
- Barre de recherche dans la navbar  
- Recherche par nom + description  
- Correspondance partielle (`LIKE %mot%`)

### ⭐ Système d’avis clients
- Affichage des avis avec :
  - Nom de l’utilisateur
  - Date
  - Commentaire
  - Étoiles ⭐⭐⭐⭐⭐
- Design moderne et lisible  
*(Évolution prévue : réserver l’ajout d’un avis aux utilisateurs authentifiés ayant acheté le produit)*

### 🧺 Panier
- Ajouter un produit au panier  
- Mise à jour du nombre d’articles  
- Gestion du stock

### 🔐 Authentification & Administration
Espace Admin :
- CRUD Catégories  
- CRUD Sous-catégories  
- CRUD Produits  
- Gestion des utilisateurs  

### 🎨 UI / UX Modernisée
- Navbar améliorée  
- Design responsive  
- Cartes produits stylisées  
- Système d’avis visuel  
- Slider promotionnel (Black Friday)

---

## 🛠️ Technologies utilisées

- Symfony 7
- PHP 8.2
- Twig
- Doctrine ORM
- MySQL / MariaDB
- Bootstrap 5
- Webpack Encore
- FontAwesome
- KNP Paginator

- PayPal (intégration des paiements via PayPal Sandbox)
- Google Identity / OAuth2 (connexion / inscription avec compte Google)


---

## ⚙️ Installation & lancement du projet

### 1️⃣ Cloner le projet

git clone https://github.com/sahardel92/MyProjectE-commerce.git
cd MyProjectE-commerce

---

### 2️⃣ Installer les dépendances PHP
composer install

---

### 3️⃣ Installer les dépendances front-end
npm install
npm run dev

---

### 4️⃣ Lancer le serveur Symfony
symfony serve:start

---

✨ Auteur

Sahar Dellouz
Développeuse Full-Stack en formation — Interface3

