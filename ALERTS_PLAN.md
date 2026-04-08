# PLAN DE DÉVELOPPEMENT - GESTION DES ALERTES

## 📋 **Vue d'ensemble de la fonctionnalité**

**Objectif:** Implémenter un système complet de gestion des alertes avec:
- ✅ Création d'alertes (nom, contenu, urgence, canaux)
- ✅ Planification (immédiate ou récurrente)
- ✅ Historique et statuts (pending, sent, failed)
- ✅ CRUD complet (créer, lire, modifier, supprimer)
- ✅ Simulation d'envoi (logs Laravel au lieu de Telegram)

---

## 🏗️ **Architecture proposée**

### 1. **Modèle de données - Table `alerts`**
```sql
- id (primary key)
- name (string) - Nom de l'alerte
- content (text) - Contenu du message
- type (enum: 'telegram') - Type d'alerte
- urgency_level (enum: 'low', 'medium', 'high', 'critical')
- channels (json) - Canaux de réception ['telegram', 'email', etc.]
- status (enum: 'pending', 'sent', 'failed')
- scheduled_at (datetime, nullable) - Pour planification
- sent_at (datetime, nullable) - Date d'envoi réel
- recurrence (json, nullable) - {'type': 'daily', 'interval': 1}
- user_id (foreign key) - Créateur de l'alerte
- created_at, updated_at
```

### 2. **Modèle Alert (app/Models/Alert.php)**
- Relations: belongsTo(User)
- Scopes: pending(), sent(), failed(), scheduled()
- Méthodes: isRecurring(), shouldSendNow(), markAsSent()

### 3. **Contrôleur AlertController (app/Http/Controllers/Api/AlertController.php)**
- `index()` - Liste des alertes (avec filtres)
- `store()` - Créer une alerte
- `show()` - Détail d'une alerte
- `update()` - Modifier une alerte
- `destroy()` - Supprimer une alerte
- `send()` - Envoyer une alerte immédiatement
- `history()` - Historique des envois

### 4. **Service AlertService (app/Services/AlertService.php)**
- `sendAlert(Alert $alert)` - Simulation d'envoi
- `scheduleAlert(Alert $alert)` - Planification
- `processScheduledAlerts()` - Traitement des alertes programmées (command/job)

---

## 🔐 **Permissions et Autorisation**

### Permissions à ajouter:
- `alerts.view` - Voir les alertes
- `alerts.create` - Créer des alertes
- `alerts.edit` - Modifier des alertes
- `alerts.delete` - Supprimer des alertes
- `alerts.send` - Envoyer des alertes

### Rôles:
- **Admin**: Toutes les permissions
- **User**: `alerts.view`, `alerts.create` (alertes personnelles uniquement)

---

## 🛣️ **Routes API**

```php
// Dans routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('alerts')->group(function () {
        Route::get('/', [AlertController::class, 'index']);           // Liste
        Route::post('/', [AlertController::class, 'store']);          // Créer
        Route::get('/{alert}', [AlertController::class, 'show']);     // Détail
        Route::put('/{alert}', [AlertController::class, 'update']);   // Modifier
        Route::delete('/{alert}', [AlertController::class, 'destroy']); // Supprimer
        Route::post('/{alert}/send', [AlertController::class, 'send']); // Envoyer
        Route::get('/history', [AlertController::class, 'history']);  // Historique
    });
});
```

---

## 📊 **Endpoints détaillés**

### 1. **GET /api/v1/alerts** - Liste des alertes
**Query params:**
- `status` (pending|sent|failed)
- `urgency` (low|medium|high|critical)
- `scheduled` (true|false) - Alertes programmées
- `page`, `per_page` - Pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Alerte sécurité",
      "content": "Message d'alerte",
      "urgency_level": "high",
      "status": "pending",
      "scheduled_at": "2026-04-07 15:00:00",
      "created_at": "2026-04-07T10:00:00Z"
    }
  ],
  "meta": { "pagination": {...} }
}
```

### 2. **POST /api/v1/alerts** - Créer une alerte
**Request:**
```json
{
  "name": "Alerte importante",
  "content": "Ceci est une alerte de test",
  "urgency_level": "medium",
  "channels": ["telegram"],
  "scheduled_at": "2026-04-07 15:00:00", // optionnel pour planification
  "recurrence": { // optionnel pour récurrence
    "type": "daily",
    "interval": 1
  }
}
```

### 3. **POST /api/v1/alerts/{id}/send** - Envoyer immédiatement
**Response (simulation):**
```json
{
  "status": "success",
  "message": "Alerte envoyée (simulation)",
  "data": {
    "alert_id": 1,
    "sent_at": "2026-04-07T14:30:00Z",
    "status": "sent"
  }
}
```

### 4. **GET /api/v1/alerts/history** - Historique
**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Alerte test",
      "status": "sent",
      "sent_at": "2026-04-07T14:30:00Z",
      "channels": ["telegram"],
      "log_entry": "Alert sent via simulation"
    }
  ]
}
```

---

## 🔄 **Plan d'implémentation (étapes)**

### Phase 1: Base de données
1. ✅ Créer migration `create_alerts_table.php`
2. ✅ Créer modèle `Alert.php` avec relations et méthodes
3. ✅ Ajouter permissions dans `RoleAndPermissionSeeder.php`

### Phase 2: Logique métier
4. ✅ Créer `AlertService.php` pour la simulation d'envoi
5. ✅ Créer `AlertController.php` avec toutes les méthodes CRUD
6. ✅ Ajouter routes dans `routes/api.php`

### Phase 3: Planification (optionnel)
7. ⏳ Créer command `ProcessScheduledAlerts` (si récurrence implémentée)
8. ⏳ Ajouter tâche cron pour traitement automatique

### Phase 4: Tests et documentation
9. ✅ Tests manuels avec Postman
10. ✅ Mise à jour documentation API
11. ✅ Activity logging pour les actions alertes

---

## 🎯 **Validation et tests**

### Tests à effectuer:
1. **Création d'alerte** - Vérifier sauvegarde en DB
2. **Envoi immédiat** - Vérifier log Laravel et statut
3. **Planification** - Vérifier scheduled_at
4. **Historique** - Vérifier récupération des alertes envoyées
5. **Permissions** - Vérifier accès selon rôles
6. **CRUD** - Créer, modifier, supprimer

### Points de validation:
- ✅ Statuts corrects (pending → sent)
- ✅ Logs Laravel générés
- ✅ Réponses JSON conformes
- ✅ Autorisation fonctionnelle
- ✅ Activity logging intégré

---

## 📋 **Checklist de validation**

- [ ] Migration créée et exécutable
- [ ] Modèle Alert avec toutes les méthodes
- [ ] Permissions ajoutées au seeder
- [ ] AlertController implémenté
- [ ] Routes ajoutées
- [ ] AlertService pour simulation
- [ ] Tests manuels réussis
- [ ] Documentation mise à jour
- [ ] Activity logging intégré

---

**Prêt à commencer l'implémentation ?** 🚀