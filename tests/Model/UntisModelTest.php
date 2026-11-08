<?php

namespace Btinet\Test\Model;

use Btinet\Untis\Enum\Substitution\UntisField;
use Btinet\Untis\Enum\Substitution\UntisPair;
use Btinet\Untis\Model\UntisSubstitutionModel;
use Btinet\Untis\Service\UntisSubstitutionModelSplitter;
use PHPUnit\Framework\TestCase;

class UntisModelTest extends TestCase
{
    private const int CURRENT_LINE = 1006;
    private UntisSubstitutionModel $model;

    protected function setUp(): void
    {
        $this->model = new UntisSubstitutionModel();
    }

    public static function dataProvider(): iterable
    {
        $filePath = dirname(__DIR__, 2) . '/data/gpu014.txt';
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Testdaten-Datei nicht gefunden: {$filePath}");
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lineIndex = self::CURRENT_LINE;
        $testLine = $lines[$lineIndex -1] ?? null;

        if ($testLine === null) {
            throw new \RuntimeException("Die Datei {$filePath} ist leer oder hat nicht genügend Zeilen.");
        }
        $data = str_getcsv($testLine, ',', '"', "");
        yield 'Datensatz aus gpu014.txt (Zeile ' . ($lineIndex + 1) . ')' => [$data];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShowRawAndProcessedData(array $data): void
    {
        $this->model->setData($data);
        echo "\n" . str_repeat('=', 100) . "\n";
        echo "ZEILE VERARBEITET\n";
        echo str_repeat('=', 100) . "\n";
        $fields = [
            ['Vertretungsnummer', UntisField::Id, $this->model->getUntisId()],
            ['Datum', UntisField::Date, $this->model->getDate()?->format('Y-m-d')],
            ['Stunde', UntisField::CourseHour, $this->model->getCourseHour()],
            ['Absenznummer', UntisField::AbsenceId, $this->model->getAbsenceId()],
            ['Unterrichtsnummer', UntisField::CourseId, $this->model->getCourseId()],
            ['Absente Lehrkraft', UntisField::PlannedTeacher, $this->model->getPlannedTeacher()],
            ['Vertretende Lehrkraft', UntisField::UpdatedTeacher, $this->model->getUpdatedTeacher()],
            ['Fach', UntisField::PlannedSubject, $this->model->getPlannedSubject()],
            ['Statistikkennzeichen Fach', UntisField::PlannedSubjectComment, $this->model->getPlannedSubjectComment()],
            ['Vertretungsfach', UntisField::UpdatedSubject, $this->model->getUpdatedSubject()],
            ['Statistikkennzeichen Vertretungsfach', UntisField::UpdatedSubjectComment, $this->model->getUpdatedSubjectComment()],
            ['Raum', UntisField::PlannedRoom, $this->model->getPlannedRoom()],
            ['Vertretungsraum', UntisField::UpdatedRoom, $this->model->getUpdatedRoom()],
            ['Statistik-Kennzeichen', UntisField::Statistics, $this->model->getStatistics()],
            ['Klasse(n)', UntisField::Classes, implode(', ', $this->model->getClasses())],
            ['Absenzgrund', UntisField::AbsenceReason, $this->model->getAbsenceReason()],
            ['Text zur Vertretung', UntisField::Message, $this->model->getMessage()],
            ['Art (Bitfeld)', UntisField::Bit, count($this->model->getBits()) . ' Bits gesetzt'],
            ['Vertretungsklasse(n)', UntisField::UpdatedClasses, implode(', ', $this->model->getUpdatedClasses())],
            ['Vertretungsart', UntisField::Type, $this->model->getType()],
            ['Zeit der letzten Änderung', UntisField::UpdatedAt, $this->model->getUpdatedAt()?->format('Y-m-d H:i')],
            ['Vertretungen/Entfälle zählen', UntisField::Counts, implode(', ', $this->model->getCounts())],
        ];
        $maxLabel = 0;
        $maxRaw = 0;
        $maxProcessed = 0;

        foreach ($fields as [$label, $field, $processed]) {
            $raw = $data[$field->value] ?? '(fehlt)';

            $processedStr = match (true) {
                $processed === null => '(null)',
                is_array($processed) => implode(', ', $processed),
                $processed instanceof \BackedEnum => $processed->value,
                $processed instanceof \UnitEnum => $processed->name,
                is_bool($processed) => $processed ? 'true' : 'false',
                default => (string) $processed,
            };
            $maxLabel = max($maxLabel, mb_strlen($label));
            $maxRaw = max($maxRaw, mb_strlen($raw));
            $maxProcessed = max($maxProcessed, mb_strlen($processedStr));
        }

        foreach ($fields as [$label, $field, $processed]) {
            $raw = $data[$field->value] ?? '(fehlt)';

            $processedStr = match (true) {
                $processed === null => '(null)',
                is_array($processed) => implode(', ', $processed),
                $processed instanceof \BackedEnum => $processed->value,
                $processed instanceof \UnitEnum => $processed->name,
                is_bool($processed) => $processed ? 'true' : 'false',
                default => (string) $processed,
            };
            printf(
                "%-{$maxLabel}s | %-{$maxRaw}s | %s\n",
                $label,
                $raw,
                $processedStr
            );
        }

        echo str_repeat('=', 100) . "\n";

        $this->assertTrue(true);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testInterpreterLogicWithRealData(array $data): void
    {
        // $data ist ein array/eine Zeile aus der CSV
        $this->model->setData($data);
        $interpreter = $this->model->getSplitter();
        $entries = $interpreter->returnEntries();
        $pairType = $interpreter->getPairType();


            echo "\nRAW MODEL DATA:\n";
            echo "[" . $pairType->value . "]\n";
            echo "Plan: " . ($this->model->getPlannedTeacher() ?? 'null') . " \n";
            echo "Update: " . ($this->model->getUpdatedTeacher() ?? 'null') . " \n";
            echo "Klassen: " . implode(', ', $this->model->getClasses()) . " \n";
            echo "neue Klassen: " . implode(', ', $this->model->getUpdatedClasses()) . " \n";
            echo "Fach: " . ($this->model->getPlannedSubject() ?? 'null') . " \n";
            echo "neues Fach: " . ($this->model->getUpdatedSubject() ?? 'null') . " \n";
            echo "Raum: " . ($this->model->getPlannedRoom() ?? 'null') . " \n";
            echo "neuer Raum: " . ($this->model->getUpdatedRoom() ?? 'null') . " \n";
            echo "Infotext: " . ($this->model->getMessage() ?? 'null') . " \n";
            echo "Bits: " . implode(',', array_map(fn($item) => $item->getGermanLabel(), $this->model->getBits())) . "\n";
            echo "Counter: " . implode(',', $this->model->getCounts()) . " => ";
            echo "Entries: " . implode(', ', $entries) . "\n";

        echo "\n";
        echo "DISPLAY ENTRY:\n";
        echo "Neuer Lehrer: " . ($interpreter->getDisplayEntry()?->hasChangedTeacher() ? 'ja' : 'nein') . " \n";
        echo "Neue Klasse(n): " . ($interpreter->getDisplayEntry()?->hasChangedClasses() ? 'ja' : 'nein') . " \n";
        echo "Neues Fach: " . ($interpreter->getDisplayEntry()?->hasChangedSubject() ? 'ja' : 'nein') . " \n";
        echo "Neuer Raum: " . ($interpreter->getDisplayEntry()?->hasChangedRoom() ? 'ja' : 'nein') . " \n";
        echo "Hat Infotext: " . ($interpreter->getDisplayEntry()?->hasMessage() ? 'ja' : 'nein') . " \n";
        echo "Ist Ausfall: " . ($interpreter->getDisplayEntry()?->isCancelled() ? 'ja' : 'nein') . " \n";
        echo "Ist Vertretung: " . ($interpreter->getDisplayEntry()?->isSubstituted() ? 'ja' : 'nein') . " \n";
        echo "Ist nur umgezogen: " . ($interpreter->getDisplayEntry()?->isMoved() ? 'ja' : 'nein') . " \n";
        echo "geratener Typ: " . $interpreter->getDisplayEntry()?->getGuessedType() . " \n";

        echo "\n\n";

        echo "Anzahl: " . count($interpreter->getSubstitutionRows()) . " \n";
        foreach ($interpreter->getSubstitutionRows() as $entry) {
            echo "Eintrag:\n";
            print_r($entry);
            echo "\n\n";
        }

        // Regel A: Das Ergebnis-Array darf NIEMALS null enthalten
        $this->assertNotContains(null, $entries, 'Entries sollten keine null-Werte enthalten');

        // Regel B: Wenn Counter leer ist (NONE), darf kein Eintrag erzeugt werden
        if ($pairType === UntisPair::NONE) {
            $this->assertEmpty($entries, 'Bei leerem Counter sollten keine Entries erzeugt werden');
        }

        // Regel C: Wenn nur MINUS (FIRST), muss genau der geplante Lehrer drinstehen
        if ($pairType === UntisPair::FIRST) {
            $this->assertCount(1, $entries, 'FIRST sollte genau 1 Entry haben');
            $this->assertSame($this->model->getPlannedTeacher(), $entries[0], 'FIRST Entry muss der geplante Lehrer sein');
        }

        // Regel D: Wenn nur PLUS (LAST), muss der Vertreter (oder als Fallback der geplante) drinstehen
        if ($pairType === UntisPair::LAST) {
            $this->assertCount(1, $entries, 'LAST sollte genau 1 Entry haben');
            $expectedTeacher = $this->model->getUpdatedTeacher() ?? $this->model->getPlannedTeacher();
            $this->assertSame($expectedTeacher, $entries[0], 'LAST Entry muss der Vertreter oder (Fallback) der geplante Lehrer sein');
        }

        // Regel E: Wenn BEIDES (BOTH), müssen beide Lehrer drinstehen
        if ($pairType === UntisPair::BOTH) {
            $this->assertGreaterThanOrEqual(1, count($entries), 'BOTH sollte mindestens 1 Entry haben');
            $this->assertContains($this->model->getPlannedTeacher(), $entries, 'BOTH muss den geplanten Lehrer enthalten');
            $this->assertContains($this->model->getUpdatedTeacher(), $entries, 'BOTH muss den Vertreter enthalten, wenn dieser existiert');
        }
    }

}