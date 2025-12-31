@if(!$value || !$value->village)
    <span class="italic text-gray-400">No address</span>
@else
    <div class="text-[10px] text-gray-400 mt-1">
        <p><span class="text-orange-400">ភូមិ</span> {{ $value->village->name_kh }}</p>
        <p><span class="text-orange-400">ឃុំ</span> {{ $value->village->commune->name_kh }}</p>
        <p><span class="text-orange-400">ស្រុក</span> {{ $value->village->commune->district->name_kh }}</p>
        <p><span class="text-orange-400">ខេត្ត</span> {{ $value->village->commune->district->province->name_kh }}</p>
    </div>
@endif