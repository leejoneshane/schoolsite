<?php

namespace App\Exports\Sheets;

use App\Models\Classroom;
use App\Models\LunchSurvey;
use App\Models\LunchTeacher;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LunchGradeSheet implements FromView, WithTitle
{
    protected $section;
    protected $grade;

    public function __construct($section, $grade)
    {
        $this->section = $section;
        $this->grade = $grade;
    }

    public function view(): View
    {
        $classes = Classroom::where('grade_id', $this->grade)->orderBy('id')->get();
        $rows = [];

        foreach ($classes as $class) {
            $surveys = LunchSurvey::class_survey($class->id, $this->section);
            $students = $class->students;

            // Lunch counts
            $all_students_count = $students->count();

            // Students ordering lunch (by_school)
            $ordering_students = $surveys->where('by_school', true);
            $meat_count = $ordering_students->where('vegen', false)->count();

            // Vegetarian seats
            $vegen_seats = $ordering_students->where('vegen', true)->map(fn($s) => $s->seat)->sort()->implode(',');

            // Lactose intolerant (milk = false implies soy milk replacement if ordering by school)
            // Logic: "乳糖不耐症學生座號" (Students who drink soy milk)
            // Usually, vegen students might drink milk or soy milk. 
            // If the field implies 'Not drinking milk', check 'milk' == false
            $milk_seats = $ordering_students->where('milk', false)->map(fn($s) => $s->seat)->sort()->implode(',');

            // Students NOT ordering lunch
            // Those who are NOT in $ordering_students
            $ordering_uuids = $ordering_students->pluck('uuid')->toArray();
            $no_order_seats = $students->filter(function ($student) use ($ordering_uuids) {
                return !in_array($student->uuid, $ordering_uuids);
            })->map(fn($s) => $s->seat)->sort()->implode(',');

            // Tutor Dining Logic
            $tutor_dining = '無';
            // Find tutor for this class
            // Classroom has 'tutors' relation which returns Teachers.
            // But usually there is one main tutor. We check LunchTeacher records for any teacher who is tutor of this class.

            // We can match by tutor_class in user->teacher profile? 
            // In LunchController: $is_tutor = employee()->tutor_class;
            // We need to find the teacher record whose tutor_class == $class->id

            // Let's assume there is only one main tutor for simplicity or check all.
            // But LunchTeacher is keyed by uuid. 
            // We can search LunchTeacher where 'tutor' is true. 
            // But LunchTeacher doesn't store class_id directly easily? 
            // Wait, LunchTeacher doesn't have class info. 
            // We need to find the User/Teacher who is the tutor of this class.
            // Classroom->tutors()

            $tutors = $class->tutors;
            $dining_days_str = [];

            foreach ($tutors as $tutor) {
                // Check if this tutor has a LunchTeacher record for this section
                $teacher_lunch = LunchTeacher::where('uuid', $tutor->uuid)->where('section', $this->section)->first();

                if ($teacher_lunch && $teacher_lunch->tutor) {
                    // Check specific weekdays based on grade
                    $days = ['一', '二', '三', '四', '五'];
                    $fixed_days_indices = [];

                    if ($this->grade == 1 || $this->grade == 2) {
                        $fixed_days_indices = [3]; // Thu
                    } elseif ($this->grade == 3 || $this->grade == 4) {
                        $fixed_days_indices = [0, 1, 3]; // Mon, Tue, Thu
                    } elseif ($this->grade >= 5) {
                        $fixed_days_indices = [0, 1, 3, 4]; // Mon, Tue, Thu, Fri
                    }

                    $eating_days = [];
                    foreach ($fixed_days_indices as $idx) {
                        // Check if teacher selected to eat on this day
                        // The 'weekdays' array in LunchTeacher stores if they eat or not. index 0=Mon
                        if (isset($teacher_lunch->weekdays[$idx]) && $teacher_lunch->weekdays[$idx]) {
                            $eating_days[] = $days[$idx];
                        }
                    }

                    if (!empty($eating_days)) {
                        $dining_days_str[] = '星期' . implode('', $eating_days);
                    }
                }
            }

            $tutor_dining_text = empty($dining_days_str) ? '無' : implode('、', array_unique($dining_days_str));

            $rows[] = [
                'class_name' => $class->name,
                'meat_count' => $meat_count,
                'vegen_seats' => $vegen_seats,
                'total_students' => $all_students_count,
                'milk_seats' => $milk_seats,
                'no_order_seats' => $no_order_seats,
                'tutor_dining' => $tutor_dining_text,
            ];
        }

        return view('exports.lunch_grade_sheet', ['rows' => $rows]);
    }

    public function title(): string
    {
        return $this->grade . '年級';
    }
}
