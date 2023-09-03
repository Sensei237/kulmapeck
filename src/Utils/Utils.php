<?php
namespace App\Utils;

class Utils {

    public static function checkNumberOperator($number) {

        
        // Remove leading '+' or '0237' from the number
        $cleanedNumber = preg_replace('/^(\+237|0237)/', '', $number);
        
        // Check the cleaned number's length
        if (strlen($cleanedNumber) !== 9) {
            throw new \InvalidArgumentException('Number length must be at least 9 digits');
        }
        
        // Extract the first three digits of the number
        $prefix = substr($cleanedNumber, 0, 3);
        
        // Define the prefix values for CM_OM and CM_MOMO
        $cmOmPrefixes = ['69', '655', '656', '657', '658', '659'];
        $cmMomoPrefixes = ['67', '651', '652', '653', '654'];
        
        // Check the prefix and return the appropriate value
        if (in_array($prefix, $cmOmPrefixes)) {
            return 'CM_OM';
        } elseif (in_array($prefix, $cmMomoPrefixes)) {
            return 'CM_MOMO';
        } else {
            throw new \InvalidArgumentException('Invalid number prefix');
        }
    }
}
