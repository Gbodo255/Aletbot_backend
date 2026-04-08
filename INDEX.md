# 📖 INDEX DE DOCUMENTATION

## 🚀 Par où commencer?

### Débutant
1. **[QUICK_START.md](./QUICK_START.md)** ← COMMENCEZ ICI
   - Guide de démarrage en 5 minutes
   - Exemples cURL simples
   - Tests Postman

2. **[README.md](./README.md)**
   - Vue d'ensemble du projet
   - Installation complète
   - Configuration de base

### Développeur
3. **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)**
   - Tous les endpoints détaillés
   - Formats de requête/réponse
   - Codes d'erreur
   - Filtres et paramètres

4. **[IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md)**
   - Architecture complète
   - Fonctionnalités implémentées
   - Structure du code
   - Données de test

### Configuration
5. **[CONFIGURATION.md](./CONFIGURATION.md)**
   - Variables d'environnement
   - Fichiers .env
   - Configuration par environnement
   - Gestion des secrets

6. **[POSTGRESQL_SETUP.md](./POSTGRESQL_SETUP.md)** ⭐ **IMPORTANT**
   - Guide complet PostgreSQL
   - Installation sur Windows/Mac/Linux
   - Dépannage et troubleshooting
   - Outils recommandés

---

## 📋 Fichiers Importants

### Documentation (Markdown)
- 📄 [QUICK_START.md](./QUICK_START.md) - Démarrage rapide
- 📄 [README.md](./README.md) - Présentation générale  
- 📄 [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) - Endpoints
- 📄 [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md) - Résumé technique
- 📄 [CONFIGURATION.md](./CONFIGURATION.md) - Configuration
- 📄 [POSTGRESQL_SETUP.md](./POSTGRESQL_SETUP.md) - **Configuration PostgreSQL** ⭐
- 📄 [PROJECT_SUMMARY.txt](./PROJECT_SUMMARY.txt) - Vue d'ensemble
- 📄 [ALERTS_IMPLEMENTATION.md](./ALERTS_IMPLEMENTATION.md) - Gestion des alertes

### Collections & Tests
- 📦 [postman_collection.json](./postman_collection.json) - Collection Postman
- 🧪 [tests/Feature/AuthApiTest.php](./tests/Feature/AuthApiTest.php) - Tests API

### Scripts
- 🚀 [START_API.bat](./START_API.bat) - Lancement facile (Windows)
- � [SETUP_POSTGRESQL.bat](./SETUP_POSTGRESQL.bat) - Configuration PostgreSQL automatique (Windows) ⭐
- 📝 [test-api.php](./test-api.php) - Script de test PHP
- 📝 [test-alerts.php](./test-alerts.php) - Tests des alertes

### Code Source
- 🎮 [app/Http/Controllers/Api/](./app/Http/Controllers/Api/) - Contrôleurs API
- 🏠 [app/Models/](./app/Models/) - Modèles Eloquent
- 🛣️ [routes/api.php](./routes/api.php) - Routes API
- 📊 [database/migrations/](./database/migrations/) - Migrations BD
- 🌱 [database/seeders/](./database/seeders/) - Seeders

### Configuration
- ⚙️ [.env](./.env) - Configuration environnement
- ⚙️ [.env.example](./.env.example) - Exemple de configuration
- ⚙️ [config/](./config/) - Configuration Laravel

---

## 🎯 Cas d'Usage Courants

### Je veux tester l'API rapidement
→ [Lire QUICK_START.md](./QUICK_START.md)

### Je veux comprendre architecture
→ [Lire IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md)

