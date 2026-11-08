<?php

namespace Btinet\Untis\Model;

use Btinet\Untis\Enum\Substitution\UntisType;
use DateTimeImmutable;

class UntisSubstitutionRow
{
    private ?string $untisId = null;
    private ?DateTimeImmutable $date = null;
    private ?int $courseHour = null;
    private ?string $subject = null;
    private ?string $subjectComment = null;
    private ?string $teacher = null;
    private ?array $classes = null;
    private ?string $room = null;
    private ?string $reason = null;
    private ?string $comment = null;
    private ?string $message = null;
    private ?array $bits = null;
    private ?UntisType $type = null;
    private ?array $count = null;
    private ?int $amount = null;

    public function getUntisId(): ?string
    {
        return $this->untisId;
    }

    public function setUntisId(?string $untisId): UntisSubstitutionRow
    {
        $this->untisId = $untisId;
        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?DateTimeImmutable $date): UntisSubstitutionRow
    {
        $this->date = $date;
        return $this;
    }

    public function getCourseHour(): ?int
    {
        return $this->courseHour;
    }

    public function setCourseHour(?int $courseHour): UntisSubstitutionRow
    {
        $this->courseHour = $courseHour;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): UntisSubstitutionRow
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubjectComment(): ?string
    {
        return $this->subjectComment;
    }

    public function setSubjectComment(?string $subjectComment): UntisSubstitutionRow
    {
        $this->subjectComment = $subjectComment;
        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): UntisSubstitutionRow
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getClasses(): ?array
    {
        return $this->classes;
    }

    public function setClasses(?array $classes): UntisSubstitutionRow
    {
        $this->classes = $classes;
        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): UntisSubstitutionRow
    {
        $this->room = $room;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): UntisSubstitutionRow
    {
        $this->reason = $reason;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): UntisSubstitutionRow
    {
        $this->comment = $comment;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): UntisSubstitutionRow
    {
        $this->message = $message;
        return $this;
    }

    public function getBits(): ?array
    {
        return $this->bits;
    }

    public function setBits(?array $bits): UntisSubstitutionRow
    {
        $this->bits = $bits;
        return $this;
    }

    public function getType(): ?UntisType
    {
        return $this->type;
    }

    public function setType(?UntisType $type): UntisSubstitutionRow
    {
        $this->type = $type;
        return $this;
    }

    public function getCount(): ?array
    {
        return $this->count;
    }

    public function setCount(?array $count): UntisSubstitutionRow
    {
        $this->count = $count;
        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): UntisSubstitutionRow
    {
        $this->amount = $amount;
        return $this;
    }
}