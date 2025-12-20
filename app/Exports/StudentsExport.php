<?php

namespace App\Exports;

use App\Models\Cities;
use App\Models\CoursePayments;
use App\Models\CourseSchedules;
use App\Models\EducationalQualifications;
use App\Models\IdentityCards;
use App\Models\States;
use App\Models\Students;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    /**
     * Return the collection of data to be exported.
     */
    public function collection()
    {
        return $this->students; // Use pre-fetched data
    }


    public function headings(): array
    {
        return [
            'ID',
            'Track ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Address',
            'Last completed course',
            'Last completed course board',
            'City',
            'State',
            'Country',
            'Date of birth',
            'Identity card type',
            'Identity card no',
            'University',
            'Course',
            'Course fee',
            'Payed amount',
            'Pending amount',
            'Payment Status',
            'Profile Status',
            'Next Payment Date',
        ];
    }

    public function map($student): array
    {
        $pending_amount = '';
        $payed_amount = floatval($student->amount) + floatval($student->discount);
        $course_data = CourseSchedules::with([
            'course:id,specialization,stream_id,university_id',
            'course.streams:id,code',
            'course.university:id,university_code',
        ])
            ->where('id', $student->course_schedule_id)

            ->first();
        if ($course_data && $course_data->course) {
            $course = $course_data->course; // To avoid repeated access
            $university = $course->university->university_code ?? '';
            $specialization = $course->specialization ?? '';
            $stream_code = $course->streams->code ?? '';
            $course_fee = floatval($course_data->course_fee) ?? 0;
            $other_fees = floatval($course_data->other_fees) ?? 0;
            $course_fee += $other_fees;

            $pending_amount = $course_fee - $payed_amount;
        } else {
            // Default values if no course data is found
            $university = '';
            $specialization = '';
            $stream_code = '';
            $course_fee = '';
            $other_fees = '';
        }

        $date_of_birth = date('d-m-Y', strtotime($student->date_of_birth));
        $education = EducationalQualifications::where('student_id', $student->id)
            ->first();
        if ($education) {
            $university_name = $education->other_college_name;
            $degree_name = $education->other_degree_name;
        } else {
            $university_name = '';
            $degree_name = '';
        }
        $location = Cities::with(['state.country' => function ($query) {
            $query->select('id', 'name');
        }, 'state' => function ($query) {
            $query->select('id', 'name', 'country_id');
        }])
            ->where('id', $student->city_id)
            ->select('id', 'name', 'state_id') // Select columns from the city table
            ->first();
        $identity_card = IdentityCards::where('id', $student->identity_card_id)
            ->value('name');

        return [
            $student->id, //A
            $student->student_track_id, // B
            $student->first_name, //C
            $student->last_name, //D
            $student->email, //E
            $student->phone_number, //F
            $student->address, //G
            $degree_name, //H
            $university_name, //I
            $location->name, //J
            $location->state->name, //K
            $location->state->country->name, //K
            $date_of_birth, //L
            $identity_card,
            $student->identity_card_no, //N
            $university, //O
            $stream_code . ' ' . $specialization, //O
            $course_fee, //O
            $payed_amount, //P
            $pending_amount, //Q
            $student->payment_status, //R
            ($student->profile_completion * 25) / 100, //S
            $student->next_payment_date, //T
            // Add more fields as needed
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set the style for the header row
        $sheet->getStyle('A1:W1')->getFont()->setBold(true);

        // // Example: Format the 'Percentage Column' (assuming it is column F, which is the 6th column)
        $sheet->getStyle('V')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        // // Example: Format the 'Number Column' (assuming it is column G, which is the 7th column)
        foreach (range('R', 'T') as $columnLetter) {
            $sheet->getStyle($columnLetter . '')->getNumberFormat()->setFormatCode('#,##0.00');
        }

        // // Apply auto-size to all columns (optional)
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
    }
}
