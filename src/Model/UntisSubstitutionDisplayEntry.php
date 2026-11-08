<?php

namespace Btinet\Untis\Model;

use Btinet\Untis\Enum\Substitution\UntisBit;
use DateTimeImmutable;

class UntisSubstitutionDisplayEntry
{
    // Input Data
    private ?int $untisId = null;
    private ?DateTimeImmutable $date = null;
    private ?int $courseHour = null;
    private ?string $plannedSubject = null;
    private ?string $updatedSubject = null;
    private ?array $plannedClasses = null;
    private ?array $updatedClasses = null;
    private ?string $plannedTeacher = null;
    private ?string $updatedTeacher = null;
    private ?string $plannedRoom = null;
    private ?string $updatedRoom = null;
    private ?string $message = null;
    private ?array $bits = null;

    // Output Data

    /**
     * @param int|null $untisId
     * @param DateTimeImmutable|null $date
     * @param int|null $courseHour
     * @param string|null $plannedSubject
     * @param string|null $updatedSubject
     * @param array|null $plannedClasses
     * @param array|null $updatedClasses
     * @param string|null $plannedTeacher
     * @param string|null $updatedTeacher
     * @param string|null $plannedRoom
     * @param string|null $updatedRoom
     * @param string|null $message
     * @param array|null $bits
     */
    public function __construct(
        ?int               $untisId,
        ?DateTimeImmutable $date,
        ?int               $courseHour,
        ?string            $plannedSubject,
        ?string            $updatedSubject,
        ?array             $plannedClasses,
        ?array             $updatedClasses,
        ?string            $plannedTeacher,
        ?string            $updatedTeacher,
        ?string            $plannedRoom,
        ?string            $updatedRoom,
        ?string            $message,
        ?array             $bits
    )
    {
        $this->untisId = $untisId;
        $this->date = $date;
        $this->courseHour = $courseHour;
        $this->plannedSubject = $plannedSubject;
        $this->updatedSubject = $updatedSubject;
        $this->plannedClasses = $plannedClasses;
        $this->updatedClasses = $updatedClasses;
        $this->plannedTeacher = $plannedTeacher;
        $this->updatedTeacher = $updatedTeacher;
        $this->plannedRoom = $plannedRoom;
        $this->updatedRoom = $updatedRoom;
        $this->message = $message;
        $this->bits = $bits;
    }

    public function hasChangedSubject(): bool
    {
        return $this->plannedSubject !== $this->updatedSubject && !empty($this->updatedSubject);
    }

    public function hasChangedClasses(): bool
    {
        return $this->plannedClasses !== $this->updatedClasses && !empty($this->updatedClasses);
    }

    public function hasChangedTeacher(): bool
    {
        return $this->plannedTeacher !== $this->updatedTeacher && !empty($this->updatedTeacher);
    }

    public function hasChangedRoom(): bool
    {
        return $this->plannedRoom !== $this->updatedRoom && !empty($this->updatedRoom);
    }

    public function isCancelled(): bool
    {
        return (in_array(UntisBit::Cancellation, $this->bits, true) ||
                in_array(UntisBit::Release, $this->bits, true))
            && !$this->hasChangedTeacher();
    }

    public function isSubstituted(): bool
    {
        return !$this->isCancelled() && $this->hasChangedTeacher();
    }

    public function isMoved(): bool
    {
        return in_array(UntisBit::RoomExchange, $this->bits, true) ||
            (!$this->isCancelled() && !$this->isSubstituted() && $this->hasChangedRoom());
    }

    public function hasMessage(): bool
    {
        return !empty($this->message);
    }

    public function getGuessedType(): string
    {
        return match(true) {
            $this->isCancelled() => 'untis-cancelled',
            $this->isSubstituted() => 'untis-substituted',
            $this->isMoved() => 'untis-moved',
        };
    }

}