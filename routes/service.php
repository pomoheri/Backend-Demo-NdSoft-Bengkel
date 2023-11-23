<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'service', "as" => 'service.'], function () {
        //CRUD Labour
        Route::group(['prefix' => 'labour', "as" => 'labour.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\LabourController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\Service\LabourController::class, 'add']);
            Route::get('/detail/{id}', [\App\Http\Controllers\Api\Service\LabourController::class, 'detail']);
            Route::post('/update', [\App\Http\Controllers\Api\Service\LabourController::class, 'update']);
            Route::get('/delete/{labour}', [\App\Http\Controllers\Api\Service\LabourController::class, 'delete']);
        });
        //Hand Over (Serah Terima)
        Route::group(['prefix' => 'hand-over', "as" => 'hand-over.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\HandOverController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\Service\HandOverController::class, 'add']);
            Route::get('/detail/{hand_over_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'detail']);
            Route::post('/update', [\App\Http\Controllers\Api\Service\HandOverController::class, 'update']);
            Route::get('/print-hand-over/{hand_over_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'printHandOver']);
            Route::get('/delete/{hand_over_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'delete']);
            Route::get('/transfer/{hand_over_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'transferToWo']);
        });
        //Estimation (estimasi dibuat atas permintaan/hand over)
        Route::group(['prefix' => 'estimation', "as" => 'estimation.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\EstimationController::class, 'list']);
        });
        //WorkOrder
        Route::group(['prefix' => 'work-order', "as" => 'work-order.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\WorkOrderController::class, 'list']);
            Route::get('/detail/{transaction_unique}', [\App\Http\Controllers\Api\Service\WorkOrderController::class, 'detail']);
            Route::post('/update', [\App\Http\Controllers\Api\Service\WorkOrderController::class, 'updateWo']);
            Route::post('/update-status', [\App\Http\Controllers\Api\Service\WorkOrderController::class, 'updateStatus']);
        });
        //Invoice
        Route::group(['prefix' => 'invoice', "as" => 'invoice.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\InvoiceController::class, 'list']);
            Route::get('/detail/{transaction_unique}', [\App\Http\Controllers\Api\Service\InvoiceController::class, 'detail']);
            Route::post('/submit-payment', [\App\Http\Controllers\Api\Service\InvoiceController::class, 'submitPayment']);
            Route::post('/update-invoice', [\App\Http\Controllers\Api\Service\InvoiceController::class, 'updateInvoice']);
            Route::get('/get-pdf/{transaction_unique}', [\App\Http\Controllers\Api\Service\InvoiceController::class, 'getPdf']);
        });

    });
});