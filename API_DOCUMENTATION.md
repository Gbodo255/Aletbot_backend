# API de Gestion des Utilisateurs - Documentation Complète

## Vue d'ensemble
Une API REST complète pour la gestion des utilisateurs avec authentification, rôles et permissions, profil utilisateur, et historique d'activité.

## Architecture

### Modèles de Données
- **User**: Utilisateurs du système
- **Role**: Rôles (admin, user, etc.)
- **Permission**: Permissions granulaires
- **NotificationPreference**: Préférences de notification par utilisateur
- **ActivityLog**: Historique d'activité et des alertes

### Relations
```
User -> has many Roles (many-to-many)
User -> has many ActivityLogs
User -> has one NotificationPreference
Role -> has many Permissions (many-to-many)
Role -> has many Users (many-to-many)
```

## Endpoints API

### 1. AUTHENTIFICATION

#### Register (Inscription)
```
POST /api/v1/auth/register
Content-Type: application/json

Request Body:
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+33612345678" // optionnel
}

Response: 201 Created
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+33612345678",
        "avatar": null,
        "bio": null,
        "roles": [
            {
                "id": 2,
                "name": "user",
                "description": "Standard user role"
            }
        ]
    },
    "token": "api_token_here"
}
```

#### Login (Connexion)
```
POST /api/v1/auth/login
Content-Type: application/json

Request Body:
{
    "email": "john@example.com",
    "password": "password123"
}

Response: 200 OK
{
    "message": "Login successful",
    "user": {...},
    "token": "api_token_here"
}
```

#### Logout (Déconnexion)
```
POST /api/v1/auth/logout
Authorization: Bearer {token}

Response: 200 OK
{
    "message": "Logout successful"
}
```

#### Get Current User
```
GET /api/v1/auth/me
Authorization: Bearer {token}

Response: 200 OK
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": [...],
        "notificationPreferences": {...}
    }
}
```

### 2. PROFIL UTILISATEUR

#### Get Profile
```
GET /api/v1/profile
Authorization: Bearer {token}

Response: 200 OK
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+33612345678",
        "avatar": "/storage/avatars/user_1.jpg",
        "bio": "Developer",
        "roles": [...],
        "notificationPreferences": {
            "email_notifications": true,
            "push_notifications": true,
            "sms_notifications": false,
            "activity_alerts": true,
            "security_alerts": true
        }
    }
}
```

#### Update Profile
```
PUT /api/v1/profile
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "name": "John Doe Updated",
    "phone": "+33612345679",
    "bio": "Senior Developer"
}

Response: 200 OK
{
    "message": "Profile updated successfully",
    "user": {...}
}
```

#### Update Notification Preferences
```
PUT /api/v1/profile/notification-preferences
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "email_notifications": true,
    "push_notifications": false,
    "sms_notifications": true,
    "activity_alerts": true,
    "security_alerts": true
}

Response: 200 OK
{
    "message": "Notification preferences updated successfully",
    "preferences": {
        "id": 1,
        "user_id": 1,
        "email_notifications": true,
        "push_notifications": false,
        "sms_notifications": true,
        "activity_alerts": true,
        "security_alerts": true
    }
}
```

#### Change Password
```
PUT /api/v1/profile/change-password
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}

Response: 200 OK
{
    "message": "Password changed successfully"
}
```

### 3. HISTORIQUE D'ACTIVITÉ

#### Get All Activity Logs (Admin Only)
```
GET /api/v1/activity-logs?user_id=1&action=login&date_from=2026-04-01&date_to=2026-04-07
Authorization: Bearer {token}

Response: 200 OK
{
    "logs": {
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "action": "login",
                "model": null,
                "model_id": null,
                "description": "User logged in",
                "old_values": null,
                "new_values": null,
                "ip_address": "192.168.1.1",
                "user_agent": "Mozilla/5.0...",
                "created_at": "2026-04-07T19:00:00Z"
            }
        ],
        "meta": {
            "total": 1,
            "per_page": 50,
            "current_page": 1
        }
    }
}
```

#### Get User Activity
```
GET /api/v1/activity-logs/user/1?action=update_profile
Authorization: Bearer {token}

Response: 200 OK
{
    "logs": {
        "data": [
            {
                "id": 2,
                "user_id": 1,
                "action": "update_profile",
                "model": "User",
                "model_id": 1,
                "description": "Profile updated",
                "old_values": {
                    "name": "John",
                    "bio": "Developer"
                },
                "new_values": {
                    "name": "John Doe",
                    "bio": "Senior Developer"
                },
                "ip_address": "192.168.1.1",
                "user_agent": "Mozilla/5.0...",
                "created_at": "2026-04-07T19:05:00Z"
            }
        ]
    }
}
```

#### Get Activity Log Detail
```
GET /api/v1/activity-logs/1
Authorization: Bearer {token}

Response: 200 OK
{
    "log": {...}
}
```

### 4. GESTION DES UTILISATEURS (Admin Only)

