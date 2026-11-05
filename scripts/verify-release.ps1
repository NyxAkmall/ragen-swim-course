# Ragen Swim Course — local-only release verification.
# Run from the project root. Never run on production hosting.
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)
$php = 'C:\xampp\php\php.exe'
$mysql = 'C:\xampp\mysql\bin\mysql.exe'
if (-not (Test-Path $php)) { throw "PHP XAMPP tidak ditemukan: $php" }
if (-not (Test-Path $mysql)) { throw "MySQL XAMPP tidak ditemukan: $mysql" }
if (-not (Test-Path '.\vendor\autoload.php')) { throw 'Dependensi vendor belum tersedia.' }
if (-not (Test-Path '.\phpunit.xml')) { throw 'phpunit.xml tidak ditemukan.' }
[xml]$config = Get-Content '.\phpunit.xml' -Raw
$databaseNode = $config.phpunit.php.env | Where-Object { $_.name -eq 'DB_DATABASE' } | Select-Object -First 1
$connectionNode = $config.phpunit.php.env | Where-Object { $_.name -eq 'DB_CONNECTION' } | Select-Object -First 1
if ($databaseNode.value -ne 'kelas_renang_testing' -or $connectionNode.value -ne 'mysql') {
    throw 'Pengujian dibatalkan: phpunit.xml tidak menunjuk MySQL kelas_renang_testing.'
}
$modules = & $php -m
if ($LASTEXITCODE -ne 0) { throw 'Gagal membaca ekstensi PHP.' }
foreach ($required in @('pdo_mysql', 'dom', 'mbstring', 'xmlwriter')) {
    if ($modules -notcontains $required) { throw "Ekstensi PHP belum aktif: $required" }
}
Write-Host 'Konfigurasi PHPUnit menunjuk kelas_renang_testing; guard di tests/TestCase.php tetap wajib aktif.'
Write-Host 'Memeriksa sintaks dan daftar route...'
& $php artisan route:list --json | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'Route Laravel gagal dimuat.' }
Write-Host 'Menjalankan PHPUnit. Tes mungkin menulis ke database TESTING dan storage fake.'
& $php artisan test
if ($LASTEXITCODE -ne 0) { throw 'PHPUnit gagal. Jangan deploy ke hosting.' }
Write-Host 'PHPUnit lulus. Staging dan audit konfigurasi hosting tetap wajib sebelum deployment.'
