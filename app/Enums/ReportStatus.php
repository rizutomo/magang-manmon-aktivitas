<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Belum = 'Belum Diserahkan';
    case Diserahkan = 'Diserahkan';
    case Diterima = 'Diterima';
    case Pending = 'Pending';
}