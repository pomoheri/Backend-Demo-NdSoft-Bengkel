<?php

namespace App\Helpers;

use App\Models\Customer;

class GlobalGenerateCodeHelper
{
    public function generateCustomerCode($prefix = 'INT/CU/', $length = 6) {
        $lastCode = Customer::latest()->value('code'); 
        if (!$lastCode) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int)substr($lastCode, -1 * $length);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNextNumber = str_pad($nextNumber, $length, '0', STR_PAD_LEFT); // Add leading zeros if necessary

        $mnemonic = $prefix.$formattedNextNumber;

        return $mnemonic;
    }
}
