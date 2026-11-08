<?php

namespace Btinet\Untis\Service;

use Btinet\Untis\Enum\Substitution\UntisPair;
use Btinet\Untis\Model\UntisSubstitutionDisplayEntry;
use Btinet\Untis\Model\UntisSubstitutionModel;
use Btinet\Untis\Model\UntisSubstitutionRow;

/**
 * Bietet Ausgabe der Daten für verschiedene Zwecke:
 *
 * 1. Ausgabe als Einzelzeile (z. B.: Vertretungsplan)
 * 2. Ausgabe als getrennte Datensätze (z. B.: interne Stundenbuchungen)
 */
class UntisSubstitutionModelSplitter
{
    private UntisSubstitutionModel $model;
    private bool $hasMinus = false;
    private bool $hasPlus = false;
    private UntisPair $pair;

    /**
     * @var array|null multiple data rows for internal time booking systems
     */
    private ?array $substitutionRows = [];

    /**
     * @var UntisSubstitutionDisplayEntry|null single data row for displaying on info screens
     */
    private ?UntisSubstitutionDisplayEntry $displayEntry = null;

    public function __construct(UntisSubstitutionModel $model)
    {
        $this->model = $model;
        $this->pair = UntisPair::NONE;

        $this->generateAmounts();
        $this->generatePairType();
        $this->generateSubstitutionRows();
        $this->generateSubstitutionDisplayEntry();
    }

    private function generateAmounts(): void
    {
        $counts = $this->model->getCounts();
        $this->hasMinus = in_array('-', $counts, true);
        $this->hasPlus = in_array('+', $counts, true);
    }

    private function generatePairType(): void
    {
        if ($this->hasMinus and $this->hasPlus) {
            $this->pair = UntisPair::BOTH;
        } elseif ($this->hasMinus) {
            $this->pair = UntisPair::FIRST;
        } elseif ($this->hasPlus) {
            $this->pair = UntisPair::LAST;
        }
    }

    private function generateSubstitutionDisplayEntry(): void
    {
        $model = $this->model;
        if (
            !empty($model->getPlannedSubject()) &&
            !empty($model->getPlannedTeacher()) &&
            !empty($model->getClasses()) &&
            !empty($model->getPlannedRoom())
        ) {
            $this->displayEntry = new UntisSubstitutionDisplayEntry(
                $model->getUntisId(),
                $model->getDate(),
                $model->getCourseHour(),
                $model->getPlannedSubject(),
                $model->getUpdatedSubject(),
                $model->getClasses(),
                $model->getUpdatedClasses(),
                $model->getPlannedTeacher(),
                $model->getUpdatedTeacher(),
                $model->getPlannedRoom(),
                $model->getUpdatedRoom(),
                $model->getMessage(),
                $model->getBits(),
            );
        }

    }

    private function generateSubstitutionRows(): void
    {

        $model = $this->model;
        $isSubstituted = ($model->getUpdatedTeacher() and $model->getPlannedTeacher() !== $model->getUpdatedTeacher());
        if ($model->getPlannedTeacher()) {
            $row = new UntisSubstitutionRow();

            // Daten setzen
            $row->setUntisId(
                ($isSubstituted)
                    ? $model->getUntisId() . "_orig"
                    : $model->getUntisId()
            );
            $row->setDate($model->getDate());
            $row->setCourseHour($model->getCourseHour());
            $row->setSubject($model->getPlannedSubject());
            $row->setSubjectComment($model->getPlannedSubjectComment());
            $row->setTeacher($model->getPlannedTeacher());
            $row->setClasses($model->getClasses());
            $row->setRoom($model->getPlannedRoom());
            $row->setReason(!$isSubstituted ? $model->getAbsenceReason(): "Ausfall (vertr. d. {$model->getUpdatedTeacher()})");
            $row->setMessage($model->getMessage());
            $row->setBits($model->getBits());
            $row->setType($model->getType());
            $row->setCount($model->getCounts());
            $row->setAmount($this->hasMinus ? -1 : 0);
            if (
                !empty($model->getPlannedSubject()) &&
                !empty($model->getPlannedTeacher()) &&
                !empty($model->getClasses()) &&
                !empty($model->getPlannedRoom())
            ) {
                $this->substitutionRows[] = $row;
            }
        }
        if ($isSubstituted) {
            $row = new UntisSubstitutionRow();

            // Daten setzen
            $row->setUntisId($model->getUntisId() . "_vert");
            $row->setDate($model->getDate());
            $row->setCourseHour($model->getCourseHour());
            $row->setSubject($model->getUpdatedSubject() ?: $model->getPlannedSubject());
            $row->setSubjectComment($model->getUpdatedSubjectComment() ?: $model->getPlannedSubjectComment());
            $row->setTeacher($model->getUpdatedTeacher());
            $row->setClasses($model->getClasses());
            $row->setRoom($model->getUpdatedRoom() ?: $model->getPlannedRoom());
            $row->setReason($model->getAbsenceReason()?: "Vertretung für {$model->getPlannedTeacher()}");
            $row->setMessage($model->getMessage());
            $row->setBits($model->getBits());
            $row->setType($model->getType());
            $row->setCount($model->getCounts());
            $row->setAmount($this->hasPlus ? 1 : 0);

            $this->substitutionRows[] = $row;
        }
    }

    public function getPairType(): UntisPair
    {
        return $this->pair;
    }

    public function hasPlannedTeacher(): bool
    {
        return $this->model->getPlannedTeacher() !== null;
    }

    public function hasUpdatedTeacher(): bool
    {
        return $this->model->getUpdatedTeacher() !== null;
    }

    public function isSameTeacher(): bool
    {
        return $this->hasPlannedTeacher() && $this->hasUpdatedTeacher() &&
            $this->model->getPlannedTeacher() === $this->model->getUpdatedTeacher();
    }

    /**
     * @return UntisSubstitutionRow[]|null
     */
    public function getSubstitutionRows(): ?array
    {
        return $this->substitutionRows;
    }

    public function getDisplayEntry(): ?UntisSubstitutionDisplayEntry
    {
        return $this->displayEntry;
    }

    /**
     * @return array<string> Liste der betroffenen Lehrkräfte
     */
    public function returnEntries(): array
    {
        $data = [];

        if ($this->pair === UntisPair::FIRST || $this->pair === UntisPair::BOTH) {
            $data[] = $this->model->getPlannedTeacher();
        }
        if ($this->pair === UntisPair::LAST || $this->pair === UntisPair::BOTH) {
            $teacherForPlus = $this->model->getUpdatedTeacher() ?? $this->model->getPlannedTeacher();
            $data[] = $teacherForPlus;
        }
        return array_values(array_filter($data, fn($teacher) => $teacher !== null));
    }

}