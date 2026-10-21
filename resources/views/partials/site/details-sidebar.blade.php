<aside class="details-sidebar" data-aos="fade-left">
    @if ($sidebarLinks->isNotEmpty())
        <div class="sidebar-card">
            <h4 class="sidebar-title">{{ $sidebarTitle }}</h4>
            <ul class="sidebar-links">
                @foreach ($sidebarLinks as $sidebarLink)
                    <li>
                        <a href="{{ $sidebarLink['url'] }}" class="{{ $sidebarLink['active'] ? 'active' : '' }}">
                            {{ $sidebarLink['title'] }} <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('partials.site.quote-form', ['compact' => true])
</aside>