### Je veux la documentation complète des endpoints
→ [Lire API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Je veux changer la configuration
→ [Lire CONFIGURATION.md](./CONFIGURATION.md)

### Je veux installer et configurer
→ [Lire README.md](./README.md)

### Je veux tester avec Postman
→ [Importer postman_collection.json](./postman_collection.json)

### Je veux écrire du code client
→ [Utiliser les endpoints documentés](./API_DOCUMENTATION.md)

### Je veux gérer les alertes
→ [Voir la section 6 dans API_DOCUMENTATION.md](./API_DOCUMENTATION.md#6-gestion-des-alertes)

### Je veux configurer PostgreSQL
→ [Lire POSTGRESQL_SETUP.md](./POSTGRESQL_SETUP.md)
   **OU** Double-cliquez sur [SETUP_POSTGRESQL.bat](./SETUP_POSTGRESQL.bat)

---

## 🔧 Commandes Utiles

### Démarrer l'API
```bash
# Option 1: Double-cliquer sur START_API.bat (Windows)

# Option 2: PowerShell
php artisan serve --port=8001

# Option 3: Composer
composer run serve
```

### Migrations
```bash
# Créer les tables
php artisan migrate

# Réinitialiser + seeder
php artisan migrate:fresh --seed

# Annuler les migrations
php artisan migrate:rollback
```

### Tests
```bash
# Lancer les tests
php artisan test

# Tests spécifiques
php artisan test tests/Feature/AuthApiTest.php
```

### Utilitaires
```bash
# Afficher la configuration
php artisan config:show

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Générer une nouvelle clé
php artisan key:generate

# Afficher les routes
php artisan route:list --path=api
```

---

## 📊 Endpoints Résumé

### Authentification (Public)
```
POST   /api/v1/auth/register        Inscription
POST   /api/v1/auth/login           Connexion
```

### Authentification (Protégé)
```
POST   /api/v1/auth/logout          Déconnexion
GET    /api/v1/auth/me              Profil actuel
```

### Profil (Protégé)
```
GET    /api/v1/profile              Récupérer profil
PUT    /api/v1/profile              Modifier profil
PUT    /api/v1/profile/change-password            Changer mot de passe
PUT    /api/v1/profile/notification-preferences  Préférences
```

### Historique (Protégé)
```
GET    /api/v1/activity-logs        Tous les logs
GET    /api/v1/activity-logs/user/{id}      Logs utilisateur
GET    /api/v1/activity-logs/{id}   Détail log
```

### Gestion (Admin)
```
GET    /api/v1/users                Lister utilisateurs
GET    /api/v1/users/{id}           Détail utilisateur
POST   /api/v1/users/{id}/assign-role       Assigner rôle
DELETE /api/v1/users/{id}           Supprimer utilisateur
GET    /api/v1/roles                Lister rôles
POST   /api/v1/roles                Créer rôle
DELETE /api/v1/roles/{id}           Supprimer rôle
```

---

## 🎓 Documentation Externe

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Laravel Eloquent ORM](https://laravel.com/docs/eloquent)
- [Laravel Migrations](https://laravel.com/docs/migrations)
- [Laravel Validation](https://laravel.com/docs/validation)

---

## 🆘 Dépannage

### Erreur: "Could not find driver"
```bash
# Activez l'extension SQLite dans php.ini
# Ou changez pour MySQL dans .env
```

### Erreur: "Connection refused"
```bash
# Vérifiez que le serveur MySQL/SQLite est accessible
# Vérifiez les credentials dans .env
```

### Erreur: "Unauthorized"
```bash
# Vérifiez que le token est présent dans Authorization header
# Format: Authorization: Bearer {token}
```

---

## 📞 Support

Consultez les fichiers .md correspondant au problème rencontré.

**Fichiers par sujet:**
- Installation → [README.md](./README.md)
- Configuration → [CONFIGURATION.md](./CONFIGURATION.md)
- Endpoints → [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- Démarrage → [QUICK_START.md](./QUICK_START.md)
- Tech → [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md)

---

## ✅ Checklist de Démarrage

- [ ] Lire [QUICK_START.md](./QUICK_START.md)
- [ ] Lancer le serveur avec `START_API.bat` ou `php artisan serve`
- [ ] Importer [postman_collection.json](./postman_collection.json) dans Postman
- [ ] Tester un endpoint simple (ex: registration)
- [ ] Consulter [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) pour plus
- [ ] Lire [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md) pour comprendre

---

**Dernière mise à jour**: 7 Avril 2026
**Version**: 1.0.0
**Status**: ✅ Production Ready
