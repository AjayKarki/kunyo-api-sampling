<?php

namespace Foundation\Excel\Imports;

use Foundation\Models\TopUpAmount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Class TopUpAmountImport
 * @package Foundation\Excel\Imports
 */
class TopUpAmountImport implements ToCollection, WithStartRow, WithHeadingRow
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
            $difference = array_diff(['title', 'price', 'status'], $keys);
            if (count($difference) == 0) {
                foreach ($rows as $row) {
                    TopUpAmount::updateOrCreate([
                        'title' => $row['title'],
                    ], [
                        'game_top_ups_id' => $this->id,
                        'price' => $row['price'],
                        'status' => $row['status'] ?? 0,
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
     * @inheritDoc
     */
    public function startRow(): int
    {
        return 2;
    }
}
