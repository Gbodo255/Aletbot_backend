# API Gestion des Utilisateurs - Backend Laravel

Une API REST complète pour la gestion des utilisateurs avec **authentification**, **rôles et permissions**, **profil utilisateur**, et **historique d'activité**.

## Fonctionnalités Implémentées

### 1️⃣ Gestion des Utilisateurs
- ✅ Inscription avec email et mot de passe
- ✅ Connexion/Déconnexion
- ✅ Authentification par tokens (Laravel Sanctum)
- ✅ Profil utilisateur complète

### 2️⃣ Rôles et Permissions
- ✅ Deux rôles prédéfinis: `admin` et `user`
- ✅ Permissions granulaires
- ✅ Attribution/révocation de rôles aux utilisateurs
- ✅ Gestion des permissions par rôle
- ✅ Vérification des permissions sur les endpoints

### 3️⃣ Profil Utilisateur
- ✅ Informations personnelles (nom, email, téléphone, bio, avatar)
- ✅ Modification du profil
- ✅ Changement de mot de passe
- ✅ Préférences de notification
  - Email notifications
  - Push notifications
  - SMS notifications
  - Activity alerts
  - Security alerts

### 4️⃣ Historique d'Activité
- ✅ Enregistrement automatique de toutes les actions
- ✅ Historique d'activité par utilisateur
- ✅ Logs des alertes de sécurité
- ✅ Suivi des modifications (avant/après)
- ✅ IP et User-Agent stockés
- ✅ Filtrage par date, action, utilisateur

## Structure du Projet

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── AuthController.php
│   │           ├── UserController.php
│   │           ├── ProfileController.php
│   │           ├── ActivityLogController.php
│   │           └── RoleController.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── Permission.php
│       ├── ActivityLog.php
│       └── NotificationPreference.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_04_07_165656_create_roles_table.php
│   │   ├── 2026_04_07_165657_create_permissions_table.php
│   │   ├── 2026_04_07_165657_create_activity_logs_table.php
│   │   ├── 2026_04_07_165705_create_role_user_table.php
│   │   ├── 2026_04_07_165706_create_permission_role_table.php
│   │   └── 2026_04_07_165706_create_notification_preferences_table.php
│   └── seeders/
│       ├── RoleAndPermissionSeeder.php
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── config/
│   └── sanctum.php
└── tests/
    └── Feature/
        └── AuthApiTest.php
```

## Installation et Configuration

### Prérequis
- PHP 8.3+
- Composer
- WAMP64 ou serveur Apache/MySQL

### Étapes d'Installation

1. **Cloner le projet** (déjà fait)
```bash
cd c:\Users\Temp\Desktop\Projectapp\backend
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Générer la clé d'application**
```bash
php artisan key:generate
```

4. **Publier la configuration Sanctum**
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

5. **Configurer PostgreSQL** (⭐ Important!)

Deux options:

**Option A: Configuration automatique (Windows)**
- Double-cliquez sur `SETUP_POSTGRESQL.bat`
- Le script créera la base et v configurera tout

**Option B: Configuration manuelle**
- Voir le guide complet: [POSTGRESQL_SETUP.md](./POSTGRESQL_SETUP.md)

6. **Exécuter les migrations**
```bash
php artisan migrate:fresh --seed
```

> **Note**: Si vous rencontrez des erreurs de connexion à la base de données, assurez-vous que:
> - PostgreSQL est en cours d'exécution
> - Les identifiants dans le fichier `.env` sont corrects
> - La base de données `laravel_api` existe

7. **Démarrer le serveur de développement**
```bash
php artisan serve --port=8001
```

Le serveur sera disponible à: `http://127.0.0.1:8001`

## Configuration de la Base de Données

Ce projet utilise **PostgreSQL** par défaut.

### Configuration par défaut
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_api
DB_USERNAME=postgres
DB_PASSWORD=password
DB_CHARSET=utf8
```

### Changer les identifiants

Modifier le fichier `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=votre_host
DB_PORT=5432
DB_DATABASE=votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

### Créer la base de données

```bash
psql -U postgres -c "CREATE DATABASE laravel_api;"
```

### Guide complet PostgreSQL

Pour une configuration détaillée, haute disponibilité, ou si vous rencontrez des problèmes:
👉 [Voir POSTGRESQL_SETUP.md](./POSTGRESQL_SETUP.md)

### Changer de base de données

Si vous voulez utiliser **MySQL** ou **SQLite**:

