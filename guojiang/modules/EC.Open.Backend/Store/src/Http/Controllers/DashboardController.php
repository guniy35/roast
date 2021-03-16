<?php
namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\EC\Open\Backend\Store\Model\Refund;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\OrderRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
class DashboardController extends Controller
{

    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    public function dashboard()
    {
        $s_now = Carbon::now();
        $s_time = $s_now->startOfDay();

        $e_now = Carbon::now();
        $e_time = $e_now->endOfDay();

        $time = [];
        $where = [];
        $pay_time = [];

        $where['status'] = ['>', 0];
        $where['submit_time'] = ['<=', $e_time];
        $time['submit_time'] = ['>=', $s_time];
        $orders = $this->orderRepository->getExportOrdersData($where, 0, $time, $pay_time);

        $todayCount = $orders->count(); //今日订单数

        $pay_orders = $orders->where('status', 2);  //已付款订单

        $paid_orders = $orders->where('pay_status', 1);
        $total = $paid_orders->sum('total'); //已付款金额

        $countUsers = $paid_orders->count(); //已付款订单数

        $per_total = 0; //客单价
        if ($countUsers > 0) {
            $per_total = number_format($total / $countUsers, 2);
        }

        $paidCount = $pay_orders->count(); //待发货订单

        $wait_pay_orders = $orders->where('status', 1);  //待付款订单
        $countWaitPayOrders = $wait_pay_orders->count(); //待付款订单数
        $waitPayTotal = $wait_pay_orders->sum('total'); //待付款金额

        $refund = Refund::where('created_at', '>=', $s_time)->where('created_at', '<=', $e_time)->count();

        /*7天数据*/
        $sevStartTime = Carbon::now()->subDays(7)->startOfDay();
        $sevEndTime = Carbon::now()->subDays(1)->endOfDay();
        $where['submit_time'] = ['<=', $sevEndTime];
        $time['submit_time'] = ['>=', $sevStartTime];
        $sevOrders = $this->orderRepository->getExportOrdersData($where, 0, $time, $pay_time);

        $countSevOrders = $sevOrders->count(); //7天订单数
        $sevTotal = $sevOrders->where('pay_status', 1)->sum('total');  //7天已付款订单金额
        $sevTotal = number_format($sevTotal, 2, '.', '');

        /*7天趋势*/
        $sevenDays = '"' .
            Carbon::now()->subDays(7)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(6)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(5)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(4)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(3)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(2)->format('Y-m-d') . '","' .
            Carbon::now()->subDays(1)->format('Y-m-d') . '"';
        $sevenDaysCount = $sevOrders->where('submit_time', '<', Carbon::now()->subDays(6)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(6)->startOfDay())->where('submit_time', '<', Carbon::now()->subDays(5)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(5)->startOfDay())->where('submit_time', '<', Carbon::now()->subDays(4)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(4)->startOfDay())->where('submit_time', '<', Carbon::now()->subDays(3)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(3)->startOfDay())->where('submit_time', '<', Carbon::now()->subDays(2)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(2)->startOfDay())->where('submit_time', '<', Carbon::now()->subDays(1)->startOfDay())->count() . ',' .
            $sevOrders->where('submit_time', '>=', Carbon::now()->subDays(1)->startOfDay())->where('submit_time', '<', Carbon::now()->startOfDay())->count();


        return LaravelAdmin::content(function (Content $content) use (
            $total, $per_total, $paidCount, $countWaitPayOrders,
            $countUsers, $refund, $todayCount, $countSevOrders, $sevTotal,
            $waitPayTotal, $sevenDays, $sevenDaysCount
        ) {

            $content->header('商城概括');

            $content->breadcrumb(
                ['text' => '商城概括', 'url' => 'store/dashboard', 'no-pjax' => 1]
            );

            $content->body(view('store-backend::dashboard.index', compact('total', 'per_total', 'paidCount', 'countWaitPayOrders',
                'countUsers', 'refund', 'todayCount', 'countSevOrders', 'sevTotal', 'waitPayTotal', 'sevenDays', 'sevenDaysCount')));
        });
    }

