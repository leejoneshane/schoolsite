<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\LunchSurvey;
use App\Models\LunchTeacher;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class LunchPaymentExport implements FromView
{
    use Exportable;
    protected $section;

    public function __construct($section)
    {
        $this->section = $section;
    }

    public function view(): View
    {
        // Settings for price
        $settings = DB::table('lunch')->where('section', $this->section)->first();
        $price_per_day = $settings ? $settings->money : 0;
        $weeks_per_month = 4;
        // Calculation: (Price * 5 days * 4 weeks) ? NO
        // The requirement says: "次/月（一個月以四週計算）、金額/日（請讀取 lunch 資料表的 money 欄位）、金額（次/月 乘以 金額/日）"
        // Wait, "次/月" (Times/Month).
        // Regular Students: They eat every school day? 
        // Usually 5 days a week * 4 weeks = 20 days/month?
        // Or strictly 4 weeks?
        // Let's assume 20 days (5 days * 4 weeks).

        // However, "次/月（一個月以四週計算）" might mean "Number of meals per month".
        // Regular students usually eat 5 days/week. So 20 times.
        // But some might subtract days? No, for "Regular Dining" table, it groups by Class.
        // It assumes standard ordering.
        // Students who order 'by_school' usually eat every day unless they ask for refund due to leave.
        // But here it's likely a billing statement for the start of semester.
        // Let's assume 20 days.
        $days_per_month = 5 * 4;
        $monthly_price = $days_per_month * $price_per_day;

        // 1. Regular Dining
        $classes = Classroom::all()->sortBy('id');
        $regular_rows = [];

        foreach ($classes as $class) {
            // Count students who ordered lunch (by_school = 1)
            $count = LunchSurvey::class_survey($class->id, $this->section)
                ->where('by_school', true)
                ->count();

            if ($count > 0) {
                $regular_rows[] = [
                    'class_name' => $class->name,
                    'count' => $count,
                    'subtotal' => $count * $monthly_price
                ];
            }
        }

        // 2. After-school Dining (課照班)
        // Group by Grade.
        // Student's current_enrolls_for_kind(5, $section)
        // We need to iterate all students enrolled in kind 5 for this section.
        // Easier: Get ClubEnroll where club.kind_id=5 and section=$section.

        // Let's use Student model helper? But iterating all students is slow.
        // Let's use DB query or Eloquent on models.
        // Assuming ClubEnroll links to Club.

        // "current_enrolls_for_kind" logic in Student model:
        /*
            $enrolls()->where('section', $section)->get()->filter(...)
        */

        // We can query ClubEnroll with whereHas('club', ... kind_id=5)
        // Then group by student's grade.

        // Note: requirement says "current_enrolls_for_kind" in Student model.
        // But efficiently we should query globally.

        $after_school_rows = [];
        for ($g = 1; $g <= 6; $g++) {
            // Find students in this grade
            // It's approximations. "After school class" might be mixed? 
            // Usually we report by Grade.

            // Get all students of this grade using Classroom
            $class_ids = Classroom::where('grade_id', $g)->pluck('id');
            // Find their uuids
            $uuids = Student::whereIn('class_id', $class_ids)->pluck('uuid');

            // Check enrollment in After School (Kind 5)
            // Use joins for performance?
            // "Student 模組的 current_enrolls_for_kind 可讀取" -> Suggests using the model helper.
            // But we can't instantiate thousands of models.

            // Let's do a raw query or Eloquent query to count unique students enrolled in Kind 5 for this section/grade.
            $count = DB::table('club_enrolls')
                ->join('clubs', 'club_enrolls.club_id', '=', 'clubs.id')
                ->where('club_enrolls.section', $this->section)
                ->where('clubs.kind_id', 5)
                ->where('club_enrolls.accepted', 1) // Should we only count accepted? Usually yes for billing.
                ->whereIn('club_enrolls.uuid', $uuids)
                ->count(); // Count enrollments? Or Students?
            // A student might join multiple after school classes (e.g. Mon, Tue...)
            // Requirement: "人數" (Number of people).
            // "次/月（一個月以四週計算）"
            // For after-school, do they eat 5 days? Or only days they attend?
            // Usually After-school lunch is for those staying late?
            // The prompt says: "次/月 ... 金額 ... 人數"
            // It implies a fixed rate like Regular Dining.
            // If a student is in After Schools, they eat lunch? 
            // Wait, usually After School Lunch is separate? Or is it Dinner?
            // Assuming "課照班用餐" means Lunch for these kids? Or Dinner?
            // If it follows the same "Price/Day" and "Times/Month", it assumes 5 days/week.
            // Let's assume we simply count unique students.

            $student_count = DB::table('club_enrolls')
                ->join('clubs', 'club_enrolls.club_id', '=', 'clubs.id')
                ->where('club_enrolls.section', $this->section)
                ->where('clubs.kind_id', 5)
                ->where('club_enrolls.accepted', 1)
                ->whereIn('club_enrolls.uuid', $uuids)
                ->distinct('club_enrolls.uuid')
                ->count('club_enrolls.uuid');

            if ($student_count > 0) {
                $after_school_rows[] = [
                    'grade_name' => $g . '年級',
                    'count' => $student_count,
                    'subtotal' => $student_count * $monthly_price
                ];
            }
        }

        // 3. Teacher Dining
        $teachers_lunch = LunchTeacher::where('section', $this->section)->get();
        $teacher_total_days = 0;

        foreach ($teachers_lunch as $tl) {
            // count days in 'weekdays' that are true
            // weekdays array or json
            $days = 0;
            if (is_array($tl->weekdays)) {
                foreach ($tl->weekdays as $w) {
                    if ($w)
                        $days++;
                }
            }
            // Total meals = days * weeks_per_month?
            // "統計總用餐次數" (Total dining times).
            // Does it mean per week or total per semester?
            // Since the table has "金額/日" (Price/Day) and "總金額" (Total Amount),
            // And "總次數" (Total Times).
            // If manual says "次/月" for students, maybe it means monthly billing.
            // For Teachers, "統計總用餐次數" likely means sum of (days_per_week * 4 weeks) for all teachers?
            // Let's assume 4 weeks basis for consistency.

            $teacher_total_days += ($days * $weeks_per_month);
        }

        $teacher_total_amount = $teacher_total_days * $price_per_day;

        return view('exports.lunch_payment_sheet', [
            'regular_rows' => $regular_rows,
            'after_school_rows' => $after_school_rows,
            'weeks_per_month' => '20',
            'price_per_day' => $price_per_day,
            'monthly_price' => $monthly_price,
            'teacher_total_days' => $teacher_total_days,
            'teacher_total_amount' => $teacher_total_amount
        ]);
    }
}
