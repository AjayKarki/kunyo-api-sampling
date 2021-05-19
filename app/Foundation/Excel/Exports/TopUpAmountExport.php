<?php

namespace Foundation\Excel\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class TopUpAmountExport implements FromArray
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                0 => 'title',
                1 => 'price',
                2 => 'status',
            ],
        ];
    }
}
