
@props(['status'])

<div class="max-w-[500px] flex-1">
    @include(static::$headerView)
    <div data-status-id="{{ $status['id'] }}" class="flex-wrap gap-x-2 gap-y-3 mb-7 mt-7">



        @foreach ($status['records'] as $record)
            @include(static::$recordView)
        @endforeach
    </div>
</div>
