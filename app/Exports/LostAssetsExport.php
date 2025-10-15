<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LostAssetsExport implements WithMultipleSheets
{
    protected Collection $lostAssets;

    public function __construct(Collection $lostAssets)
    {
        $this->lostAssets = $lostAssets;
    }

    public function sheets(): array
    {
        return [
            new LostAssetsDetailsSheet($this->lostAssets),
            new LostAssetsSummarySheet($this->lostAssets),
        ];
    }
}