#### List Users
```
GET /api/v1/users
Authorization: Bearer {admin_token}

Response: 200 OK
{
    "users": {
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "roles": [
                    {"id": 2, "name": "user"}
                ]
            }
        ],
        "meta": {
            "total": 10,
            "per_page": 15,
            "current_page": 1
        }
    }
}
```

#### Get User Detail
```
GET /api/v1/users/1
Authorization: Bearer {admin_token}

Response: 200 OK
{
    "user": {...}
}
```

#### Assign Role to User
```
POST /api/v1/users/1/assign-role
Authorization: Bearer {admin_token}
Content-Type: application/json

Request Body:
{
    "role_id": 1
}

Response: 200 OK
{
    "message": "Role assigned successfully",
    "user": {
        "id": 1,
        "roles": [...]
    }
}
```

#### Remove Role from User
```
POST /api/v1/users/1/remove-role
Authorization: Bearer {admin_token}
Content-Type: application/json

Request Body:
{
    "role_id": 2
}

Response: 200 OK
{
    "message": "Role removed successfully",
    "user": {...}
}
```

#### Delete User
```
DELETE /api/v1/users/1
Authorization: Bearer {admin_token}

Response: 200 OK
{
    "message": "User deleted successfully"
}
```

### 5. GESTION DES RÔLES (Admin Only)

#### List Roles
```
GET /api/v1/roles
Authorization: Bearer {admin_token}

Response: 200 OK
{
    "roles": [
        {
            "id": 1,
            "name": "admin",
            "description": "Administrator role",
            "permissions": [
                {
                    "id": 1,
                    "name": "users.view"
                }
            ]
        }
    ]
}
```

#### Create Role
```
POST /api/v1/roles
Authorization: Bearer {admin_token}
Content-Type: application/json

Request Body:
{
    "name": "moderator",
    "description": "Moderator role"
}

Response: 201 Created
{
    "message": "Role created successfully",
    "role": {...}
}
```

#### Assign Permission to Role
```
POST /api/v1/roles/2/assign-permission
Authorization: Bearer {admin_token}
Content-Type: application/json

Request Body:
{
    "permission_id": 3
}

Response: 200 OK
{
    "message": "Permission assigned successfully",
    "role": {
        "id": 2,
        "permissions": [...]
    }
}
```

#### Remove Permission from Role
```
POST /api/v1/roles/2/remove-permission
Authorization: Bearer {admin_token}
Content-Type: application/json

Request Body:
{
    "permission_id": 3
}

Response: 200 OK
{
    "message": "Permission removed successfully",
    "role": {...}
}
```

#### Delete Role
```
DELETE /api/v1/roles/2
Authorization: Bearer {admin_token}

Response: 200 OK
{
    "message": "Role deleted successfully"
}
```

## Permissions par défaut
- users.view - Voir les utilisateurs
- users.create - Créer un utilisateur
- users.edit - Modifier un utilisateur
- users.delete - Supprimer un utilisateur
- roles.view - Voir les rôles
- roles.create - Créer un rôle
- roles.edit - Modifier un rôle
- roles.delete - Supprimer un rôle
- permissions.view - Voir les permissions
- permissions.create - Créer une permission
- permissions.edit - Modifier une permission
- permissions.delete - Supprimer une permission
- activity-logs.view - Voir les logs d'activité
- activity-logs.export - Exporter les logs

