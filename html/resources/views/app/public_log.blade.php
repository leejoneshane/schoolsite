<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">授課時間：</span>
    {{ $public->timeperiod }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">週節次：</span>
    {{ $public->week_session }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">教學領域：</span>
    {{ $public->domain->name }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">單元名稱：</span>
    {{ $public->teach_unit }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">授課教師：</span>
    {{ $public->teacher->realname }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">授課班級：</span>
    {{ is_null($public->teach_class) ? '特殊需求' : $public->classroom->name }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">授課地點：</span>
    {{ $public->location }}
</div>