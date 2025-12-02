<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case READY_FOR_EVALUATION = 'ready_for_evaluation';
    case COMPLETED = 'completed';
}
