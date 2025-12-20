<?php

use App\Models\ReferenceNumber;

if (!function_exists('getNextReferenceNumber')) {

    function getNextReferenceNumber($type)
    {
        // Create or get record
        $record = ReferenceNumber::firstOrCreate(
            ['type' => $type],
            ['current_no' => 0]
        );

        // Increment number
        $record->current_no += 1;
        $record->save();

        return $record->current_no;
    }
}
