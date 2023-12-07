<?php

namespace App\Http\Controllers\Api\RekapHarian;

use App\Models\PurchaseOrder;
use App\Models\SmallTransaction;
use PDF;
use Nim4n\SimpleDate;
use Illuminate\Http\Request;
use App\Models\SellSparepart;
use App\Models\ServiceInvoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class RekapHarianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function invoiceService(Request $request)
    {
        try {
            $date = (isset($request->date) && $request->date) ? $request->date : date('Y-m-d');

            $data = ServiceInvoice::query();
            $data = $data->with('workOrder')->where('status', 'Closed')->where('closed_at', $date)->get();

            $cash = 0;
            $transfer = 0;
            $qris = 0;
            $edc = 0;
            if($data->count() > 0){
                foreach ($data as $key => $value) {
                    if($value->payment_gateway == 'Cash'){
                        $cash += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'Transfer'){
                        $transfer += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'QRIS'){
                        $qris += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'EDC'){
                        $edc += ($value->workOrder) ? $value->workOrder->total : 0;
                    }
                }
            }

            $output = [
                'date'     => $date,
                'cash'     => $cash,
                'transfer' => $transfer,
                'qris'     => $qris,
                'edc'      => $edc
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Rekap Harian Service Invoice']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function invoiceSell(Request $request)
    {
        try {
            $date = (isset($request->date) && $request->date) ? $request->date : date('Y-m-d');
            
            $data = SellSparepart::query();
            $data = $data->where('status', 'Closed')->where('closed_at', $date);

            $cash = $data->where('payment_gateway', 'Cash')->get()->sum('total');
            $transfer = $data->where('payment_gateway', 'Transfer')->get()->sum('total');
            $qris = $data->where('payment_gateway', 'QRIS')->get()->sum('total');
            $edc = $data->where('payment_gateway', 'EDC')->get()->sum('total');

            $output = [
                'date'     => $date,
                'cash'     => $cash,
                'transfer' => $transfer,
                'qris'     => $qris,
                'edc'      => $edc
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Rekap Harian Sell Sparepart']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function lainnya(Request $request)
    {
        try {
            $date = (isset($request->date) && $request->date) ? $request->date : date('Y-m-d');

            $data = SmallTransaction::query();
            $total = $data->where('status', 'Debit')->where('date', $date)->get()->sum('total');

            $output = [
                'total' => $total
            ];
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Rekap Harian Pemasukan Small Transaction']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function getPdf(Request $request)
    {
        try {
            $date = (isset($request->date) && $request->date) ? $request->date : date('Y-m-d');

            $data_service = ServiceInvoice::query();
            $data_service = $data_service->with('workOrder')->where('status', 'Closed')->where('closed_at', $date)->get();

            $service_cash = 0;
            $service_transfer = 0;
            $service_qris = 0;
            $service_edc = 0;
            if($data_service->count() > 0){
                foreach ($data_service as $key => $value) {
                    if($value->payment_gateway == 'Cash'){
                        $service_cash += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'Transfer'){
                        $service_transfer += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'QRIS'){
                        $service_qris += ($value->workOrder) ? $value->workOrder->total : 0;
                    }else if($value->payment_gateway == 'EDC'){
                        $service_edc += ($value->workOrder) ? $value->workOrder->total : 0;
                    }
                }
            }

            $data_sell = SellSparepart::query();
            $data_sell = $data_sell->where('status', 'Closed')->where('closed_at', $date);

            $sell_cash = $data_sell->where('payment_gateway', 'Cash')->get()->sum('total');
            $sell_transfer = $data_sell->where('payment_gateway', 'Transfer')->get()->sum('total');
            $sell_qris = $data_sell->where('payment_gateway', 'QRIS')->get()->sum('total');
            $sell_edc = $data_sell->where('payment_gateway', 'EDC')->get()->sum('total');

            $data = [
                'date'             => SimpleDate::date($date),
                'service_cash'     => $service_cash,
                'service_transfer' => $service_transfer,
                'service_qris'     => $service_qris,
                'service_edc'      => $service_edc,
                'sell_cash'        => $sell_cash,
                'sell_transfer'    => $sell_transfer,
                'sell_qris'        => $sell_qris,
                'sell_edc'         => $sell_edc
            ];

            $pdf = PDF::loadView('documents.rekap-harian', $data)->setPaper('a4', 'potrait');

            $pdf_file = $pdf->output();

            $directory = 'rekap-harian/' . date('y-m-d') . '/';
            $filename = md5(date('y-m-d')) . '.pdf';
        
            if (Storage::disk('s3')->exists($directory . $filename)) {
                Storage::disk('s3')->delete($directory . $filename);
            }
            // Upload the file to S3
            Storage::disk('s3')->put($directory . $filename, $pdf_file, 'public');
        
            $pdf_url = env('AWS_URL') . $directory . $filename;

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($pdf_url, ['Data Berhasil Di Generate']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function pengeluaran(Request $request)
    {
        try {
            $date = (isset($request->date) && $request->date) ? $request->date : date('Y-m-d');

            $po_sparepart = PurchaseOrder::where('status', 'Paid')->where('invoice_date', $date)->get()->sum('total');

            $small_transaction = SmallTransaction::query();

            $data = $small_transaction->where('status', 'Kredit')->where('date', $date);

            $cost = $data->where('category', 'Cost')->get()->sum('total');
            $sublet = $data->where('category', 'Sublet')->get()->sum('total');
            $asset = $data->where('category', 'Asset')->get()->sum('total');
            $prive = $data->where('category', 'Prive')->get()->sum('total');

            $output = [
                'po_sparepart' => $po_sparepart,
                'cost'         => $cost,
                'sublet'       => $sublet,
                'asset'        => $asset,
                'prive'        => $prive
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Rekap Harian Pengeluaran']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
