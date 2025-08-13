# 🚀 Guide d'implémentation Swagger/OpenAPI dans Laravel

## 📋 Table des matières
- [Installation](#installation)
- [Configuration](#configuration)
- [Génération de la documentation](#génération-de-la-documentation)
- [Utilisation avancée](#utilisation-avancée)
- [Bonnes pratiques](#bonnes-pratiques)
- [Dépannage](#dépannage)

---

## 🔧 Installation

### 1. Installer L5-Swagger (si pas encore fait)
```bash
composer require "darkaonline/l5-swagger"
```

### 2. Publier la configuration
```bash
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

### 3. Configurer Laravel Sanctum pour l'authentification API
```bash
php artisan install:api
```

### 4. Configurer votre fichier .env
Ajoutez ces variables à votre fichier `.env` :
```env
# Configuration Swagger/OpenAPI
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_FORMAT_TO_USE_FOR_DOCS=json
L5_SWAGGER_CONST_HOST=http://localhost:8000
L5_SWAGGER_UI_DOC_EXPANSION=none
L5_SWAGGER_UI_FILTERS=true
L5_SWAGGER_UI_PERSIST_AUTHORIZATION=false
L5_SWAGGER_OPEN_API_SPEC_VERSION=3.1.0
```

---

## ⚙️ Configuration

### Fichiers créés/modifiés :

1. **`config/l5-swagger.php`** - Configuration principale de Swagger
2. **`app/Http/Controllers/Controller.php`** - Documentation de base de l'API
3. **`app/Http/Controllers/API/AuthController.php`** - Endpoints d'authentification
4. **`app/Http/Controllers/API/UserController.php`** - CRUD utilisateurs
5. **`app/Models/User.php`** - Schéma utilisateur avec Sanctum
6. **`routes/api.php`** - Routes API

---

## 📖 Génération de la documentation

### Méthode 1 : Via L5-Swagger (recommandée)
```bash
php artisan l5-swagger:generate
```

### Méthode 2 : Script manuel (si L5-Swagger non installé)
```bash
php generate-swagger.php
```

### Accéder à la documentation
Une fois générée, visitez : `http://localhost:8000/api/documentation`

---

## 🔑 Utilisation avancée

### Authentification dans Swagger UI

1. **Démarrer le serveur Laravel :**
```bash
php artisan serve
```

2. **Créer un utilisateur de test :**
```bash
php artisan tinker
User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password123')]);
```

3. **Dans Swagger UI :**
   - Allez à `/api/auth/login`
   - Utilisez les identifiants : `test@example.com` / `password123`
   - Copiez le token reçu
   - Cliquez sur "Authorize" en haut à droite
   - Entrez : `Bearer [votre-token]`

### Structure des annotations principales

#### `@OA\Info` - Informations générales de l'API
```php
/**
 * @OA\Info(
 *     title="Mon API",
 *     version="1.0.0",
 *     description="Description de mon API"
 * )
 */
```

#### `@OA\SecurityScheme` - Schémas d'authentification
```php
/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
```

#### `@OA\Schema` - Modèles de données
```php
/**
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email")
 * )
 */
```

#### `@OA\Post` - Endpoint POST
```php
/**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Créer un utilisateur",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserRequest")
 *     ),
 *     @OA\Response(response=201, description="Utilisateur créé")
 * )
 */
```

---

## 📏 Bonnes pratiques

### 1. Organisation des tags
- Groupez vos endpoints par fonctionnalité
- Utilisez des noms clairs et cohérents
- Exemple : `Authentication`, `Users`, `Products`

### 2. Documentation des réponses
- Documentez TOUS les codes de statut possibles
- Incluez des exemples de données
- Spécifiez les schémas de données

### 3. Sécurité
- Documentez les endpoints protégés avec `security={{"sanctum":{}}}`
- Expliquez clairement les permissions requises
- Fournissez des exemples d'authentification

### 4. Validation
- Documentez tous les champs requis
- Spécifiez les types et formats
- Incluez les règles de validation

### 5. Exemples
```php
/**
 * @OA\Parameter(
 *     name="page",
 *     in="query",
 *     description="Numéro de page",
 *     required=false,
 *     @OA\Schema(type="integer", example=1)
 * )
 */
```

---

## 🛠️ Dépannage

### Problème : La documentation ne se génère pas
**Solution :**
```bash
php artisan config:clear
php artisan cache:clear
php artisan l5-swagger:generate
```

### Problème : Erreur 404 sur `/api/documentation`
**Vérifiez :**
1. La route dans `config/l5-swagger.php`
2. Que L5-Swagger est bien installé
3. Les permissions du dossier `storage/`

### Problème : Les annotations ne sont pas détectées
**Vérifiez :**
1. Le namespace `use OpenApi\Annotations as OA;`
2. La syntaxe des annotations
3. Le chemin de scan dans `config/l5-swagger.php`

### Problème : L'authentification ne fonctionne pas
**Vérifiez :**
1. Laravel Sanctum est installé : `php artisan install:api`
2. Le middleware `auth:sanctum` sur les routes
3. Le trait `HasApiTokens` sur le modèle User

---

## 📁 Structure des fichiers

```
app/
├── Http/
│   └── Controllers/
│       ├── Controller.php (documentation de base)
│       └── API/
│           ├── AuthController.php (authentification)
│           └── UserController.php (CRUD utilisateurs)
├── Models/
│   └── User.php (schéma utilisateur)
config/
└── l5-swagger.php (configuration Swagger)
routes/
└── api.php (routes API)
storage/
└── api-docs/
    ├── api-docs.json
    └── api-docs.yaml
```

---

## 🎯 Endpoints disponibles

### Authentification
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion (protégé)

### Utilisateurs
- `GET /api/users` - Liste des utilisateurs (protégé)
- `POST /api/users` - Créer un utilisateur (protégé)
- `GET /api/users/{id}` - Détails d'un utilisateur (protégé)
- `PUT /api/users/{id}` - Modifier un utilisateur (protégé)
- `DELETE /api/users/{id}` - Supprimer un utilisateur (protégé)

### Profil
- `GET /api/profile` - Profil utilisateur actuel (protégé)
- `PUT /api/profile` - Modifier le profil (protégé)

---

## 🚀 Prochaines étapes

1. **Étendre l'API** avec d'autres modèles (Produits, Commandes, etc.)
2. **Ajouter la pagination** avec des métadonnées Swagger
3. **Implémenter les filtres** et la recherche
4. **Ajouter la validation avancée** avec Form Requests
5. **Configurer les environnements** (dev, staging, prod)
6. **Ajouter les tests automatisés** pour vos endpoints

---

## 📚 Ressources supplémentaires

- [Documentation OpenAPI 3.0](https://swagger.io/specification/)
- [L5-Swagger GitHub](https://github.com/DarkaOnLine/L5-Swagger)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Swagger PHP Annotations](https://zircote.github.io/swagger-php/)

---

**🎉 Félicitations ! Votre API Laravel est maintenant documentée avec Swagger !**
