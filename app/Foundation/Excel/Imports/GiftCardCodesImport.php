<?php

namespace Foundation\Excel\Imports;

use Illuminate\Support\Collection;
use Foundation\Models\GiftCardsCode;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GiftCardCodesImport implements ToCollection, WithHeadingRow
{

    protected $id;

    public $response;

    function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        if ($rows->count() > 0) {
            $keys = array_keys($rows->first()->toArray());
            $difference = array_diff(['codes', 'is_used'], $keys);
            if (count($difference) == 0) {
                foreach ($rows as $row) {
                    GiftCardsCode::updateOrCreate([
                        'codes' => $row['codes'],
                    ], [
                        'gift_cards_id' => $this->id,
                        'is_used' => $row['is_used'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                $this->response['error'] = true;
            }
        } else {
            $this->response['error'] = true;
        }
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
