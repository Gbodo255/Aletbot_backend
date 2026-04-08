# Configuration Files Guide

## .env (Fichier Principal de Configuration)

Le fichier `.env` contient toutes les variables de configuration:

```env
# Informations de l'Application
APP_NAME=ProjectApp
APP_ENV=local              # local, testing, production
APP_DEBUG=true             # true en dev, false en prod
APP_URL=http://localhost

# Clé d'Application (généré avec: php artisan key:generate)
APP_KEY=base64:kg4k4MK6/7WEv5+w9ELTagHyumgnYFmu+TmGr8YE9ig=

# Localisation
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug            # debug, info, notice, warning, error, critical, alert, emergency

# ========================= DATABASE =========================

# Options: sqlite, mysql, pgsql
DB_CONNECTION=sqlite

# SQLite
DB_DATABASE=database/database.sqlite

# Alternative MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=projectapp
# DB_USERNAME=root
# DB_PASSWORD=root

# ========================= SESSION =========================

SESSION_DRIVER=database
SESSION_LIFETIME=120       # minutes
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# ========================= CACHE =========================

CACHE_STORE=database
# CACHE_PREFIX=

# ========================= QUEUE =========================

QUEUE_CONNECTION=database

# ========================= BROADCAST =========================

BROADCAST_CONNECTION=log

# ========================= FILESYSTEM =========================

FILESYSTEM_DISK=local      # local, public, s3, etc

# ========================= REDIS =========================

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ========================= MAIL =========================

MAIL_MAILER=log            # log, smtp, sendmail, postmark, mailgun, etc
MAIL_FROM_NAME="${APP_NAME}"
MAIL_FROM_ADDRESS=hello@example.com

# SMTP Configuration
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=465
# MAIL_USERNAME=xxxx
# MAIL_PASSWORD=xxxx
# MAIL_ENCRYPTION=tls

# ========================= SANCTUM (API TOKENS) =========================

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8001,127.0.0.1:8001
SANCTUM_ENCRYPTION_KEYS=${APP_KEY}

# ========================= AUTRES =========================

TELESCOPE_ENABLED=false    # Laravel Telescope
MAINTENANCE_DRIVER=file
```

## .env.testing (Configuration pour les Tests)

Utilisée lors de l'exécution des tests:

```env
APP_NAME=ProjectApp
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:kg4k4MK6/7WEv5+w9ELTagHyumgnYFmu+TmGr8YE9ig=

DB_CONNECTION=sqlite
DB_DATABASE=:memory:       # Base de données en mémoire pour les tests

QUEUE_CONNECTION=sync
SESSION_DRIVER=array

MAIL_MAILER=array

BROADCAST_DRIVER=log
CACHE_DRIVER=array
```

## Configuration par Environnement

### Développement Local (APP_ENV=local)

```env
APP_DEBUG=true
LOG_LEVEL=debug
DB_CONNECTION=sqlite
MAIL_MAILER=log            # Les emails s'affichent dans les logs
QUEUE_CONNECTION=sync      # Jobs exécutés immédiatement
```

**Avantages:**
- Débogage facile
- Pas de dépendances externes
- Tests rapides

### Production (APP_ENV=production)

```env
APP_DEBUG=false            # IMPORTANT: JAMAIS true en production!
LOG_LEVEL=warning          # Moins d'informations dans les logs
DB_CONNECTION=mysql        # Utiliser un vrai serveur MySQL
DB_HOST={votre_serveur}
MAIL_MAILER=smtp           # Utiliser un service email réel
CACHE_STORE=redis          # Pour les performances
QUEUE_CONNECTION=redis     # Pour les jobs asynchrones
```

**Sécurité:**
- Génération d'une nouvelle `APP_KEY`
- HTTPS obligatoire
- Mot de passe BD complexe
- Fichiers de log hors du web root

## Variables Importantes

### Database (DB_*)

| Variable | Description | Exemple |
|----------|-------------|---------|
| DB_CONNECTION | Type de BD | sqlite, mysql |
| DB_HOST | Serveur | 127.0.0.1, db.exemple.com |
| DB_PORT | Port | 3306 (MySQL), 5432 (PostgreSQL) |
| DB_DATABASE | Nom BD | projectapp |
| DB_USERNAME | Utilisateur | root |
| DB_PASSWORD | Mot de passe | - |

### Mail (MAIL_*)

| Variable | Description | Exemple |
|----------|-------------|---------|
| MAIL_MAILER | Type | smtp, sendmail, postmark |
| MAIL_HOST | Serveur | smtp.gmail.com |
| MAIL_PORT | Port | 465, 587 |
| MAIL_USERNAME | Email/Compte | user@example.com |
| MAIL_PASSWORD | Mot de passe | - |
| MAIL_ENCRYPTION | Chiffrement | tls, ssl |
| MAIL_FROM_ADDRESS | From | noreply@example.com |
| MAIL_FROM_NAME | Nom From | Mon App |

### Sanctum (SANCTUM_*)

| Variable | Description | Valeur |
|----------|-------------|--------|
| SANCTUM_STATEFUL_DOMAINS | Domaines autorisés | localhost:8001 |
| SANCTUM_ENCRYPTION_KEYS | Clé de chiffrement | Base64 encoded |

## Gestion des Secrets

### Variables Sensibles

Ne JAMAIS commiter dans Git:
- `APP_KEY`
- `DB_PASSWORD`
- `MAIL_PASSWORD`
- Tokens/Clés API

### Fichiers à Ignorer

Assurez-vous que `.gitignore` contient:

```
.env
.env.local
.env.*.local
.env.backup
```

### En Production

1. **Set les variables via:**
   - Variables d'environnement du système
   - Configuration du serveur
   - Env vars setup au déploiement

2. **Ne jamais:**
   - Commiter `.env`
   - Exécuter `php artisan key:generate` en prod
   - Changer `APP_DEBUG` en production

## Commandes Utiles

```bash
# Générer une nouvelle clé d'application
php artisan key:generate

# Afficher la configuration
php artisan config:show

# Cache la configuration (production)
php artisan config:cache

# Vider le cache de configuration
php artisan config:clear

# Véifier les variables d'env
php artisan env
```

## Dépannage

### Erreur: "No encryption key has been specified"

```bash
php artisan key:generate
```

### Configuration inadaptée

```bash
# Vérifier la config chargée
php artisan config:show database

# Réinitialiser le cache
php artisan config:clear
php artisan config:cache
```

### Tests échouent

Assurez que vous avez créé `.env.testing`:
```bash
cp .env .env.testing
# Modifier pour test (BD :memory:, etc)
```

## Fichier de Référence Complet

Voir le fichier `.env.example` pour tous les paramètres disponibles.

Les valeurs par défaut sont souvent suffisantes pour le développement.