1. Modifier `.env`:
   ```env
   # Pour MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_api
   DB_USERNAME=root
   DB_PASSWORD=
   
   # Pour SQLite
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```

2. Exécuter les migrations

## Rôles et Permissions Prédéfinis

### Rôles
| Rôle | Description | Permissions |
|------|-------------|-------------|
| admin | Administrateur | Accès complet |
| user | Utilisateur standard | Voir ses logs d'activité |

### Permissions
- `users.view` - Voir les utilisateurs
- `users.create` - Créer un utilisateur
- `users.edit` - Modifier un utilisateur
- `users.delete` - Supprimer un utilisateur
- `roles.view` - Voir les rôles
- `roles.create` - Créer un rôle
- `roles.edit` - Modifier un rôle
- `roles.delete` - Supprimer un rôle
- `permissions.view` - Voir les permissions
- `permissions.create` - Créer une permission
- `permissions.edit` - Modifier une permission
- `permissions.delete` - Supprimer une permission
- `activity-logs.view` - Voir les logs d'activité
- `activity-logs.export` - Exporter les logs

## Tester l'API

### Via Postman
1. Ouvrir Postman
2. Importer la collection: `postman_collection.json`
3. Définir les variables d'environnement:
   - `base_url`: `http://127.0.0.1:8001/api/v1`
   - `token`: (sera défini automatiquement lors du login)

### Via cURL

**Inscription:**
```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Connexion:**
```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Récupérer le profil (avec token):**
```bash
curl -X GET http://127.0.0.1:8001/api/v1/auth/me \
  -H "Authorization: Bearer {token}"
```

## Documentation Complète

Voir [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) pour la documentation détaillée de tous les endpoints.

## Authentification

L'API utilise **Laravel Sanctum** pour l'authentification par tokens:

1. L'utilisateur reçoit un token lors de l'inscription ou la connexion
2. Le token doit être inclus dans l'en-tête `Authorization: Bearer {token}` pour tous les endpoints protégés
3. Les tokens ne sont jamais stockés - ils sont générés dynamiquement

## Sécurité

- ✅ Passwords hashés avec bcrypt
- ✅ Protection CSRF (si utilisé avec sessions)
- ✅ Validation des données requête
- ✅ Autorisation basée sur les rôles
- ✅ Logging de toutes les actions sensibles
- ✅ IP et User-Agent enregistrés pour les logs
- ✅ Soft deletes pour les utilisateurs

## Logging et Audit

Tous les événements importants sont automatiquement enregistrés:
- Connexion/Déconnexion
- Modification de profil
- Changement de mot de passe
- Changments de préférences
- Gestion des rôles
- Gestion des permissions
- Suppressions

Chaque log contient:
- L'ID de l'utilisateur
- L'action effectuée
- Les valeurs avant/après pour les modifications
- L'adresse IP
- Le User-Agent
- Timestamp exact

## Dépannage

### Erreur: "could not find driver"
Le pilote PDO SQLite n'est pas activé. Solution:
- Utilisez MySQL à la place, ou
- Activez l'extension SQLite dans `php.ini`:
  ```ini
  extension=pdo_sqlite
  ```

### Erreur: "Connection refused"
MySQL n'est pas accessible. Solutions:
- Démarrez WAMP64/MySQL
- Vérifiez les identifiants dans `.env`
- Utilisez SQLite avec le chemin correct

### Erreur: "Base de données n'existe pas"
```bash
# Créez la base de données
mysql -u root -p -e "CREATE DATABASE projectapp;"

# Puis exécutez les migrations
php artisan migrate:fresh --seed
```

## Fichiers Importants

- `.env` - Configuration de l'application
- `routes/api.php` - Routes API
- `app/Http/Controllers/Api/` - Contrôleurs API
- `app/Models/` - Modèles Eloquent
- `database/migrations/` - Schémas de base de données
- `database/seeders/` - Données de test

## Prochaines Étapes

### Fonctionnalités à Ajouter
- [ ] Authentification OAuth2 (Google, GitHub, etc.)
- [ ] 2FA (Two-Factor Authentication)
- [ ] API de gestion des sessions actives
- [ ] Export des logs d'activité (CSV, PDF)
- [ ] Webhook pour les événements
- [ ] Rate limiting
- [ ] API versioning
- [ ] Tests unitaires complets
- [ ] Documentation Swagger/OpenAPI

## Support

Pour plus d'informations ou en cas de problème, consultez la documentation:
- Laravel: https://laravel.com/docs
- Sanctum: https://laravel.com/docs/sanctum
- Eloquent: https://laravel.com/docs/eloquent


## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
