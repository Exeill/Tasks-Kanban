<div id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record flex flex-col {{ $record->bg_color }} dark:bg-gray-700 rounded-md p-4 cursor-grab  dark:text-gray-200 border-l-8 shadow-md border-slate-300 
    {{ $record->text_color }} justify-between
    min-w-[200px] min-h-[180px] max-w-[300px] max-h-[240px] flex-1"
    @if ($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3) x-data x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('{{ $record->bg_color }}', 'dark:bg-gray-700')
            }, 3000)
        " @endif>

        <div class="font-semibold text-lg ">
            {{ $record->{static::$recordTitleAttribute} }}          
        </div>

        <div class="text-balance font-light text-xs ">
            {{ $record->getTrim() ?? 'No Description'}}         
        </div>
        
        @if($record->status)
        <div class="flex flex-row justify-between mb-2">
            <div class="text-balance font-light text-[10px] border rounded-md p-1">
                {{ $record->status }}          
            </div>
        </div>
        @endif

<div class="flex flex-row gap-1">
    <div class="font-light text-[10px] ">
    Created {{ $record->created_at->diffForHumans() }}
    </div>

    {{-- <div class="font-light text-[10px] ">
    Updated {{ $record->updated_at->diffForHumans() }}
    </div> --}}
</div>
    
</div>
