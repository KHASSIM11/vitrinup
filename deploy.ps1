param([string]$message = "deploy")

# Git push
git push

# Lire .env.deploy
$env = @{}
Get-Content ".env.deploy" | ForEach-Object {
    if ($_ -match '^(.*?)=(.*)$') { $env[$matches[1]] = $matches[2] }
}

# SSH avec ssh natif
$cmd = "cd $($env['SSH_PATH']) && git fetch origin && git reset --hard origin/main"
Write-Host "Deploiement sur Hostinger..."
ssh -p $env['SSH_PORT'] -o StrictHostKeyChecking=no "$($env['SSH_USER'])@$($env['SSH_HOST'])" $cmd
Write-Host "Deploiement termine !"
