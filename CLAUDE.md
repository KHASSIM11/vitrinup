# CLAUDE.md — VitrinUp

## Projet
Boutique en ligne de vente de chaussures (single store). Panel admin complet.
- **URL production** : https://vitrinup.stokup.net
- **Stack** : PHP 8.x OOP, MySQL, Apache, JavaScript vanilla, CSS natif
- **Architecture** : MVC custom — `controllers/`, `views/`, `models/`, `config/`, `core/`
- **Langue** : Français (code, messages, commentaires)
- **Hébergeur** : Hostinger (SSH port 65002, user `u640824467`, IP `145.79.20.92`)

## Workflow obligatoire après chaque modification

**Chaque fois que tu modifies un ou plusieurs fichiers, tu DOIS automatiquement :**

```
1. git add <fichiers modifiés>
2. git commit -m "type(scope): description en français"
3. git push origin main
4. .\deploy.ps1 -SkipPush    ← git pull sur le serveur Hostinger
```

**Ne jamais attendre que l'utilisateur demande le déploiement.** Le déploiement fait partie de chaque modification.

### Types de commits
- `feat` — nouvelle fonctionnalité
- `fix` — correction de bug
- `refactor` — refactoring sans changement de comportement
- `style` — CSS/UI uniquement
- `chore` — maintenance, config, scripts

## Architecture MVC

```
controllers/          ← logique métier (AdminXxxController.php, HomeController.php...)
views/
  admin/
    layout/           ← header.php + footer.php partagés (require_once dans chaque vue)
    dashboard.php
    produits/         ← index.php, form.php
    categories/       ← index.php, form.php
    commandes/        ← index.php, voir.php
    stocks/           ← index.php, entree.php, sortie.php, historique.php, export_pdf.php
    admins/           ← index.php, form.php
  home/
  catalogue/
  ...
models/               ← classes Produit, Categorie, Commande...
core/
  App.php             ← routeur principal
  Controller.php      ← classe de base (view(), model())
  Database.php        ← PDO wrapper
config/
  config.php          ← constantes (URL_ROOT, SITE_NAME, DB_*, UPLOAD_*, STOCK_SEUIL_ALERTE)
assets/
  css/admin.css       ← styles admin (cache-buster ?v=N à incrémenter si modifié)
  js/admin.js
```

## Règles de code

- **Vues admin** : toujours `require_once __DIR__ . '/../layout/header.php'` en tête et `footer.php` en fin
- **Variables de layout** : définir `$pageTitle` et `$activePage` avant le `require_once` du header
- **Flash messages** : utiliser `$_SESSION['flash_success']` et `$_SESSION['flash_error']`
- **Sécurité** : `htmlspecialchars()` sur toutes les sorties utilisateur
- **PDO** : utiliser le wrapper `Database` — `$this->db->query()->bind()->execute()` ou `->single()` / `->resultSet()`
- **CSS inline** : éviter, préférer les classes dans `admin.css`
- **Pas de commentaires** sauf si le WHY est non-évident

## Déploiement — détail technique

Le script `deploy.ps1` lit `.env.deploy` (non versionné) et fait :
1. `git push` vers GitHub (sauf `-SkipPush`)
2. SSH sur `145.79.20.92:65002` → `git fetch && git reset --hard origin/main`

Les clés SSH sont déjà configurées (pas de mot de passe interactif).

## Fichiers critiques

| Fichier | Rôle |
|---|---|
| `core/App.php` | Routeur — ajouter toute nouvelle route ici |
| `views/admin/layout/header.php` | Sidebar + nav (modifier `$_nav` pour nouveaux menus) |
| `assets/css/admin.css` | Tous les styles admin |
| `config/config.php` | Constantes globales |
| `.env.deploy` | Credentials SSH (jamais versionné) |
| `deploy.ps1` | Script de déploiement |
