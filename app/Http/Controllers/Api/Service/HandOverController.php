<?php

namespace App\Http\Controllers\Api\Service;

use PDF;
use App\Models\HandOver;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\HandOverRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HandOverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list(Request $request)
    {
        try {
            $data = HandOver::query();
            if (isset($request->start_date) && $request->start_date) {
                $data = $data->where('created_at', '>=', $request->start_date);
            }
            if (isset($request->end_date) && $request->end_date) {
                $data = $data->where('created_at', '<=', $request->end_date);
            }
            $data = $data->whereIn('status', ['Draft', 'New'])->with('vehicle', 'vehicle.carType', 'vehicle.carType.carBrand', 'vehicle.customer')->orderBy('created_at', 'desc')->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data HandOver']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'vehicle_id'                  => ['required', 'integer'],
                'request_order'               => ['required', 'array'],
                'request_order.*.description' => ['required'],
                'carrier'                     => ['required'],
                'carrier_phone'               => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'vehicle_id'    => $request->vehicle_id,
                'status'        => 'Draft',
                'carrier'       => $request->carrier,
                'carrier_phone' => $request->carrier_phone,
                'created_by'    => auth()->user()->name
            ];

            $handOver = HandOver::create($data);

            if (count($request->request_order) > 0) {
                foreach ($request->request_order as $key => $value) {
                    $data_request = [
                        'hand_over_unique' => $handOver->hand_over_unique,
                        'request'          => $value['description'],
                    ];
                    HandOverRequest::updateOrCreate($data_request);
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($handOver, ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail($hand_over_unique)
    {
        try {
            $handOver = HandOver::with('vehicle', 'vehicle.customer', 'vehicle.carType', 'vehicle.carType.carBrand', 'handOverRequest')
                ->where('hand_over_unique', $hand_over_unique)
                ->first();
            if (!$handOver) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($handOver, ['Data Detail']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'hand_over_unique'            => ['required'],
                'vehicle_id'                  => ['required', 'integer'],
                'request_order'               => ['required', 'array'],
                'request_order.*.description' => ['required'],
                'carrier'                     => ['required', 'max:200'],
                'carrier_phone'               => ['required', 'max:15']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $handOver = HandOver::where('hand_over_unique', $request->hand_over_unique)->first();
            if (!$handOver) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            if ($handOver->status == 'Transfered') {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Gagal, Data sudah di keluarkan']);
            }

            $data = [
                'vehicle_id'    => $request->vehicle_id,
                'updated_by'    => auth()->user()->name,
                'carrier'       => $request->carrier,
                'carrier_phone' => $request->carrier_phone,
            ];

            $handOver->update($data);

            $get_id = [];
            foreach ($request->request_order as $key => $value) {
                $get_id[] = $value['id'];
            };

            $deleteNotrequest = HandOverRequest::where('hand_over_unique', $request->hand_over_unique)->whereNotIn('id', $get_id)->delete();

            foreach ($request->request_order as $key => $value) {
                $detail_request = HandOverRequest::where('id', $value['id'])->where('hand_over_unique', $request->hand_over_unique)->first();

                if ($detail_request) {
                    $data_detail = [
                        'request'      => $value['description']
                    ];
                    $detail_request->update($data_detail);
                } else {
                    $data_detail = [
                        'hand_over_unique'   => $request->hand_over_unique,
                        'request'            => $value['description']
                    ];
                    HandOverRequest::create($data_detail);
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($handOver, ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function printHandOver($hand_over_unique)
    {
        try {
            $handOver = HandOver::with('vehicle', 'vehicle.customer', 'vehicle.carType', 'vehicle.carType.carBrand', 'handOverRequest')
                ->where('hand_over_unique', $hand_over_unique)->first();
            if (!$handOver) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $content_qrcode = 'HandOver-'.$handOver->handover_unique.'/'.$handOver->created_at;
            $qrcode_ttd = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')->generate($content_qrcode));

            $data = [
                'handOver' => $handOver,
                'qrcode_ttd' => $qrcode_ttd
            ];
            $pdf = PDF::loadView('documents.hand-over-document', $data)->setPaper('a4', 'potrait');

            $pdf_file = $pdf->output();

            $directory = 'hand-over/' . $hand_over_unique . '/';
            $filename = $hand_over_unique . '.pdf';
        
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

    public function delete($hand_over_unique)
    {
        try {
            $hand_over = HandOver::where('hand_over_unique', $hand_over_unique)->first();
            if (!$hand_over) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            if ($hand_over->status == 'Transfered') {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Gagal, Data sudah di keluarkan']);
            }
            $hand_over->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function transferToWo($hand_over_unique)
    {
        try {
            $handOver = HandOver::with('vehicle', 'handOverRequest')->where('hand_over_unique', $hand_over_unique)->first();
            if (!$handOver) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $work_order = WorkOrder::where('transaction_unique', $handOver->hand_over_unique)->first();
            if ($work_order) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Sudah ada di Work Order']);
            } else {
                $wo_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCodeWo();

                $work_order = WorkOrder::create([
                    'transaction_code'     => $wo_code,
                    'transaction_unique'   => $handOver->hand_over_unique,
                    'vehicle_id'           => $handOver->vehicle_id,
                    'total'                => 0,
                    'status'               => 'Draft',
                    'carrier'              => $handOver->carrier,
                    'carrier_phone'        => $handOver->carrier_phone,
                    'km'                   => $handOver->vehicle->last_km,
                    'created_by'           => auth()->user()->name
                ]);

                if ($handOver->handOverRequest) {
                    foreach ($handOver->handOverRequest as $key => $value) {
                        $service_request = ServiceRequest::create([
                            'transaction_unique' => $work_order->transaction_unique,
                            'request'            => $value->request
                        ]);
                    }
                }

                $handOver->update([
                    'status' => 'Transfered'
                ]);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([$work_order], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
