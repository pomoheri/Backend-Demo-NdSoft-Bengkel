<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\SparePart;
use App\Models\Supplier;

class GlobalGenerateCodeHelper
{
    public function generateCustomerCode($prefix = 'INT/CU/', $length = 6)
    {
        $lastCode = Customer::max('code');
        if (!$lastCode) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int)substr($lastCode, -1 * $length);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNextNumber = str_pad($nextNumber, $length, '0', STR_PAD_LEFT); // Add leading zeros if necessary

        $mnemonic = $prefix . $formattedNextNumber;

        return $mnemonic;
    }

    public function generateSupplierCode($prefix = 'INT/SU/', $length = 6)
    {
        $lastCode = Supplier::max('supplier_code');
        if (!$lastCode) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int)substr($lastCode, -1 * $length);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNextNumber = str_pad($nextNumber, $length, '0', STR_PAD_LEFT); // Add leading zeros if necessary

        $mnemonic = $prefix . $formattedNextNumber;

        return $mnemonic;
    }
    public function generateSparePartCode($prefix = 'INT/PART/', $length = 6)
    {
        $lastCode = SparePart::max('part_number');
        if (!$lastCode) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int)substr($lastCode, -1 * $length);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNextNumber = str_pad($nextNumber, $length, '0', STR_PAD_LEFT); // Add leading zeros if necessary

        $mnemonic = $prefix . $formattedNextNumber;

        return $mnemonic;
    }
    public function generateTransactionCode($prefix = 'INT/PO/', $length = 6)
    {
        $currentMonth = now()->format('Y/m'); 
        
        $lastCode = PurchaseOrder::max('transaction_code');
        $storedMonth = substr($lastCode, strlen($prefix), 7);

        if ($storedMonth != $currentMonth) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int)substr($lastCode, -1 * $length);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNextNumber = str_pad($nextNumber, $length, '0', STR_PAD_LEFT); // Add leading zeros if necessary

        $mnemonic = $prefix . $currentMonth .'/'. $formattedNextNumber;

        return $mnemonic;
    }

}
