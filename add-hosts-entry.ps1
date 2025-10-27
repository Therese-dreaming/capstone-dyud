# PowerShell script to add pcc-inventory.local to hosts file
# Run this script as Administrator

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$entries = @(
    "127.0.0.1 pcc-inventory.local",
    "127.0.0.1 www.pcc-inventory.local"
)

Write-Host "Adding entries to hosts file..." -ForegroundColor Green

foreach ($entry in $entries) {
    $exists = Select-String -Path $hostsPath -Pattern $entry -Quiet
    if (-not $exists) {
        Add-Content -Path $hostsPath -Value $entry
        Write-Host "Added: $entry" -ForegroundColor Cyan
    } else {
        Write-Host "Already exists: $entry" -ForegroundColor Yellow
    }
}

Write-Host "`nHosts file updated successfully!" -ForegroundColor Green
Write-Host "You can now access your site at: https://pcc-inventory.local" -ForegroundColor Cyan
