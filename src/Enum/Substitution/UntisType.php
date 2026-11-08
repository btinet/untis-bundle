<?php

namespace Btinet\Untis\Enum\Substitution;

enum UntisType : string
{
    case ShiftedTo             = 'T';  // verlegt
    case ShiftedFrom           = 'F';  // verlegt von
    case Exchange              = 'W';  // Tausch
    case Supervision           = 'S';  // Betreuung
    case SpecialOccurrence     = 'A';  // Sondereinsatz
    case Cancellation          = 'C';  // Entfall
    case Exemption             = 'L';  // Freisetzung
    case PartialExchange       = 'P';  // Teilvertretung
    case RoomExchange          = 'R';  // Raumvertretung
    case BreakSupervision      = 'B';  // Pausenaufsichtsvertretung
    case TeacherExchange       = '~';  // Lehrertausch
    case Exam                  = 'E';  // Klausur

    /**
     * Gibt die deutsche Bezeichnung für die UI / Doku zurück.
     */
    public function getGermanLabel(): string
    {
        return match ($this) {
            self::ShiftedTo         => 'Verlegt',
            self::ShiftedFrom       => 'Verlegt von',
            self::Exchange          => 'Tausch',
            self::Supervision       => 'Betreuung',
            self::SpecialOccurrence => 'Sondereinsatz',
            self::Cancellation      => 'Ausfall',
            self::Exemption         => 'Freisetzung',
            self::PartialExchange   => 'Teilvertretung',
            self::RoomExchange      => 'Raumvertretung',
            self::BreakSupervision  => 'Pausenaufsichtsvertretung',
            self::TeacherExchange   => 'Lehrertausch',
            self::Exam              => 'Klausur',
        };
    }

    /**
     * Parst ein rohes Zeichen aus der GPU014.TXT sicher in das Enum.
     * Trimmt automatisch Leerzeichen, falls das Feld in der Datei mit Spaces gefüllt ist.
     */
    public static function tryFromChar(?string $char): ?self
    {
        return self::tryFrom(trim($char));
    }
}
