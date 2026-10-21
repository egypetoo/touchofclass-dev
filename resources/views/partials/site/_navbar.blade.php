<header class="header fixed-top top-0 start-0 end-0 header-main px-md-5 " id="header">
  <nav class="navbar navbar-expand-lg position-relative z-3" aria-label="القائمة الرئيسية">
    <div class="container-fluid px-md-5 px-3 d-flex align-items-center justify-content-between w-100">

      <!-- Logo (Right in RTL) -->
      <a class="navbar-brand py-0 me-0" href="{{ route('home') }}" aria-label="{{ $siteDisplayName }} - الرئيسية">
        <img
          src="{{ $siteLogoUrl }}"
          alt="{{ $siteDisplayName }}"
          class="header-logo"
          width="125"
          height="58"
        />
      </a>

      <!-- Desktop Menu (Center) -->
      <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto text-center align-items-center gap-xl-4 gap-3">
          @foreach ($mainMenu as $item)
            @if ($item->showsProductMenu())
              <li class="nav-item km-nav-dd">
                <a
                  class="nav-link {{ $item->isCurrent() ? 'active' : '' }}"
                  href="{{ $item->href() }}"
                  aria-haspopup="true"
                >{{ $item->title }} <i
                    class="fa-solid fa-angle-down km-nav-caret"
                  ></i></a>
                <ul class="km-dd-menu">
                  @foreach ($navbarProductCategories as $category)
                    <li class="km-dd-item">
                      <a href="{{ $category['url'] }}" data-cat="{{ $category['slug'] }}">
                        <span class="km-dd-text">{{ $category['name'] }}<small>{{ $category['count_label'] }}</small></span>
                        <i class="fa-solid fa-angle-left km-dd-arrow"></i>
                      </a>
                      <ul class="km-dd-sub">
                        @foreach ($category['products'] as $menuProduct)
                          <li><a href="{{ $menuProduct['url'] }}">{{ $menuProduct['name'] }}</a></li>
                        @endforeach
                      </ul>
                    </li>
                  @endforeach
                </ul>
              </li>
            @elseif ($item->children->isNotEmpty())
              <li class="nav-item km-nav-dd">
                <a
                  class="nav-link {{ $item->isCurrent() ? 'active' : '' }}"
                  href="{{ $item->href() }}"
                  aria-haspopup="true"
                >{{ $item->title }} <i
                    class="fa-solid fa-angle-down km-nav-caret"
                  ></i></a>
                <ul class="km-dd-menu">
                  @foreach ($item->children as $child)
                    <li class="km-dd-item">
                      <a href="{{ $child->href() }}">
                        <span class="km-dd-text">{{ $child->title }}</span>
                      </a>
                    </li>
                  @endforeach
                </ul>
              </li>
            @else
              <li class="nav-item">
                <a
                  class="nav-link {{ $item->isCurrent() ? 'active' : '' }}"
                  href="{{ $item->href() }}"
                >{{ $item->title }}</a>
              </li>
            @endif
          @endforeach
        </ul>
      </div>

      <!-- Left Side Actions (Left in RTL) -->
      <div class="header-actions d-none d-lg-flex align-items-center gap-3">
        @if ($switchLocale)
          <a class="header-pill-btn" href="{{ $localeSwitchUrl($switchLocale) }}">
            <span>{{ strtoupper($switchLocale) }}</span>
            <i class="fa-solid fa-globe"></i>
          </a>
        @endif
      </div>

      <!-- Mobile Header Controls -->
      <div class="d-lg-none d-flex align-items-center gap-2">
        @if ($switchLocale)
          <a class="header-pill-btn py-1 px-2" style="font-size: 13px;" href="{{ $localeSwitchUrl($switchLocale) }}">
            <span>{{ strtoupper($switchLocale) }}</span>
            <i class="fa-solid fa-globe"></i>
          </a>
        @endif

        <!-- Mobile Hamburger Toggler -->
        <button
          class="navbar-toggler shadow-none border-0 text-white"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#mobileMenu"
          aria-controls="mobileMenu"
          aria-label="فتح القائمة"
        >
          <i class="fa-solid fa-bars-staggered" style="color: #c6a664; font-size: 24px;"></i>
        </button>
      </div>

    </div>

    <!-- Mobile Offcanvas Menu -->
    <div
      class="offcanvas offcanvas-start d-lg-none pb-5"
      id="mobileMenu"
      tabindex="-1"
      aria-labelledby="mobileMenuLabel"
      data-lenis-prevent
    >
      <div class="offcanvas-header border-bottom border-light border-opacity-10">
        <a class="navbar-brand" href="{{ route('home') }}" id="mobileMenuLabel">
          <img src="{{ $siteLogoUrl }}" alt="{{ $siteDisplayName }}" width="104" height="48" loading="lazy"/>
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
      </div>

      <div class="offcanvas-body">
        <ul class="navbar-nav gap-2">
          @foreach ($mainMenu as $item)
            @if ($item->showsProductMenu())
              <li class="nav-item km-m-dd">
                <button
                  class="nav-link km-m-toggle collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#m-products"
                  aria-expanded="false"
                  aria-controls="m-products"
                >
                  {{ $item->title }} <i class="fa-solid fa-angle-down"></i>
                </button>
                <div class="collapse" id="m-products">
                  <ul class="km-m-menu">
                    @foreach ($navbarProductCategories as $category)
                      <li>
                        <div class="km-m-cat">
                          <a href="{{ $category['url'] }}">
                            {{ $category['name'] }}
                          </a>
                          <button
                            class="collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#m-cat-{{ $category['slug'] }}"
                            aria-expanded="false"
                            aria-controls="m-cat-{{ $category['slug'] }}"
                            aria-label="{{ $category['name'] }}"
                          >
                            <i class="fa-solid fa-plus"></i>
                          </button>
                        </div>
                        <ul class="collapse km-m-sub" id="m-cat-{{ $category['slug'] }}">
                          @foreach ($category['products'] as $menuProduct)
                            <li><a href="{{ $menuProduct['url'] }}">{{ $menuProduct['name'] }}</a></li>
                          @endforeach
                        </ul>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </li>
            @elseif ($item->children->isNotEmpty())
              <li class="nav-item km-m-dd">
                <button
                  class="nav-link km-m-toggle collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#m-menu-{{ $item->id }}"
                  aria-expanded="false"
                  aria-controls="m-menu-{{ $item->id }}"
                >
                  {{ $item->title }} <i class="fa-solid fa-angle-down"></i>
                </button>
                <div class="collapse" id="m-menu-{{ $item->id }}">
                  <ul class="km-m-menu">
                    @foreach ($item->children as $child)
                      <li><a href="{{ $child->href() }}">{{ $child->title }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </li>
            @else
              <li class="nav-item">
                <a
                  class="nav-link {{ $item->isCurrent() ? 'active' : '' }} d-flex align-items-center justify-content-between"
                  href="{{ $item->href() }}"
                >{{ $item->title }}</a>
              </li>
            @endif
          @endforeach
        </ul>

        <!-- Contact Info -->
        <ul class="list-unstyled px-3 py-4 mt-4 rounded-3 list-contact">
          @if ($contactPhones !== [])
            <li class="mb-3 border-bottom pb-3">
              <strong>الهاتف:</strong>
              @foreach ($contactPhones as $phone)
                <a href="{{ $phone['tel'] }}" class="d-block text-decoration-none mt-1" dir="ltr">{{ $phone['display'] }}</a>
              @endforeach
            </li>
          @endif
          @if ($contactEmail)
            <li class="mb-3 border-bottom pb-3">
              <strong>البريد الإلكتروني:</strong>
              <a href="mailto:{{ $contactEmail }}" class="d-block text-decoration-none mt-1">{{ $contactEmail }}</a>
            </li>
          @endif
          @if ($contactAddress)
            <li class="d-grid">
              <strong>العنوان:</strong>
              <span class="mt-1">{{ $contactAddress }}</span>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </nav>
</header>
