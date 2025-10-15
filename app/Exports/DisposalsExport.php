<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DisposalsExport implements WithMultipleSheets
{
    protected Collection $disposals;

    public function __construct(Collection $disposals)
    {
        $this->disposals = $disposals;
    }

    public function sheets(): array
    {
        return [
            new DisposalDetailsSheet($this->disposals),
            new DisposalSummarySheet($this->disposals),
        ];
    }
}