## Rôles par défaut
- **admin**: Accès complet à tous les endpoints
- **user**: Accès limité (peut voir ses logs d'activité)

## Authentification
- Utilise **Laravel Sanctum** pour les tokens API
- Les tokens sont générés lors de l'inscription et la connexion
- Les tokens doivent être envoyés dans l'en-tête `Authorization: Bearer {token}`

## Gestion des erreurs

### 401 Unauthorized
```json
{
    "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
    "message": "Unauthorized"
}
```

### 422 Unprocessable Entity
```json
{
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### 404 Not Found
```json
{
    "message": "Resource not found"
}
```

## Logging et Suivi d'Activité
Tous les événements importants sont enregistrés:
- Connexion/déconnexion
- Modifications de profil
- Changements de mot de passe
- Modifications de préférences de notification
- Attributions/suppression de rôles
- Suppressions d'utilisateurs
- Création/modification/suppression de rôles

Chaque log contient:
- L'ID de l'utilisateur
- L'action effectuée
- Les anciennes et nouvelles valeurs
- L'adresse IP
- Le User-Agent
- Timestamp

---

### 6. GESTION DES ALERTES

Le système de gestion des alertes permet de créer, planifier et envoyer des alertes avec différents niveaux d'urgence et canaux de réception.

#### Modèle de données Alert
```json
{
  "id": 1,
  "name": "Alerte sécurité",
  "content": "Message d'alerte important",
  "type": "telegram",
  "urgency_level": "high",
  "channels": ["telegram"],
  "status": "pending",
  "scheduled_at": "2026-04-07T15:00:00Z",
  "sent_at": "2026-04-07T15:05:00Z",
  "recurrence": null,
  "user_id": 1,
  "created_at": "2026-04-07T10:00:00Z",
  "updated_at": "2026-04-07T10:00:00Z",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### Liste des Alertes
```
GET /api/v1/alerts
Authorization: Bearer {token}

Query Parameters (optionnels):
- status: pending|sent|failed
- urgency: low|medium|high|critical
- scheduled: true|false (alertes programmées)
- page: numéro de page
- per_page: éléments par page

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "Alerte de test",
      "urgency_level": "medium",
      "status": "pending",
      "scheduled_at": null,
      "created_at": "2026-04-07T10:00:00Z",
      "user": {
        "id": 1,
        "name": "John Doe"
      }
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "per_page": 15,
      "current_page": 1,
      "last_page": 1,
      "from": 1,
      "to": 1
    }
  }
}
```

#### Créer une Alerte
```
POST /api/v1/alerts
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "name": "Alerte importante",
  "content": "Ceci est une alerte de sécurité critique",
  "urgency_level": "high",
  "channels": ["telegram"],
  "scheduled_at": "2026-04-07T15:00:00Z" // optionnel pour planification
}

Response: 201 Created
{
  "message": "Alerte créée avec succès",
  "data": {
    "id": 1,
    "name": "Alerte importante",
    "content": "Ceci est une alerte de sécurité critique",
    "urgency_level": "high",
    "channels": ["telegram"],
    "status": "pending",
    "scheduled_at": "2026-04-07T15:00:00Z",
    "user_id": 1,
    "created_at": "2026-04-07T10:00:00Z",
    "updated_at": "2026-04-07T10:00:00Z"
  }
}
```

#### Détail d'une Alerte
```
GET /api/v1/alerts/1
Authorization: Bearer {token}

Response: 200 OK
{
  "data": {
    "id": 1,
    "name": "Alerte importante",
    "content": "Ceci est une alerte de sécurité critique",
    "urgency_level": "high",
    "channels": ["telegram"],
    "status": "pending",
    "scheduled_at": "2026-04-07T15:00:00Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

#### Modifier une Alerte
```
PUT /api/v1/alerts/1
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "name": "Alerte sécurité modifiée",
  "content": "Contenu modifié",
  "urgency_level": "critical"
}

Response: 200 OK
{
  "message": "Alerte mise à jour avec succès",
  "data": {
    "id": 1,
    "name": "Alerte sécurité modifiée",
    "content": "Contenu modifié",
    "urgency_level": "critical",
    "status": "pending"
  }
}
```

#### Supprimer une Alerte
```
DELETE /api/v1/alerts/1
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Alerte supprimée avec succès"
}
```

#### Envoyer une Alerte Immédiatement
```
POST /api/v1/alerts/1/send
Authorization: Bearer {token}

Response: 200 OK
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

#### Historique des Alertes
```
GET /api/v1/alerts/history
Authorization: Bearer {token}

Query Parameters (optionnels):
- start_date: 2026-04-01
- end_date: 2026-04-07
- urgency: high|medium|low|critical
- page: numéro de page
- per_page: éléments par page

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "Alerte de test",
      "content": "Message de test",
      "status": "sent",
      "urgency_level": "medium",
      "channels": ["telegram"],
      "sent_at": "2026-04-07T14:30:00Z",
      "user": {
        "id": 1,
        "name": "John Doe"
      }
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "per_page": 15,
      "current_page": 1,
      "last_page": 1
    }
  }
}
```

#### Permissions pour les Alertes
- `alerts.view` - Voir les alertes
- `alerts.create` - Créer des alertes
- `alerts.edit` - Modifier des alertes
- `alerts.delete` - Supprimer des alertes
- `alerts.send` - Envoyer des alertes

#### Règles d'autorisation
- **Admin**: Peut voir/modifier toutes les alertes
- **User standard**: Peut seulement voir/modifier ses propres alertes
- Les alertes déjà envoyées ne peuvent pas être modifiées ou supprimées
- Seuls les admins peuvent envoyer des alertes d'autres utilisateurs

#### Simulation d'envoi
Au lieu d'envoyer réellement des messages Telegram, le système:
1. Log le message dans les logs Laravel (`storage/logs/laravel.log`)
2. Met à jour le statut de l'alerte à "sent"
3. Enregistre la date d'envoi
4. Retourne une réponse de succès

#### Niveaux d'urgence
- `low` - Faible priorité
- `medium` - Priorité normale
- `high` - Haute priorité
- `critical` - Priorité critique

#### Statuts des alertes
- `pending` - En attente d'envoi
- `sent` - Envoyée avec succès
- `failed` - Échec d'envoi

#### Planification
Les alertes peuvent être planifiées pour un envoi futur:
```json
{
  "scheduled_at": "2026-04-07T15:00:00Z"
}
```

Les alertes programmées sont automatiquement envoyées à l'heure prévue (nécessite un cron job pour le traitement automatique).
