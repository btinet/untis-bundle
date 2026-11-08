<?php

require_once "vendor/autoload.php";

use Btinet\Untis\Model\UntisSubstitutionModel;
use Btinet\Untis\Service\EncodingHelper;

// 2. Konfiguration
$filePath = __DIR__ . '/data/gpu014.txt';
$model = new UntisSubstitutionModel();

// 3. Datei öffnen
$handle = fopen($filePath, 'r');
if ($handle === false) {
    die("Fehler: Datei konnte nicht geöffnet werden: {$filePath}\n");
}

echo "Starte Import von: {$filePath}\n";
echo str_repeat('-', 50) . "\n";

$lineNumber = 0;
$successCount = 0;
$errorCount = 0;

// 4. Zeile für Zeile verarbeiten (speicherschonend, auch bei großen Dateien)
while (($line = fgets($handle)) !== false) {
    $lineNumber++;
    $line = trim($line);

    // Leere Zeilen überspringen
    if ($line === '') {
        continue;
    }

    // CSV parsen (Untis GPU ist standardmäßig Semikolon-getrennt)
    $data = str_getcsv($line, ',');

    try {
        // Hier passiert die Magie: Validierung und Typ-Konvertierung
        $model->setData($data);

        $successCount++;

        // Kurzes, positives Feedback (kann später entfernt oder durch Logging ersetzt werden)
        echo "✓ Zeile {$lineNumber}: ID {$model->getUntisId()}, "
            . "Datum: " . ($model->getDate()?->format('Y-m-d') ?? 'kein Datum') . ", "
            . "Klassen: " . implode(', ', $model->getClasses()) . "\n";

    } catch (\InvalidArgumentException $e) {
        $errorCount++;
        // Bei kaputten Zeilen sofort sehen, was schiefging
        echo "✗ Zeile {$lineNumber} FEHLER: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

// 5. Zusammenfassung
echo str_repeat('-', 50) . "\n";
echo "Import abgeschlossen!\n";
echo "Verarbeitete Zeilen: {$lineNumber}\n";
echo "Erfolgreich: {$successCount}\n";
echo "Fehler: {$errorCount}\n";