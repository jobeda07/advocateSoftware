<?php

namespace App\Enums;

enum PriorityEnum:string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function display()
    {
        return match ($this) {
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
        };
    }
}
