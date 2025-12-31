<div class="text-xs text-gray-500">Year {{ ceil($value / 2) }}</div>
<span class="font-bold">Semester {{ $value % 2 == 0 ? 2 : 1 }}</span>