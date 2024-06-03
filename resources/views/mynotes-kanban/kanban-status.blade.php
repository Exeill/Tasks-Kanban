@props(['status'])

<div class="">
    @include(static::$headerView)
    <div
data-status-id="{{ $status['id'] }}"
class="flex flex-wrap gap-x-2 gap-y-3 mb-7 mt-7" 
>



@foreach($status['records'] as $record)

    @include(static::$recordView)
@endforeach
</div>
</div>
