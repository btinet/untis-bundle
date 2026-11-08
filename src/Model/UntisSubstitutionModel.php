<?php

namespace Btinet\Untis\Model;

use Btinet\Untis\Enum\Substitution\UntisField;
use Btinet\Untis\Enum\Substitution\UntisType;
use Btinet\Untis\Service\EncodingHelper;
use Btinet\Untis\Service\UntisSubstitutionModelSplitter;
use InvalidArgumentException;

class UntisSubstitutionModel
{
    private readonly EncodingHelper $encoder;

    private ?UntisSubstitutionModelSplitter $splitter = null;
    private ?array $data = [];
    private ?int $untisId = null;
    private ?\DateTimeImmutable $date = null;
    private int $courseHour = 0;
    private ?int $absenceId = null;
    private ?int $courseId = null;
    private ?string $plannedTeacher = null;
    private ?string $updatedTeacher = null;
    private ?string $plannedSubject = null;
    private ?string $plannedSubjectComment = null;
    private ?string $updatedSubject = null;
    private ?string $updatedSubjectComment = null;
    private ?string $plannedRoom = null;
    private ?string $updatedRoom = null;
    private ?string $statistics = null;
    private array $classes = [];
    private ?string $absenceReason = null;
    private ?string $message = null;
    private array $bits = [];
    private array $updatedClasses = [];
    private ?UntisType $type = null;
    private ?\DateTimeImmutable $updatedAt = null;
    private array $counts = [];
    public function __construct()
    {
        $this->encoder = new EncodingHelper();
    }

    public function setData(array $data): self
    {
        if (count($data) !== count(UntisField::cases())) {
            throw new InvalidArgumentException(sprintf(
                'Invalid data array provided. Expected %d columns, got %d. Data: "%s"',
                count(UntisField::cases()),
                count($data),
                json_encode($data)
            ));
        }

        // Daten setzen
        $this->data = $data;

        // Attribute setzen
        $this->untisId = $this->encoder->strToId($this->get(UntisField::Id));
        $this->date = $this->encoder->strToDateTimeImmutable($this->get(UntisField::Date));
        $this->courseHour = $this->encoder->strToPositiveInt($this->get(UntisField::CourseHour));
        $this->absenceId = $this->encoder->strToId($this->get(UntisField::AbsenceId));
        $this->absenceReason = $this->encoder->strToStr($this->get(UntisField::AbsenceReason));
        $this->courseId = $this->encoder->strToId($this->get(UntisField::CourseId));
        $this->classes = $this->encoder->strToStr($this->get(UntisField::Classes), true);
        $this->updatedClasses = $this->encoder->strToStr($this->get(UntisField::UpdatedClasses), true);
        $this->plannedTeacher = $this->encoder->strToStr($this->get(UntisField::PlannedTeacher));
        $this->updatedTeacher = $this->encoder->strToStr($this->get(UntisField::UpdatedTeacher));
        $this->plannedSubject = $this->encoder->strToStr($this->get(UntisField::PlannedSubject));
        $this->plannedSubjectComment = $this->encoder->strToStr($this->get(UntisField::PlannedSubjectComment));
        $this->updatedSubject = $this->encoder->strToStr($this->get(UntisField::UpdatedSubject));
        $this->updatedSubjectComment = $this->encoder->strToStr($this->get(UntisField::UpdatedSubjectComment));
        $this->plannedRoom = $this->encoder->strToStr($this->get(UntisField::PlannedRoom));
        $this->updatedRoom = $this->encoder->strToStr($this->get(UntisField::UpdatedRoom));
        $this->statistics = $this->encoder->strToStr($this->get(UntisField::Statistics));
        $this->type = $this->encoder->strToType($this->get(UntisField::Type));
        $this->bits = $this->encoder->strToBitArray($this->get(UntisField::Bit));
        $this->message = $this->encoder->strToStr($this->get(UntisField::Message));
        $this->updatedAt = $this->encoder->strToDateTimeImmutable($this->get(UntisField::UpdatedAt), "YmdHi");
        $this->counts = $this->encoder->strToStr($this->get(UntisField::Counts), true);

        // Splitter initiieren
        $this->splitter = new UntisSubstitutionModelSplitter($this);

        return $this;
    }

    private function get(UntisField $field): ?string
    {
        return $this->data[$field->value] ?? null;
    }

    public function getSplitter(): ?UntisSubstitutionModelSplitter
    {
        return $this->splitter;
    }

    public function getUntisId(): ?int
    {
        return $this->untisId;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function getCourseHour(): int
    {
        return $this->courseHour;
    }

    public function getAbsenceId(): ?int
    {
        return $this->absenceId;
    }

    public function getCourseId(): ?int
    {
        return $this->courseId;
    }

    public function getPlannedTeacher(): ?string
    {
        return $this->plannedTeacher;
    }

    public function getUpdatedTeacher(): ?string
    {
        return $this->updatedTeacher;
    }

    public function getPlannedSubject(): ?string
    {
        return $this->plannedSubject;
    }

    public function getPlannedSubjectComment(): ?string
    {
        return $this->plannedSubjectComment;
    }

    public function getUpdatedSubject(): ?string
    {
        return $this->updatedSubject;
    }

    public function getUpdatedSubjectComment(): ?string
    {
        return $this->updatedSubjectComment;
    }

    public function getPlannedRoom(): ?string
    {
        return $this->plannedRoom;
    }

    public function getUpdatedRoom(): ?string
    {
        return $this->updatedRoom;
    }

    public function getStatistics(): ?string
    {
        return $this->statistics;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getAbsenceReason(): ?string
    {
        return $this->absenceReason;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getBits(): array
    {
        return $this->bits;
    }

    public function getUpdatedClasses(): array
    {
        return $this->updatedClasses;
    }

    public function getType(): ?UntisType
    {
        return $this->type;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCounts(): array
    {
        return $this->counts;
    }

}