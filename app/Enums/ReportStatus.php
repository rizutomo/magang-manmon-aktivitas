<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Diserahkan = 'Diserahkan';
    case Diterima = 'Diterima';
    case Dikembalikan = 'Dikembalikan';
}