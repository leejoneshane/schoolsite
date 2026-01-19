<?php

namespace App\Exports\Sheets;

use App\Models\LunchTeacher;
use App\Models\LunchCafeteria;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LunchLocationSheet implements FromView, WithTitle
{
    protected $section;
    protected $cafeteria;

    public function __construct($section, $cafeteria)
    {
        $this->section = $section;
        $this->cafeteria = $cafeteria;
    }

    public function view(): View
    {
        // Get all teachers for this section
        $teachers = LunchTeacher::where('section', $this->section)->get();

        $rows = [];
        $totals = [
            'mon' => 0,
            'tue' => 0,
            'wed' => 0,
            'thu' => 0,
            'fri' => 0,
            'vegen' => 0,
            'soy_milk' => 0
        ];

        foreach ($teachers as $teacher) {
            // Check if this teacher has chosen this cafeteria for ANY day
            // places array [0 => site_id, ...]
            $places = $teacher->places ?? [];
            $weekdays = $teacher->weekdays ?? [];

            $is_at_this_location = false;
            $days_marks = [0 => '', 1 => '', 2 => '', 3 => '', 4 => ''];

            $has_meal_here = false;

            for ($i = 0; $i < 5; $i++) {
                // Check if they are eating ($weekdays[$i]) AND location is this cafeteria
                if (isset($weekdays[$i]) && $weekdays[$i] && isset($places[$i]) && $places[$i] == $this->cafeteria->id) {
                    $is_at_this_location = true;
                    $days_marks[$i] = '1';
                    $has_meal_here = true;

                    // Increment daily totals
                    $key = ['mon', 'tue', 'wed', 'thu', 'fri'][$i];
                    $totals[$key]++;
                }
            }

            if ($is_at_this_location) {
                // If they eat here at least once

                $is_vegen = $teacher->vegen ? '1' : '';
                $is_soy = (!$teacher->milk) ? '1' : ''; // false milk = soy milk

                // Increment totals for vegen/soy ONLY if they are eating here?
                // The requirements says "Teacher Name", "Mon-Fri", "Vegen", "Soy Milk".
                // If a teacher eats at Location A on Mon, and Location B on Tue.
                // In Location A sheet: Mon=1, Vegen=1 (if vegen).
                // It makes sense to count them as Vegen/Soy if they appear on this sheet.
                // However, do we count them multiple times if they appear in multiple sheets?
                // Usually "Vegen" column indicates preference. 
                // Let's assume we sum them up.

                if ($is_vegen)
                    $totals['vegen']++;
                if ($is_soy)
                    $totals['soy_milk']++;

                $rows[] = [
                    'name' => $teacher->realname,
                    'mon' => $days_marks[0],
                    'tue' => $days_marks[1],
                    'wed' => $days_marks[2],
                    'thu' => $days_marks[3],
                    'fri' => $days_marks[4],
                    'vegen' => $is_vegen,
                    'soy_milk' => $is_soy,
                ];
            }
        }

        // Sort by name (optional, but good for listing)
        usort($rows, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return view('exports.lunch_location_sheet', ['rows' => $rows, 'totals' => $totals]);
    }

    public function title(): string
    {
        return $this->cafeteria->description;
    }
}
