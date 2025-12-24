<?php
namespace Database\Seeders;

use App\Models\Dictionary;
use Illuminate\Database\Seeder;

class DictionarySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'gender'            => [
                'male'   => 'Male',
                'female' => 'Female',
                'other'  => 'Other',
            ],
            'academic_status'   => [
                'active'      => 'Active',
                'probation'   => 'Probation',
                'suspended'   => 'Suspended',
                'graduated'   => 'Graduated',
                'withdrawn'   => 'Withdrawn',
                'transferred' => 'Transferred',
            ],
            'enrollment_status' => [
                'full_time' => 'Full Time',
                'part_time' => 'Part Time',
                'exchange'  => 'Exchange Student',
            ],
            'title'             => [
                'mr'   => 'Mr.',
                'ms'   => 'Ms.',
                'mrs'  => 'Mrs.',
                'dr'   => 'Dr.',
                'prof' => 'Prof.',
            ],
            'blood_group'       => [
                'a_plus'   => 'A+',
                'a_minus'  => 'A-',
                'b_plus'   => 'B+',
                'b_minus'  => 'B-',
                'o_plus'   => 'O+',
                'o_minus'  => 'O-',
                'ab_plus'  => 'AB+',
                'ab_minus' => 'AB-',
            ],
            'attendance_status' => [
                'present' => 'Present',
                'absent'  => 'Absent',
                'late'    => 'Late',
                'excused' => 'Excused',
            ],
        ];

        foreach ($data as $category => $items) {
            foreach ($items as $key => $label) {
                Dictionary::firstOrCreate(
                    ['category' => $category, 'key' => $key],
                    ['label' => $label, 'is_active' => true]
                );
            }
        }
    }
}