    public function getMonthData()
    {
        /*最近12个月数据*/
        $date = array_reverse($this->getLastMonthArea(Carbon::now()->year, Carbon::now()->month, 12, 1));
        $monthList = [];
        $monthTotal = [];
        $checkMonthArray = [];

        foreach ($date as $key => $item) {
            $monthList[] = $item['currentMonth'];
            $checkMonthArray[] = $item['currentMonth'];
        }

        $startMonth = $date[0]['startMonth'];
        $endMonth = $date[11]['endMonth'];

        $orderTable = $prefix = config('ibrand.app.database.prefix', 'ibrand_') . 'order';

        $monthData = DB::table($orderTable)
            ->select(DB::raw('substr(submit_time, 1, 7) as yearmonth, SUM(total) as total'))
            ->where('pay_status', 1)
            ->whereBetween('submit_time', [$startMonth, $endMonth])
            ->groupBy('yearmonth')
            ->get();

        foreach ($checkMonthArray as $key => $item) {
            $filter = $monthData->filter(function ($value) use ($item) {
                return $value->yearmonth == $item;
            });
            if (count($filter) == 0) {
                $monthTotal[] = 0;
            } else {
                $monthTotal[] = $filter->first()->total / 1000000;
            }
        }

        /*最近12个月用户数据*/
        $userTable = config('ibrand.app.database.prefix', 'ibrand_') . 'user';
        $monthUserData = DB::table($userTable)
            ->select(DB::raw('substr(created_at, 1, 7) as yearmonth, COUNT(*) as total'))
            ->whereBetween('created_at', [$startMonth, $endMonth])
            ->groupBy('yearmonth')
            ->get();

        $monthUserTotal = [];
        foreach ($checkMonthArray as $key => $item) {
            $filter = $monthUserData->filter(function ($value) use ($item) {
                return $value->yearmonth == $item;
            });
            if (count($filter) == 0) {
                $monthUserTotal[] = 0;
            } else {
                $monthUserTotal[] = $filter->first()->total;
            }
        }

        /*最近30天数据*/
        $daysData = $this->getLastDayArea();
        $dayList = $daysData[0];
        $dayTime = $daysData[1];

        $startDay = $dayTime[0] . ' 00:00:00';
        $endDay = $dayTime[29] . ' 23:59:59';
        $daysOrderData = DB::table($orderTable)
            ->select(DB::raw('substr(submit_time, 1, 10) as monthDay, SUM(total) as total'))
            ->where('pay_status', 1)
            ->whereBetween('submit_time', [$startDay, $endDay])
            ->groupBy('monthDay')
            ->get();

        $daysTotal = [];
        foreach ($dayTime as $item) {
            $filter = $daysOrderData->filter(function ($value) use ($item) {
                return $value->monthDay == $item;
            });
            if (count($filter) == 0) {
                $daysTotal[] = 0;
            } else {
                $daysTotal[] = $filter->first()->total / 1000000;
            }
        }

        /*最近30天用户数据*/
        $daysUserData = DB::table($userTable)
            ->select(DB::raw('substr(created_at, 1, 10) as monthDay, COUNT(*) as total'))
            ->whereBetween('created_at', [$startDay, $endDay])
            ->groupBy('monthDay')
            ->get();

        $daysUserTotal = [];
        foreach ($dayTime as $item) {
            $filter = $daysUserData->filter(function ($value) use ($item) {
                return $value->monthDay == $item;
            });
            if (count($filter) == 0) {
                $daysUserTotal[] = 0;
            } else {
                $daysUserTotal[] = $filter->first()->total;
            }
        }


        return $this->ajaxJson(true, ['monthTotal' => $monthTotal, 'monthList' => $monthList, 'dayList' => $dayList, 'daysTotal' => $daysTotal, 'monthUserTotal' => $monthUserTotal, 'daysUserTotal' => $daysUserTotal]);

    }

    /**
     * 获取最近12个月
     * @param $year
     * @param $month
     * @param $length
     * @param int $page
     * @return array
     */
    protected function getLastMonthArea($year, $month, $length, $page = 1)
    {
        if (!$page) {
            $page = 1;
        }
        $monthNum = $month + $length - $page * $length;
        $num = 1;
        if ($monthNum < -12) {
            $num = ceil($monthNum / (-12));
        }

        $timeAreaList = [];
        for ($i = 0; $i < $length; $i++) {
            $temMonth = $monthNum - $i;
            $temYear = $year;
            if ($temMonth <= 0) {
                $temYear = $year - $num;
                $temMonth = $temMonth + 12 * $num;
                if ($temMonth <= 0) {
                    $temMonth += 12;
                    $temYear -= 1;
                }
            }

            $startMonth = strtotime($temYear . '-' . $temMonth . '-01');//该月的月初时间戳

            if ($temMonth + 1 > 12) {
                $endMonth = strtotime($temYear + 1 . '-01' . '-01 23:59:59') - 86400;//该月的月末时间戳
            } else {
                $endMonth = strtotime($temYear . '-' . ($temMonth + 1) . '-01 23:59:59') - 86400;//该月的月末时间戳
            }

            $res['startMonth'] = $temYear . '-' . $temMonth . '-01 00:00:00'; //该月的月初格式化时间
            $res['endMonth'] = date('Y-m-d H:i:s', $endMonth);//该月的月末格式化时间
            $res['currentMonth'] = date('Y-m', $endMonth);
            $res['timeArea'] = implode(',', [$startMonth, $endMonth]);//区间时间戳
            $timeAreaList[] = $res;
        }
        return $timeAreaList;
    }

    /**
     * 获取最近30天
     * @return array
     */
    protected function getLastDayArea()
    {
        $day = [];
        $day_time = [];
        for ($i = 30; $i >= 1; $i--) {
            $day[] = date('n-d', time() - 60 * 60 * 24 * $i);
            $day_time[] = date('Y-m-d', time() - 60 * 60 * 24 * $i);
        }
        return [$day, $day_time];
    }
}