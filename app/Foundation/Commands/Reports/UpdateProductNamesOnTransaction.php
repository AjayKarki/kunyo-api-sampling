<?php

namespace Foundation\Commands\Reports;

use Illuminate\Console\Command;
use Foundation\Lib\Product\ProductName;
use Modules\Payment\Libs\Payment;

/**
 * Class UpdateProductNamesOnTransaction
 * @package Foundation\Commands\Reports
 */
class UpdateProductNamesOnTransaction extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product name on transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app('db')->table('transactions')
            ->orderBy('created_at', 'DESC')
            ->whereNull('product_names')
            ->where('status', Payment::PAYMENT_STATUS_DELIVERED)
            ->limit(5)
            ->chunk(env('PRODUCT_NAMES_CHUNK', 100), function($transactions) {
                foreach ($transactions as $transaction)
                {
                    if (is_null($transaction->product_names)) {
                        $productName = ProductName::generate($transaction->id);

                        if ($productName) {
                            app('db')
                                ->table('transactions')
                                ->where('id', $transaction->id)
                                ->update([
                                    'product_names' => $productName,
                                ]);
                        }
                    }
                }
            });

        $this->info('The products names are successfully updated. Thank you !');
        $this->info('Total Product Name Inserted : '. app('db')->table('transactions')
                ->orderBy('created_at', 'DESC')
                ->whereNotNull('product_names')
                ->where('status', Payment::PAYMENT_STATUS_DELIVERED)->count());
        $this->info('Total Remaining : '. app('db')->table('transactions')
                ->orderBy('created_at', 'DESC')
                ->whereNull('product_names')
                ->where('status', Payment::PAYMENT_STATUS_DELIVERED)->count());
    }

}
