@echo off
REM Script batch pour démarrer l'API facilement sur Windows

echo ========================================
echo API Gestion des Utilisateurs - Laravel
echo ========================================
echo.

cd /d C:\Users\Temp\Desktop\Projectapp\backend

echo Verification de PHP...
php --version > nul 2>&1
if errorlevel 1 (
    echo ERREUR: PHP n'est pas installe ou non accessible!
    echo Assurez-vous que PHP 8.3 est installe.
    pause
    exit /b 1
)

echo ✓ PHP OK
echo.

echo Verification de Composer...
composer --version > nul 2>&1
if errorlevel 1 (
    echo ERREUR: Composer n'est pas installe!
    echo Installer Composer depuis https://getcomposer.org
    pause
    exit /b 1
)

echo ✓ Composer OK
echo.

echo Installation des dépendances...
composer install --quiet
if errorlevel 1 (
    echo ERREUR: Installation des dépendances échouée!
    pause
    exit /b 1
)

echo ✓ Dépendances OK
echo.

echo Nettoyage et configuration...
php artisan config:cache > nul 2>&1
php artisan view:cache > nul 2>&1
php artisan route:cache > nul 2>&1

echo.
echo ========================================
echo DÉMARRAGE DE L'API
echo ========================================
echo.
echo URL de l'API: http://127.0.0.1:8001/api/v1
echo.
echo Endpoints disponibles:
echo   POST   /auth/register     - Inscription
echo   POST   /auth/login        - Connexion
echo   GET    /auth/me           - Profil courant
echo   POST   /auth/logout       - Déconnexion
echo   GET    /profile           - Récupérer profil
echo   PUT    /profile           - Modifier profil
echo   GET    /activity-logs     - Historique
echo.
echo Documentation:
echo   - API_DOCUMENTATION.md    - Endpoints complets
echo   - QUICK_START.md          - Guide de démarrage
echo   - postman_collection.json - Collection Postman
echo.
echo Importer la collection Postman pour tester facilement!
echo.
echo Appuyez sur Ctrl+C pour arrêter le serveur
echo.
echo ========================================
echo.

php artisan serve --port=8001

pause
