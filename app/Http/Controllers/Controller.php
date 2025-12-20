<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function update_profile_completion($student_id, $old_value, $new_value)
    {
        Students::where('id', $student_id)
            ->where('profile_completion', $old_value)
            ->update(['profile_completion' => $new_value]);
    }

    public function getProfileCompletedState($student_id)
    {
        $completed_levels = [
            0 => 'danger',
            1 => 'warning',
            2 => 'primary',
            3 => 'info',
            4 => 'success',
        ];
        $profile_completion = Students::where('id', $student_id)->value('profile_completion');

        return [$profile_completion, $completed_levels[$profile_completion]];
    }

    public function format_number($number, $space = "")
    {
        $number = floatval($number);
        if ($number >= 1_000_000_000) {
            return number_format($number / 1_000_000_000, 2) . $space . 'B';
        } elseif ($number >= 1_000_000) {
            return number_format($number / 1_000_000, 2) . $space . 'M';
        } elseif ($number >= 100_000) {
            return number_format($number / 100_000, 2) . $space . 'L';
        } elseif ($number >= 1_000) {
            return number_format($number / 1_000, 2) . $space . 'K';
        } else {
            return $number;
        }
    }
}
