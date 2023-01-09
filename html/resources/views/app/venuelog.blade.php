<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">預約者：</span>
    {{ $reserve->subscriber->realname }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">預約場地（設備）：</span>
    {{ $reserve->venue->name }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">預約日期：</span>
    {{ $reserve->reserved_at->format('Y-m-d') }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">預約節次：</span>
    {{ $reserve->timesection }}
</div>
<div class="p-2">
    <span class="text-indigo-700 dark:text-indigo-200">用途：</span>
    {{ $reserve->reason }}
</div>
