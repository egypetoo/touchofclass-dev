@php
    $media = $record->getMedia('images');
@endphp
@if($media->isNotEmpty())
    <div class="mb-4">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Existing images') }} ({{ $media->count() }})
        </p>
        <div class="flex flex-wrap gap-2">
            @foreach($media as $item)
                <a href="{{ $item->getUrl() }}" target="_blank" rel="noopener" class="block rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 hover:opacity-90">
                    <img src="{{ $item->getUrl() }}" alt="" class="h-20 w-20 object-cover" />
                </a>
            @endforeach
        </div>
    </div>
@endif
