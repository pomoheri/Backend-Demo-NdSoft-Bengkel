<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Models\Customer;
use App\Models\HandOver;
use App\Models\ServiceInvoice;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Models\SellSparepart;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getData(Request $request)
    {
        try {
            $month = (isset($request->month) && $request->month) ? $request->month : date('m');
            $year = (isset($request->year) && $request->year) ? $request->year : date('Y');

            $sell_sparepart = SellSparepart::where('status', 'Closed')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->count();
            $hand_over = HandOver::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $customer  = Customer::count();
            $invoice_service = ServiceInvoice::where('status', 'Closed')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->count();
            $work_order_day = WorkOrder::where('status', '!=', 'Closed')->where('created_at', date('Y-m-d'))->count();
            $vehicle = Vehicle::count();

            $three_month_ago = today()->subMonths(3)->format('Y-m-d');
            $follow_up = WorkOrder::with('vehicle','vehicle.customer', 'vehicle.carType', 'vehicle.carType.carBrand','serviceInvoice')
                                ->whereDate('created_at', $three_month_ago)
                                ->where('status', 'Closed')
                                ->get();
            $list_follow_up = [];
            if($follow_up->count() > 0){
                foreach ($follow_up as $key => $value) {
                    $list_follow_up[] = [
                        'transaction_unique' => $value->transaction_unique,
                        'customer' => ($value->vehicle) ? ($value->vehicle->customer ? $value->vehicle->customer->name : '') : '',
                        'phone'    => ($value->vehicle) ? ($value->vehicle->customer ? $value->vehicle->customer->phone : '') : '',
                        'car'      => ($value->vehicle) ? ($value->vehicle->carType ? $value->vehicle->carType->name : '') : '',
                        'license_plate' => ($value->vehicle) ? $value->vehicle->license_plate : '',
                        'invoice_code' => ($value->serviceInvoice) ? $value->serviceInvoice->transaction_code : ''
                    ];
                }
            }

            $output = [
                'sell_sparepart'  => $sell_sparepart,
                'hand_over_entry' => $hand_over,
                'customer_all'    => $customer,
                'invoice_service' => $invoice_service,
                'work_order_day'  => $work_order_day,
                'vehicle_all'     => $vehicle,
                'follow_up_three_month' => $list_follow_up

            ];
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Dashboard']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
