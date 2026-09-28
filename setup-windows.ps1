$ErrorActionPreference = 'Stop'
$project = $PSScriptRoot
$php = 'C:\xampp\php\php.exe'
if (-not (Test-Path $php)) { throw 'XAMPP PHP was not found at C:\xampp\php\php.exe. Install XAMPP or edit $php in this script.' }
$composer = Join-Path $project 'composer.phar'
if (-not (Test-Path $composer)) {
    Write-Host 'Downloading Composer...'
    Invoke-WebRequest 'https://getcomposer.org/download/latest-stable/composer.phar' -OutFile $composer
}
$starter = Join-Path $env:TEMP ('pos-ci4-' + [guid]::NewGuid().ToString('N'))
try {
    Write-Host 'Installing the official CodeIgniter 4 app starter (requires internet)...'
    & $php $composer create-project codeigniter4/appstarter $starter --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'CodeIgniter installation failed. Check internet access and enable intl, mbstring, mysqli, and fileinfo in C:\xampp\php\php.ini.' }
    Get-ChildItem $starter -Recurse -Force -File | ForEach-Object {
        $relative = $_.FullName.Substring($starter.Length + 1)
        $target = Join-Path $project $relative
        if (-not (Test-Path $target)) {
            $parent = Split-Path $target -Parent
            if (-not (Test-Path $parent)) { New-Item -ItemType Directory -Path $parent -Force | Out-Null }
            Copy-Item -LiteralPath $_.FullName -Destination $target
        }
    }
    if (-not (Test-Path (Join-Path $project '.env'))) { Copy-Item (Join-Path $project '.env.example') (Join-Path $project '.env') }
    Write-Host 'Ready! Edit .env, create the database, then run migrations and the seeder as shown in README.md.'
} finally {
    if (Test-Path $starter) { Remove-Item $starter -Recurse -Force }
}
