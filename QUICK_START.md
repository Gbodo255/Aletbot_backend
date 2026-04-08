# 🎯 GUIDE DE DÉMARRAGE RAPIDE

## Étape 1: Démarrer le Serveur

Ouvrez PowerShell dans le dossier backend:

```powershell
cd c:\Users\Temp\Desktop\Projectapp\backend
php artisan serve --port=8001
```

**Vous devriez voir:**
```
   INFO  Server running on [http://127.0.0.1:8001].

  Press Ctrl+C to stop the server
```

## Étape 2: Tester l'API

### Option A: Avec Postman (Recommandé)

1. Télécharger [Postman](https://www.postman.com/downloads/)
2. Ouvrir Postman
3. Importer la collection:
   - Cliquer sur "Import"
   - Sélectionner `postman_collection.json` du dossier backend
4. Les endpoints et variables d'environnement seront pré-configurés

### Option B: Avec cURL

#### 1. Créer un utilisateur (Inscription)

```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!"
  }'
```

**Réponse attendue (201 Created):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "roles": [
      {
        "id": 2,
        "name": "user"
      }
    ]
  },
  "token": "token_abc123xyz..."
}
```

**Sauvegardez le token** pour les prochaines requêtes!

#### 2. Se Connecter (Login)

```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jean@example.com",
    "password": "SecurePass123!"
  }'
```

#### 3. Récupérer le Profil Actuel

```bash
curl -X GET http://127.0.0.1:8001/api/v1/auth/me \
  -H "Authorization: Bearer {votre_token}"
```

Remplacez `{votre_token}` par le token reçu lors de l'inscription.

#### 4. Modifier le Profil

```bash
curl -X PUT http://127.0.0.1:8001/api/v1/profile \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {votre_token}" \
  -d '{
    "phone": "+33612345678",
    "bio": "Développeur passionné par Laravel"
  }'
```

#### 5. Changer les Préférences de Notification

```bash
curl -X PUT http://127.0.0.1:8001/api/v1/profile/notification-preferences \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {votre_token}" \
  -d '{
    "email_notifications": true,
    "push_notifications": false,
    "activity_alerts": true,
    "security_alerts": true
  }'
```

#### 6. Voir l'Historique d'Activité

```bash
curl -X GET http://127.0.0.1:8001/api/v1/activity-logs \
  -H "Authorization: Bearer {votre_token}"
```

#### 7. Se Déconnecter

```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/logout \
  -H "Authorization: Bearer {votre_token}"
```

---

## 📋 Endpoints Disponibles

### Authentification (Public)
| Méthode | URL | Corp |
|---------|-----|------|
| POST | `/api/v1/auth/register` | email, password |
| POST | `/api/v1/auth/login` | email, password |

### Authentification (Protégé)
| Méthode | URL | Utilité |
|---------|-----|---------|
| POST | `/api/v1/auth/logout` | Déconnexion |
| GET | `/api/v1/auth/me` | Profil actuel |

### Profil (Protégé)
| Méthode | URL | Corps |
|---------|-----|-------|
| GET | `/api/v1/profile` | - |
| PUT | `/api/v1/profile` | name, phone, bio |
| PUT | `/api/v1/profile/notification-preferences` | email_notifications, push_notifications, etc. |
| PUT | `/api/v1/profile/change-password` | current_password, password |

### Historique Activité (Protégé)
| Méthode | URL | Paramètres |
|---------|-----|-----------|
| GET | `/api/v1/activity-logs` | ?user_id=X&action=login |
| GET | `/api/v1/activity-logs/user/{id}` | ?action=update_profile |
| GET | `/api/v1/activity-logs/{id}` | - |

### Gestion Utilisateurs (Admin)
| Méthode | URL | Corps |
|---------|-----|-------|
| GET | `/api/v1/users` | - |
| GET | `/api/v1/users/{id}` | - |
| POST | `/api/v1/users/{id}/assign-role` | role_id |
| DELETE | `/api/v1/users/{id}` | - |

### Gestion Rôles (Admin)
| Méthode | URL | Corps |
|---------|-----|-------|
| GET | `/api/v1/roles` | - |
| POST | `/api/v1/roles` | name, description |
| POST | `/api/v1/roles/{id}/assign-permission` | permission_id |
| DELETE | `/api/v1/roles/{id}` | - |

---

## 🔧 Configuration

### Fichier `.env`
```env
APP_NAME=ProjectApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

