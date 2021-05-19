<?php

namespace Foundation\Excel\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GiftCardCodes implements FromArray, WithHeadingRow
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                0 => 'codes',
                1 => 'is_used',
            ],
        ];
    }

}
