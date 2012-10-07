<getTime>
    now = date("%s");
</getTime>
<getTimeStr>
    weekDays = "<?=str_replace(',', '&#10;', $lang->msg('WeekDays'))?>";
    timeStr = getStringArrayAt(weekDays, date("%w")) + " " + date("%d.%m %H:%M");
</getTimeStr>
