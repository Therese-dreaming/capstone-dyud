<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;

class AssetImportTemplate implements WithMultipleSheets, WithEvents
{
    public function sheets(): array
    {
        return [
            new AssetImportInstructionsSheet(), // First sheet = default tab
            new AssetImportTemplateSheet(),
            new AssetImportReferenceSheet(),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function(BeforeWriting $event) {
                // Set the first sheet (Instructions) as the active sheet when file opens
                $event->writer->getDelegate()->setActiveSheetIndex(0);
            }
        ];
    }
}
