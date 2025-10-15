<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DepreciationExport implements WithMultipleSheets
{
    protected Collection $assets;
    protected array $summary;

    public function __construct(Collection $assets, array $summary)
    {
        $this->assets = $assets;
        $this->summary = $summary;
    }

    public function sheets(): array
    {
        return [
            new DepreciationDetailsSheet($this->assets),
            new DepreciationSummarySheet($this->assets, $this->summary),
        ];
    }
}
