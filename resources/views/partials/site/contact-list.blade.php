@php
    $itemClass ??= 'talk-contact-item';
    $listClass ??= 'talk-contact-list';
@endphp

<div class="{{ $listClass }}">
    @if ($contactPhones !== [])
        <a href="tel:{{ $contactPhones[0]['tel'] }}" class="{{ $itemClass }}">
            <i class="fas fa-phone-alt"></i>
            <span dir="ltr">{{ collect($contactPhones)->pluck('display')->implode(' / ') }}</span>
        </a>
    @endif
    @if ($contactFax)
        <div class="{{ $itemClass }}">
            <i class="fas fa-fax"></i>
            <span dir="ltr">{{ $contactFax }}</span>
        </div>
    @endif
    @if ($contactEmail)
        <a href="mailto:{{ $contactEmail }}" class="{{ $itemClass }}">
            <i class="far fa-envelope"></i>
            <span>{{ $contactEmail }}</span>
        </a>
    @endif
    @if ($contactAddress)
        <div class="{{ $itemClass }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ $contactAddress }}</span>
        </div>
    @endif
</div>
