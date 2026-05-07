# ═══════════════════════════════════════════════════════════════════
#  deploy.ps1 — Déploiement VitrinUp → Hostinger
#
#  Usage :
#    .\deploy.ps1              → git push + git pull sur le serveur
#    .\deploy.ps1 -SkipPush    → seulement git pull (sans push)
#
#  Credentials dans .env.deploy (non versionné)
# ═══════════════════════════════════════════════════════════════════

param(
    [switch]$SkipPush
)

# ── Bannière ─────────────────────────────────────────────────────
Write-Host ""
Write-Host "  =====================================" -ForegroundColor Cyan
Write-Host "   VitrinUp -- Deploiement Hostinger  " -ForegroundColor Cyan
Write-Host "  =====================================" -ForegroundColor Cyan
Write-Host ""

# ── Lire .env.deploy ─────────────────────────────────────────────
$envFile = Join-Path $PSScriptRoot ".env.deploy"
if (-not (Test-Path $envFile)) {
    Write-Host "  ERREUR : .env.deploy introuvable" -ForegroundColor Red
    exit 1
}
$cfg = @{}
Get-Content $envFile | ForEach-Object {
    if ($_ -match '^([^=]+)=(.*)$') { $cfg[$matches[1].Trim()] = $matches[2].Trim() }
}

$sshTarget = "$($cfg['SSH_USER'])@$($cfg['SSH_HOST'])"
$sshPort   = $cfg['SSH_PORT']
$sshPath   = $cfg['SSH_PATH']

# ── Étape 1 : git push ───────────────────────────────────────────
if (-not $SkipPush) {
    Write-Host "  [1/2] Git push vers GitHub..." -ForegroundColor Yellow
    git push
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  ERREUR : git push a echoue." -ForegroundColor Red
        exit 1
    }
    Write-Host "        OK" -ForegroundColor Green
    Write-Host ""
}

# ── Étape 2 : git pull sur le serveur ────────────────────────────
Write-Host "  [2/2] Deploiement sur le serveur..." -ForegroundColor Yellow
Write-Host "        $sshTarget  port $sshPort" -ForegroundColor Gray
Write-Host "        $sshPath" -ForegroundColor Gray
Write-Host ""

$cmd = "cd $sshPath && git fetch origin && git reset --hard origin/main && echo DEPLOY_OK"
$result = ssh -p $sshPort -o StrictHostKeyChecking=no -o ConnectTimeout=15 $sshTarget $cmd

if ($result -match "DEPLOY_OK") {
    Write-Host ""
    Write-Host "  Deploiement termine avec succes !" -ForegroundColor Green

    # Afficher le dernier commit deployé
    $logCmd = "cd $sshPath && git log -1 --oneline && git diff --name-only HEAD~1 HEAD"
    $log = ssh -p $sshPort -o StrictHostKeyChecking=no $sshTarget $logCmd 2>$null
    if ($log) {
        Write-Host ""
        $log | ForEach-Object { Write-Host "  $_" -ForegroundColor Gray }
    }
} else {
    Write-Host ""
    Write-Host "  ATTENTION : Le deploiement SSH a peut-etre echoue." -ForegroundColor Yellow
    Write-Host "  Verifiez manuellement sur le serveur." -ForegroundColor Yellow
    if ($result) {
        Write-Host ""
        Write-Host $result -ForegroundColor DarkGray
    }
}

Write-Host ""
