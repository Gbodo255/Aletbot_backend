# 🐘 Configuration PostgreSQL

## Vue d'ensemble

Le projet Laravel est maintenant configuré pour utiliser **PostgreSQL** comme base de données principale.

**Configuration par défaut:**
- **Type BD**: PostgreSQL
- **Host**: 127.0.0.1
- **Port**: 5432
- **Base de données**: laravel_api
- **Utilisateur**: postgres
- **Mot de passe**: password

---

## 📋 Prérequis

### Windows

#### 1. Installer PostgreSQL

1. Télécharger PostgreSQL depuis [https://www.postgresql.org/download/windows/](https://www.postgresql.org/download/windows/)
2. Exécuter le programme d'installation
3. **Étapes importantes:**
   - Définir un mot de passe pour l'utilisateur `postgres`
   - Garder le port par défaut: **5432**
   - Cocher "Initialize Database Cluster"

#### 2. Vérifier l'installation

Ouvrir PowerShell et taper:
```powershell
psql --version
```

Vous devriez voir: `psql (PostgreSQL) X.X.X`

### Mac

Installer via Homebrew:
```bash
brew install postgresql
brew services start postgresql
```

### Linux

Installer via le gestionnaire de paquets:
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install postgresql postgresql-contrib

# CentOS/RHEL
sudo yum install postgresql-server postgresql-contrib
sudo systemctl start postgresql
```

---

## 🔧 Étapes de configuration

### 1. Modifier le fichier .env

Le fichier `.env` est déjà configuré pour PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_api
DB_USERNAME=postgres
DB_PASSWORD=password
DB_CHARSET=utf8
```

**Si votre configuration est différente, modifiez ces valeurs.**

### 2. Créer la base de données

Deux options:

#### Option A: Via psql (recommandé)

Ouvrir PowerShell et taper:
```powershell
psql -U postgres -c "CREATE DATABASE laravel_api;"
```

Vous serez invité à entrer le mot de passe PostgreSQL.

#### Option B: Via pgAdmin

1. Ouvrir pgAdmin (interface web de PostgreSQL)
2. Se connecter avec les identifiants PostgreSQL
3. Clic droit sur "Databases" → "Create" → "Database"
4. Nom: `laravel_api`
5. Cliquer sur "Create"

### 3. Vérifier la connexion

Ouvrir PowerShell:
```powershell
cd c:\Users\Temp\Desktop\Projectapp\backend
php artisan db:check
```

Résultat attendu:
```
The configured database connection is operational.
```

### 4. Exécuter les migrations

```powershell
php artisan migrate:fresh --seed
```

Cela créera toutes les tables et remplira les données initiales.

---

## 🗂️ Création de plusieurs bases de données

Si vous avez besoin de plusieurs bases (dev, test, prod):

### Base de développement (déjà créée)
```powershell
psql -U postgres -c "CREATE DATABASE laravel_api;"
```

### Base de test
```powershell
psql -U postgres -c "CREATE DATABASE laravel_api_testing;"
```

### Base de production
```powershell
psql -U postgres -c "CREATE DATABASE laravel_api_production;"
```

Puis, pour chaque environnement, créer un fichier `.env` adapté:
- `.env` → laravel_api (développement)
- `.env.testing` → laravel_api_testing (tests)
- `.env.production` → laravel_api_production (production)

---

## 🚀 Démarrer avec PostgreSQL

### 1. Démarrer PostgreSQL

**Windows:**
PostgreSQL démarre automatiquement en tant que service. Vérifier:
```powershell
Get-Service postgresql-x64-*
```

**Mac:**
```bash
brew services start postgresql
```

**Linux:**
```bash
sudo systemctl start postgresql
```

### 2. Démarrer le serveur Laravel

```powershell
cd c:\Users\Temp\Desktop\Projectapp\backend
php artisan serve --port=8001
```

### 3. Tester l'API

```bash
curl -X POST http://127.0.0.1:8001/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!"
  }'
```

---

## 🐛 Dépannage

### Erreur: "could not connect to server"

**Cause:** PostgreSQL n'est pas en cours d'exécution

**Windows:**
```powershell
# Démarrer PostgreSQL
net start postgresql-x64-15  # (remplacer 15 par votre version)
```

**Mac/Linux:**
```bash
brew services start postgresql
# ou
sudo systemctl start postgresql
```

### Erreur: "FATAL: role 'postgres' does not exist"

PostgreSQL n'a pas d'utilisateur `postgres`. Vous pouvez:

1. Créer l'utilisateur:
   ```powershell
   createuser -U postgres postgres
   ```

2. OU modifier `.env` avec votre utilisateur:
   ```env
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

### Erreur: "does not exist"

La base de données `laravel_api` n'existe pas. Créez-la:
```powershell
psql -U postgres -c "CREATE DATABASE laravel_api;"
```

### Erreur: "Identification échouée pour l'utilisateur postgres"

Le mot de passe est incorrect. Deux solutions:

**Option 1:** Changer le mot de passe PostgreSQL
```powershell
psql -U postgres
# Puis dans psql:
ALTER USER postgres WITH PASSWORD 'new_password';
\q
```

**Option 2:** Modifier le mot de passe dans `.env`
```env
DB_PASSWORD=your_correct_password
```

---

## 📊 Outils PostgreSQL

### pgAdmin (Interface web)

Accessible après installation de PostgreSQL:
- URL: http://localhost:5050
- Utilisateur: pgadmin4@pgadmin.org
- Mot de passe: admin

### psql (Ligne de commande)

Connexion:
```powershell
psql -U postgres -h 127.0.0.1
```

Commandes utiles:
```sql
\l                              -- Lister les bases
\c laravel_api                  -- Connecter à laravel_api
\dt                             -- Lister les tables
\d table_name                   -- Détail d'une table
SELECT * FROM users;            -- Voir les données
\q                              -- Quitter
```

### DBeaver (Client DB complet)

Télécharger: [https://dbeaver.io/download/](https://dbeaver.io/download/)

Configuration:
- **Serveur:** 127.0.0.1
- **Port:** 5432
- **BD:** laravel_api
- **Utilisateur:** postgres
- **Mot de passe:** password

---

## 🔄 Migration de SQLite vers PostgreSQL

Si vous aviez précédemment une base SQLite, migrer les données:

### 1. Exporter les données (optionnel)

Les migrations créeront les tables vides, donc il n'est pas nécessaire d'exporter les anciennes données.

### 2. Créer et peupler la BD PostgreSQL

```powershell
# Créer la base
psql -U postgres -c "CREATE DATABASE laravel_api;"

# Exécuter les migrations
cd c:\Users\Temp\Desktop\Projectapp\backend
php artisan migrate:fresh --seed
```

### 3. Vérifier les données

```powershell
psql -U postgres -d laravel_api -c "SELECT COUNT(*) FROM users;"
```

---

## 📝 Variables d'environnement PostgreSQL

| Variable | Valeur | Description |
|----------|--------|-------------|
| DB_CONNECTION | pgsql | Type de BD |
| DB_HOST | 127.0.0.1 | Adresse du serveur |
| DB_PORT | 5432 | Port PostgreSQL |
| DB_DATABASE | laravel_api | Nom de la BD |
| DB_USERNAME | postgres | Utilisateur |
| DB_PASSWORD | password | Mot de passe |
| DB_CHARSET | utf8 | Encodage |

---

## ✅ Checklist de configuration

- [ ] PostgreSQL installé et en cours d'exécution
- [ ] Base de données `laravel_api` créée
- [ ] Fichier `.env` configuré correctement
- [ ] `php artisan db:check` fonctionne
- [ ] `php artisan migrate:fresh --seed` réussit
- [ ] API démarre sans erreur
- [ ] Tests API réussissent

---

## 🔗 Ressources utiles

- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Laravel Database Configuration](https://laravel.com/docs/11.x/database)
- [pgAdmin](https://www.pgadmin.org/)
- [psql Command Reference](https://www.postgresql.org/docs/current/app-psql.html)

---

**Configuration complétée pour PostgreSQL!** 🐘✅