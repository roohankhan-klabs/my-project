<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class FilesOverview extends ChartWidget
{
    protected ?string $heading = 'Files Overview';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
