@extends('layouts.site')

@section('content')
<!--
  ##################################################
  #                   Page Header                  #
  ##################################################
-->
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-2.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">سلة الطلب</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
          <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
          <li class="breadcrumb-item active" aria-current="page">سلة الطلب</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                      Cart                      #
  ##################################################
-->
  <section class="km-cart">
    <div class="container">
      <form class="needs-validation" id="cart-form" action="{{ route('cart.quote') }}" method="post" novalidate>
        @csrf
        <div class="row g-4 g-xl-5">
          <div class="col-lg-8">
            <div class="km-cart-panel" data-anim="up">
              <div class="km-cart-head">
                <h2>منتجاتك <span class="km-cart-count" data-cart-count>{{ $lines->count() }}</span></h2>
                <a href="{{ route('products.index') }}" class="km-cart-continue">
                  <i class="fa-solid fa-arrow-right"></i> متابعة التسوق
                </a>
              </div>

              <ul class="km-cart-list" data-cart-list @if ($lines->isEmpty()) hidden @endif>
                @foreach ($lines as $line)
                  @php
                    $product = $line['product'];
                    $specs = $product->specifications ?? [];
                  @endphp
                  <li class="km-cart-item" data-update-url="{{ route('cart.update', $product) }}" data-remove-url="{{ route('cart.destroy', $product) }}">
                    <a href="{{ route('products.show', $product) }}" class="km-cart-thumb" tabindex="-1" aria-hidden="true">
                      <img src="{{ $product->mainImageUrl() }}" width="600" height="450" alt="" loading="lazy" decoding="async" />
                    </a>
                    <div class="km-cart-info">
                      <span class="km-cart-brand">{{ $product->specText($specs['brand'] ?? '') }}</span>
                      <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                      <span class="km-cart-model" dir="ltr">{{ $product->code }}@if (($model = $product->specText($specs['model'] ?? '')) !== '' && $model !== $product->code) — {{ $model }}@endif</span>
                    </div>
                    <div class="km-qty">
                      <button type="button" class="km-qty-btn" data-qty="-1" aria-label="تقليل الكمية"><i class="fa-solid fa-minus"></i></button>
                      <input type="number" class="km-qty-input" name="items[{{ $product->id }}][qty]" value="{{ $line['qty'] }}" min="1" max="99" aria-label="الكمية" />
                      <button type="button" class="km-qty-btn" data-qty="1" aria-label="زيادة الكمية"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <button type="button" class="km-cart-remove" data-cart-remove aria-label="حذف من السلة"><i class="fa-regular fa-trash-can"></i></button>
                  </li>
                @endforeach
              </ul>

              <div class="km-cart-empty" data-cart-empty @unless ($lines->isEmpty()) hidden @endunless>
                <span class="km-cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></span>
                <h3>سلتك فاضية</h3>
                <p>تصفح المنتجات وأضف اللي محتاجه، وبعدها ابعت طلبك في خطوة واحدة.</p>
                <a href="{{ route('products.index') }}" class="km-btn">
                  <span>تصفح المنتجات</span>
                  <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
                </a>
              </div>

              <ul class="km-cart-perks">
                <li><i class="fa-solid fa-shield-halved"></i> منتجات أصلية بضمان</li>
                <li><i class="fa-solid fa-truck-fast"></i> توصيل لكل المحافظات</li>
                <li><i class="fa-solid fa-headset"></i> دعم فني بعد البيع</li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4">
            <aside class="km-cart-summary" data-anim="up">
              <h2 class="km-filter-title">ملخص الطلب</h2>

              <dl class="km-cart-totals">
                <div><dt>عدد المنتجات</dt><dd data-cart-count>{{ $lines->count() }}</dd></div>
                <div><dt>إجمالي القطع</dt><dd data-cart-units>{{ $units }}</dd></div>
                <div><dt>السعر</dt><dd>حسب عرض السعر</dd></div>
              </dl>

              <div class="km-cart-fields">
                <div>
                  <label for="cart-name" class="km-label">الاسم <span>*</span></label>
                  <input type="text" id="cart-name" name="name" class="km-input" placeholder="اسمك بالكامل" autocomplete="name" required />
                </div>
                <div>
                  <label for="cart-phone" class="km-label">رقم الموبايل <span>*</span></label>
                  <input type="tel" id="cart-phone" name="phone" class="km-input" placeholder="01xxxxxxxxx" autocomplete="tel" required />
                </div>
                <div>
                  <label for="cart-city" class="km-label">المحافظة <span>*</span></label>
                  <select id="cart-city" name="city" class="km-input km-select" required>
                    <option value="" selected disabled>اختر المحافظة</option>
                    <option value="cairo">القاهرة</option>
                    <option value="giza">الجيزة</option>
                    <option value="alex">الإسكندرية</option>
                    <option value="qalyubia">القليوبية</option>
                    <option value="sharqia">الشرقية</option>
                    <option value="other">محافظة أخرى</option>
                  </select>
                </div>
                <div>
                  <label for="cart-notes" class="km-label">ملاحظات</label>
                  <textarea id="cart-notes" name="notes" class="km-input" rows="3" placeholder="عمق البئر، مكان التركيب، أي تفاصيل تانية..."></textarea>
                </div>
              </div>

              <button type="submit" class="km-btn km-btn-block" data-cart-submit>
                <span>إرسال الطلب عبر واتساب</span>
                <span class="km-btn-icon"><i class="fa-brands fa-whatsapp"></i></span>
              </button>
              <p class="km-cart-note"><i class="fa-solid fa-circle-info"></i> هيتواصل معاك فريق المبيعات بعرض السعر النهائي.</p>
            </aside>
          </div>
        </div>
      </form>
    </div>
  </section>

  <!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
