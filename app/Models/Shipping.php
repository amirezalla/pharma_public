<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    public static function getShippingCost($region, $customerType, $orderAmount)
    {
        if ($customerType == 'Studio Medico e Dentista') {
            return 'To be determined';
        }
        if ($region == 'Campania e Lazio' && $customerType == 'Farmacia, Parafarmacia, Altro Pharma' && $orderAmount < 300) {
            return '10,00 euros';
        }
        if ($region == 'Campania e Lazio' && $customerType == 'Farmacia, Parafarmacia, Altro Pharma' && $orderAmount >= 300) {
            return '5,00 euros';
        }
        if ($customerType == 'Farmacia, Parafarmacia, Altro Pharma') {
            return '10,00 euros';
        }
        return 'Unknown'; // Default
    }
}
