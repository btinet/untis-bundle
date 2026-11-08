<?php

namespace Btinet\Untis\Enum\Substitution;

enum UntisPair: string
{
    case FIRST = "first";
    case LAST = "last";
    case BOTH = "both";
    case NONE  = "none";
}