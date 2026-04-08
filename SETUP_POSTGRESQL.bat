@echo off
REM Configuration PostgreSQL pour le projet Laravel

echo.
echo ========================================
echo   Configuration PostgreSQL
echo ========================================
echo.

REM Vérifier si psql est disponible
where psql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERREUR: PostgreSQL n'est pas installé ou non accessible
    echo.
    echo Veuillez installer PostgreSQL depuis:
    echo https://www.postgresql.org/download/windows/
    echo.
    pause
    exit /b 1
)

echo ✓ PostgreSQL détecté

REM Demander à l'utilisateur
echo.
echo Quelle est votre configuration PostgreSQL?
echo Laissez vide pour utiliser la config par défaut
echo.

set /p DB_HOST=DB_HOST (défaut: 127.0.0.1):
if "%DB_HOST%"=="" set DB_HOST=127.0.0.1

set /p DB_PORT=DB_PORT (défaut: 5432):
if "%DB_PORT%"=="" set DB_PORT=5432

set /p DB_USERNAME=DB_USERNAME (défaut: postgres):
if "%DB_USERNAME%"=="" set DB_USERNAME=postgres

set /p DB_PASSWORD=DB_PASSWORD (défaut: password):
if "%DB_PASSWORD%"=="" set DB_PASSWORD=password

set /p DB_DATABASE=DB_DATABASE (défaut: laravel_api):
if "%DB_DATABASE%"=="" set DB_DATABASE=laravel_api

echo.
echo ========================================
echo   Configuration résumée
echo ========================================
echo Host: %DB_HOST%
echo Port: %DB_PORT%
echo Username: %DB_USERNAME%
echo Database: %DB_DATABASE%
echo ========================================
echo.

REM Créer la base de données
echo Création de la base de données '%DB_DATABASE%'...
echo.

REM Essayer de créer la base avec SET PGPASSWORD
setlocal enabledelayedexpansion
set PGPASSWORD=%DB_PASSWORD%

psql -h %DB_HOST% -p %DB_PORT% -U %DB_USERNAME% -tc "SELECT 1 FROM pg_database WHERE datname = '%DB_DATABASE%'" | findstr /C:"1" >nul

if %ERRORLEVEL% EQU 0 (
    echo ✓ La base '%DB_DATABASE%' existe déjà
) else (
    echo Création de la base...
    psql -h %DB_HOST% -p %DB_PORT% -U %DB_USERNAME% -c "CREATE DATABASE %DB_DATABASE%;"
    
    if %ERRORLEVEL% EQU 0 (
        echo ✓ Base de données '%DB_DATABASE%' créée avec succès
    ) else (
        echo ✗ Erreur lors de la création de la base
        pause
        exit /b 1
    )
)

endlocal

echo.
echo ========================================
echo   Mise à jour du fichier .env
echo ========================================
echo.

REM Créer/mettre à jour le fichier .env
(
    for /f "delims=" %%a in (.env) do (
        if "%%a"=="" (
            echo.
        ) else (
            setlocal enabledelayedexpansion
            set line=%%a
            if "!line:~0,14!"=="DB_CONNECTION=" (
                echo DB_CONNECTION=pgsql
            ) else if "!line:~0,8!"=="DB_HOST=" (
                echo DB_HOST=%DB_HOST%
            ) else if "!line:~0,8!"=="DB_PORT=" (
                echo DB_PORT=%DB_PORT%
            ) else if "!line:~0,12!"=="DB_DATABASE=" (
                echo DB_DATABASE=%DB_DATABASE%
            ) else if "!line:~0,12!"=="DB_USERNAME=" (
                echo DB_USERNAME=%DB_USERNAME%
            ) else if "!line:~0,12!"=="DB_PASSWORD=" (
                echo DB_PASSWORD=%DB_PASSWORD%
            ) else (
                echo !line!
            )
            endlocal
        )
    )
) > .env.tmp

move /y .env.tmp .env

echo ✓ Fichier .env mis à jour

echo.
echo ========================================
echo   Vérification de la connexion
echo ========================================
echo.

php artisan db:check

if %ERRORLEVEL% EQU 0 (
    echo ✓ Connexion à PostgreSQL réussie
) else (
    echo ✗ Erreur de connexion
    echo.
    echo Vérifiez:
    echo - PostgreSQL est en cours d'exécution
    echo - Les identifiants sont corrects
    echo - La base de données existe
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Exécution des migrations
echo ========================================
echo.

php artisan migrate:fresh --seed

if %ERRORLEVEL% EQU 0 (
    echo ✓ Migrations exécutées avec succès
) else (
    echo ✗ Erreur lors de l'exécution des migrations
    pause
    exit /b 1
)

echo.
echo ========================================
echo   ✓ Configuration PostgreSQL terminée!
echo ========================================
echo.
echo L'API est prête à l'emploi.
echo Démarrer le serveur avec: php artisan serve --port=8001
echo.

pause