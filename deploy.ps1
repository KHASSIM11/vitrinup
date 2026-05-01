param(
    [Parameter(Mandatory=$true)]
    [string]$message
)

# 2. Faire git add .
git add .

# 3. Faire git commit -m $message
git commit -m $message

# 4. Faire git push
git push

# 5. Lire SSH_HOST, SSH_PORT, SSH_USER, SSH_PASS, SSH_PATH depuis .env.deploy
$envVars = @{}
if (Test-Path ".env.deploy") {
    Get-Content ".env.deploy" | ForEach-Object {
        if ($_ -match '^(.*?)=(.*)$') {
            $key = $matches[1]
            $value = $matches[2]
            $envVars[$key] = $value
        }
    }
} else {
    Write-Error "Le fichier .env.deploy est introuvable."
    exit 1
}

$sshHost = $envVars["SSH_HOST"]
$sshPort = $envVars["SSH_PORT"]
$sshUser = $envVars["SSH_USER"]
$sshPass = $envVars["SSH_PASS"]
$sshPath = $envVars["SSH_PATH"]

if (-not $sshHost -or -not $sshPort -or -not $sshUser -or -not $sshPath) {
    Write-Error "Certaines variables d'environnement SSH sont manquantes dans .env.deploy."
    exit 1
}

# 6. Se connecter SSH à Hostinger et exécuter git fetch origin && git reset --hard origin/main sur le chemin SSH_PATH

# Utilisation de PSSession pour la connexion SSH
try {
    $session = New-PSSession -ComputerName $sshHost -Port $sshPort -Credential (New-Object System.Management.Automation.PSCredential($sshUser, (ConvertTo-SecureString $sshPass -AsPlainText -Force)))

    # Commande à exécuter sur le serveur distant
    $remoteCommand = "cd $sshPath; git fetch origin && git reset --hard origin/main"

    # Exécution de la commande
    Invoke-Command -Session $session -ScriptBlock { param($cmd) Invoke-Expression $cmd } -ArgumentList $remoteCommand

    Write-Host "Déploiement réussi sur $sshHost."

    # Fermeture de la session SSH
    Remove-PSSession $session

} catch {
    Write-Error "Erreur lors de la connexion SSH ou de l'exécution de la commande : $($_.Exception.Message)"
    # Fermeture de la session SSH en cas d'erreur
    if ($session) {
        Remove-PSSession $session
    }
    exit 1
}