QUEUE_CONNECTION=sync
```

### Utilisateur Admin Prédéfini
- Email: `admin@example.com`
- Rôle: `admin`
- Accès complet à l'API

---

## ❌ Codes d'Erreur Courants

| Code | Erreur | Cause |
|------|--------|-------|
| 200 | OK | Succès |
| 201 | Created | Ressource créée avec succès |
| 400 | Bad Request | Données invalides |
| 401 | Unauthorized | Token manquant ou invalide |
| 403 | Forbidden | Permissions insuffisantes |
| 404 | Not Found | Ressource non trouvée |
| 422 | Unprocessable Entity | Erreur de validation |
| 500 | Server Error | Erreur serveur |

---

## 🧪 Scénario de Test Complet

### 1. Création d'un compte

```bash
POST /api/v1/auth/register
{
  "name": "Alice Martin",
  "email": "alice@test.com",
  "password": "Test@12345",
  "password_confirmation": "Test@12345"
}
```

**Réponse**: Token + User ID

### 2. Connexion

```bash
POST /api/v1/auth/login
{
  "email": "alice@test.com",
  "password": "Test@12345"
}
```

**Réponse**: Token (utiliser pour les requêtes suivantes)

### 3. Récupérer le profil

```bash
GET /api/v1/auth/me
Authorization: Bearer {TOKEN}
```

### 4. Modifier le profil

```bash
PUT /api/v1/profile
Authorization: Bearer {TOKEN}
{
  "phone": "+336 12 34 56 78",
  "bio": "Développeuse fullstack"
}
```

### 5. Consulter l'historique

```bash
GET /api/v1/activity-logs
Authorization: Bearer {TOKEN}
```

Vous verrez tous les logs: inscription, connexion, modifications...

### 6. Changer le mot de passe

```bash
PUT /api/v1/profile/change-password
Authorization: Bearer {TOKEN}
{
  "current_password": "Test@12345",
  "password": "NewPassword@123",
  "password_confirmation": "NewPassword@123"
}
```

### 7. Se déconnecter

```bash
POST /api/v1/auth/logout
Authorization: Bearer {TOKEN}
```

---

## � GESTION DES ALERTES

### 8. Créer une alerte

```bash
POST /api/v1/alerts
Authorization: Bearer {TOKEN}
{
  "name": "Alerte de sécurité",
  "content": "Attention: Tentative d'intrusion détectée",
  "urgency_level": "high",
  "channels": ["telegram"]
}
```

### 9. Lister les alertes

```bash
GET /api/v1/alerts
Authorization: Bearer {TOKEN}
```

### 10. Envoyer une alerte immédiatement

```bash
POST /api/v1/alerts/1/send
Authorization: Bearer {TOKEN}
```

**Réponse attendue:**
```json
{
  "status": "success",
  "message": "Alerte envoyée (simulation)",
  "data": {
    "alert_id": 1,
    "sent_at": "2026-04-07T14:30:00Z",
    "status": "sent",
    "channels": ["telegram"]
  }
}
```

### 11. Voir l'historique des alertes

```bash
GET /api/v1/alerts/history
Authorization: Bearer {TOKEN}
```

### 12. Créer une alerte programmée

```bash
POST /api/v1/alerts
Authorization: Bearer {TOKEN}
{
  "name": "Rappel réunion",
  "content": "Réunion d'équipe dans 30 minutes",
  "urgency_level": "medium",
  "channels": ["telegram"],
  "scheduled_at": "2026-04-07T15:00:00Z"
}
```

---

## �📚 Pour Aller Plus Loin

### Documentation Complète
Voir [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Structure du Projet
Voir [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md)

### Code Source
- Contrôleurs: `app/Http/Controllers/Api/`
- Modèles: `app/Models/`
- Routes: `routes/api.php`
- Migrations: `database/migrations/`

---

## 💡 Conseils d'Utilisation

### Pour Postman
1. **Variables d'environnement**: Les tokens sont auto-sauvegardés
2. **Tests**: Chaque requête peut définer des tests
3. **Pre-request Scripts**: Définir les variables avant la requête

### Pour cURL
- Toujours inclure le header `Content-Type: application/json`
- Le token doit être dans `Authorization: Bearer {token}`
- Utiliser les guillemets correctement en PowerShell

### Bonnes Pratiques
- ✅ Ne jamais partager vos tokens
- ✅ Toujours changer le mot de passe par défaut
- ✅ Utiliser HTTPS en production
- ✅ Régulièrement consulter les logs d'activité
- ✅ Tester avec des données fictives d'abord

---

## 🆘 Dépannage

### "Token expiré"
Les tokens Sanctum n'expirent pas automatiquement (en local). 
En production, implémenter l'expiration.

### "Unauthorized"
- Vérifiez que le token est dans l'en-tête `Authorization`
- Format: `Bearer {token}` (exact)
- Le token ne doit pas être oublié

### "Server error 500"
- Vérifier les logs: `storage/logs/laravel.log`
- Vérifier que la base de données est accessible
- Vérifier que les migrations ont été exécutées

---

## 🎓 Conceptsclés

### Authentification
- Basée sur les **tokens** (Sanctum)
- **Pas de cookies** - API stateless
- Token reçu lors du login/register

### Rôles & Permissions
- User par défaut → rôle `user`
- Rôle `admin` → accès complet
- Vérification automatique sur les endpoints

### Logs d'Activité
- Tous les événements importants enregistrés
- Consultables via `/activity-logs`
- Inclusion de IP et User-Agent
- Avant/après pour les modifications

---

**API Prête Utilisation! 🚀**

Commencez par une inscription, puis explorer les endpoints.
Consultez la documentation Postman pour un test interactif.
