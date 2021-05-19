<?php


namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\Meta;
use Foundation\Lib\Statistics\Chart;
use Foundation\Models\Order;
use Foundation\Services\AccountingService;
use Foundation\Services\ExpenseService;
use Foundation\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class AccountingAction
 * @package App\Http\Controllers\Admin\Actions
 */
class AccountingAction
{
    /**
     * @var AccountingService
     */
    private $accountingService;
    /**
     * @var ExpenseService
     */
    private $expenseService;

    private static $colours = [
        '#1ab366',
        '#1abaeb',
        '#1ab394',
        '#BABABA',
    ];
    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * AccountingAction constructor.
     * @param AccountingService $accountingService
     * @param ExpenseService $expenseService
     * @param SettingService $settingService
     */
    public function __construct(AccountingService $accountingService, ExpenseService $expenseService, SettingService $settingService)
    {
        $this->accountingService = $accountingService;
        $this->expenseService = $expenseService;
        $this->settingService = $settingService;
    }

    /**
     * Get Accounting Summary
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function summary(Request $request)
    {
        $data = [];
        if($request->ajax()){
            $filter['startDate'] = $request->get('startDate');
            $filter['endDate'] = $request->get('endDate');
            $filter['dailyDate'] = $request->get('dailyDate');
            $share = $request->get('profitShare');

            $data['sales'] = $this->accountingService->getStats($filter);
            $data['expenses'] = $this->expenseService->getStats($filter);

            $data['profit'] = $this->getProfits($data['sales'], $data['expenses'], $share);

            $data['expenses-by-type'] = $this->getExpenseStatistics($data['expenses']['expense']);
            $data['income-by-type'] = $this->getExpenseStatistics($data['expenses']['income']);

            return response()->json($data);
        }

        return view('admin.accounting.summary', compact('data'));
    }

    /**
     * Get Chart
     *
     * @param mixed $data
     * @return object
     */
    private function getExpenseStatistics($data)
    {
        $columns = [];
        $colours = [];
        $i = 0;
        foreach ($data as $key => $value){
            if(strtoupper($key) != 'TOTAL'){
                array_push($columns, [ ucfirst($key), $value ]);
                array_push($colours, [ $key =>  self::$colours[$i]]);
            }
            $i++;
        }
        return Chart::getPieChart($columns, $colours);
    }

    /**
     * Get Net Profit Data
     *
     * @param $sales
     * @param $expenses
     * @param $share
     * @return array
     */
    private function getProfits($sales, $expenses, $share)
    {
        $data = [];

        $netProfit = $sales['profit_amt'] + $expenses['net'];

        $data['net'] = nrp($netProfit);
        $data['share'] = nrp(($share ?? Meta::get('profit_share_percent') ?? 0) / 100 * $netProfit);

        return $data;
    }
}
