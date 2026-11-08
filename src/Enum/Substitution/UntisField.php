<?php

namespace Btinet\Untis\Enum\Substitution;

enum UntisField: int
{
    case Id = 0;
    case Date = 1;
    case CourseHour = 2;
    case AbsenceId = 3;
    case CourseId = 4;
    case PlannedTeacher = 5;
    case UpdatedTeacher = 6;
    case PlannedSubject = 7;
    case PlannedSubjectComment = 8;
    case UpdatedSubject = 9;
    case UpdatedSubjectComment = 10;
    case PlannedRoom = 11;
    case UpdatedRoom = 12;
    case Statistics = 13;
    case Classes = 14;
    case AbsenceReason = 15;
    case Message = 16;
    case Bit = 17;
    case UpdatedClasses = 18;
    case Type = 19;
    case UpdatedAt = 20;
    case Counts = 21;
}
