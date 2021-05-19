<?php

namespace Foundation\Commands\Reports;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateOrderReportsViewCommand
 * @package Foundation\Commands\Reports
 */
final class UpdateOrderReportsViewCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mv:order-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or Replace SQL View.';

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
        try {
            DB::statement("
                CREATE OR REPLACE VIEW orders_records
                AS
                select
                `transactions`.`id`, `transactions`.`transaction_id`, `transactions`.`payment_gateway_id`, `transactions`.`picked_by`, `transactions`.`user_id`,
                `transactions`.`created_at`, `transactions`.`is_delivered`, `transactions`.`product_names` as `product_name`, `transactions`.`status`, `picker`.`id` as `picker_id`,
                `customer`.`id` as `customer_id`,
                (select Cast(sum(case when delivery_status = '0' then 0 ELSE 1 end) AS UNSIGNED) from `orders` where `transaction_id` = `transactions`.`id`) as `order_is_delivered`,
                (select SUM(orders.quantity) As count_of_order from `orders` where `transaction_id` = `transactions`.`id`) as `count_of_order`,
                (select Cast(sum(case when order_type = '1' then quantity end) AS UNSIGNED) from `orders` where `transaction_id` = `transactions`.`id`) as `total_gift_card`,
                (select Cast(sum(case when order_type = '0' then quantity end) AS UNSIGNED) from `orders` where `transaction_id` = `transactions`.`id`) as `total_top_up`,
                CONCAT_WS(' ', customer.first_name, customer.middle_name, customer.last_name) AS customer_full_name,
                CONCAT_WS(' ', picker.first_name, picker.middle_name, picker.last_name) AS picker_full_name,
                (select count(*) from transaction_conversations where transactions.id = transaction_conversations.transaction_id and acknowledged_by_admin = 0) AS unread_conversations_by_admin_count from `transactions`
                left join `users` as `customer` on `customer`.`id` = `transactions`.`user_id`
                left join `users` as `picker` on `picker`.`id` = `transactions`.`picked_by`
                order by `transactions`.`created_at` desc
            ");
        } catch (\Illuminate\Database\QueryException $exception) {
            dd($exception->getMessage());
        }

        $this->info('The Reports is generated successfully. Thank you !');
    }

}
