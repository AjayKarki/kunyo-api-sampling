<?php

namespace Foundation\Commands\Payment;

use Illuminate\Console\Command;
use Modules\Payment\Libs\Payment;
use Symfony\Component\Process\Process;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Foundation\Mixins\UpdatePaymentStatus as PaymentStatus;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class UpdatePaymentStatus
 * @package Foundation\Commands\Payment
 */
final class UpdatePaymentStatus extends Command
{

    use PaymentStatus;

    protected $signature = 'payment:update-status';

    protected $description = 'Update the payment status according to the gateway';

    protected $process;

    public function __construct()
    {
        parent::__construct();

        $this->process = new Process([
            $this->check(),
        ]);
    }

    public function handle()
    {
        try {
            $this->process->mustRun();
        } catch (ProcessFailedException $exception) {
            write_log($exception->getMessage(), 'payment-status-error');
        }
    }

    private function check()
    {
        # Getting the log as in strings
        try {
            $collectionOfLastUpdatedString = \File::get(storage_path('logs/payment-status.log'));
        } catch (FileNotFoundException $exception) {
            write_log('Hello World');
            write_log('Started ...');

            exit();
        }

        # Clearing the log for the next one
        if (function_exists('exec')) {
            exec("truncate -s 0 " . storage_path('logs/payment-status.log'));
        }

        # Filter the \n from the string
        $collectionOfLastUpdatedString = trim(preg_replace('/\s\s+/', ' ', $collectionOfLastUpdatedString));

        # Exploding the comma separated string to array and filter the null/empty values
        $collectionOfLastUpdatedArr = array_filter(explode(',',  $collectionOfLastUpdatedString));

        # Getting the last transaction id
        $lastUpdatedTransactionId = end($collectionOfLastUpdatedArr);

        # If is not null or empty get the
        if ($lastUpdatedTransactionId) {
            $lastTransactionId = app('db')->table('transactions')
                ->where('id', $lastUpdatedTransactionId)
                ->value('id');
        }

        $query = \Modules\Payment\Payment::query();

        if (isset($lastTransactionId)) {
            $query = $query->where('id','>=', $lastTransactionId);
        }

        $query->orderBy('created_at', 'ASC')
            ->take(10)
            ->cursor()->each(function ($transaction) {
                write_log($transaction->id);
                $response = $this->callRespectiveGateway($transaction, $transaction->payment_gateway_id);

                if (is_null($response)) {
                    write_log($transaction->id .' failed to check the status for ', 'payment-status-error');
                } else {
                    if ($transaction) {

                        if ($response->status) {
                            $transaction->update([
                                'status' => Payment::PAYMENT_STATUS_DELIVERED,
                            ]);
                        }

                        if ($response->response) {
                            $transaction->update([
                                'metas' => array_merge( (array) $transaction->metas, (array) $response->response),
                            ]);
                        }

                    }
                }
            });
    }

}
