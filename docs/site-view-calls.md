# مرجع استدعاء بيانات الصفحات

المتغيرات دي جاهزة في البليد. النص المترجم (`title`، `name`، `sub_title`...) بيرجع لغة الصفحة الحالية من غير ما تختار `ar` أو `en`.

`{{ }}` يطبع نص فقط. لو القيمة مصفوفة، اطبع العنصر: `{{ $item['lines'][0] }}`.

## على كل صفحات الموقع

بتتجاب من `SiteLayoutComposer` لأي فيو تحت `layouts.site` أو `site.*`.

| المتغير | الاستدعاء |
|---|---|
| `$siteLogoUrl` | `{{ $siteLogoUrl }}` صورة اللوجو، ولو مش موجودة في الداشبورد ترجع `assets/images/logo/logo-clear.png` |
| `$siteDisplayName` | `{{ $siteDisplayName }}` |
| `$siteSetting` | موديل الإعدادات. النص السفلي: `{{ $siteSetting?->footerBody() }}` |
| `$mainMenu` | قائمة النافبار. كل عنصر موديل `Menu` |
| `$footerNavigation` | روابط الفوتر من المنيو |
| `$siteFooter` | مصفوفة جاهزة للفوتر: الشعار والوصف والسوشيال وأعمدة المشاريع والخدمات والمعلومات والتواصل |
| `$switchLocale` | كود اللغة التانية، مثل `en` |
| `$localeSwitchUrl` | `{{ $localeSwitchUrl($switchLocale) }}` |
| `$navbarProductCategories` | أقسام قائمة المنتجات، مصفوفة جاهزة |
| `$mainCategories` | أقسام المنتجات الرئيسية |
| `$productMenu` | نفس أقسام المنيو كموديلات |
| `$mainServices` | الخدمات الجذرية |
| `$mainBranch` | الفرع الرئيسي. العنوان: `{{ $mainBranch?->address }}` |
| `$branches` | كل الفروع |
| `$contactPhones` | مصفوفة هواتف. كل عنصر: `display` و `tel` |
| `$primaryPhone` | أول هاتف، أو `null` |
| `$contactFax` | `{{ $contactFax }}` |
| `$contactAddress` | `{{ $contactAddress }}` |
| `$contactEmail` | `{{ $contactEmail }}` |
| `$whatsappUrl` | `{{ $whatsappUrl }}` |
| `$bookLabel` | نص زر الحجز |
| `$cartCount` | `{{ $cartCount }}` |
| `$turnstileEnabled` | `true` أو `false` |
| `$turnstileSiteKey` | مفتاح Turnstile |
| `$metatags` | وسوم السيو في الهيد |
| `$schema` | سكيما الصفحة |

المنيو:

```blade
@foreach ($mainMenu as $item)
  <a href="{{ $item->href() }}">{{ $item->title }}</a>
  @foreach ($item->children as $child)
    <a href="{{ $child->href() }}">{{ $child->title }}</a>
  @endforeach
@endforeach
```

قائمة المنتجات:

```blade
@foreach ($navbarProductCategories as $category)
  <a href="{{ $category['url'] }}">{{ $category['name'] }}</a>
  <span>{{ $category['count_label'] }}</span>
  @foreach ($category['products'] as $product)
    <a href="{{ $product['url'] }}">{{ $product['name'] }}</a>
  @endforeach
@endforeach
```

الهاتف:

```blade
@if ($primaryPhone)
  <a href="{{ $primaryPhone['tel'] }}">{{ $primaryPhone['display'] }}</a>
@endif
```

## سكشن الداشبورد

لما المتغير يكون موديل `Section`، الحقول المشتركة:

| الاستدعاء | المعنى |
|---|---|
| `{{ $section?->sub_title }}` | العنوان الصغير |
| `{{ $section?->titleLines()[0] ?? '' }}` | أول سطر في العنوان، حقل «العنوان» |
| `{{ $section?->second_title }}` | السطر التاني، حقل «العنوان الثاني» |
| `{{ $section?->titleLines()[1] ?? '' }}` | السطر التاني لو العنوان القديم مكتوب بسطرين في حقل واحد |
| `{!! $section?->titleHtml() !!}` | العنوان بسطوره، آمن للطباعة |
| `{{ $section?->text }}` | النص |
| `{{ $section?->button_text }}` | نص الزر |
| `{{ $section?->button_link_url }}` | رابط الزر |
| `{{ $section?->alt_image }}` | النص البديل للصورة |
| `{{ $section?->imageUrl('studio/about-main.jpg') }}` | صورة السكشن، ولو ناقصة الصورة الثابتة |
| `{{ $section?->statisticParts()['number'] }}` | رقم الإحصائية |
| `{{ $section?->statisticParts()['suffix'] }}` | اللاحقة، مثل `+` |

`?->` لأن السكشن ممكن يكون غير موجود.

## الصفحة الرئيسية

الكونترولر: `HomeController`. الفيو: `site.home.index`.

### `$heroSlides`

مصفوفة شرائح. كل شريحة مصفوفة جاهزة، مش موديل.

```blade
@foreach ($heroSlides as $slide)
  <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}">
  <span>{{ $slide['lines'][0] ?? '' }}</span>
  <span>{{ $slide['lines'][1] ?? '' }}</span>
  <p>{{ $slide['text'] }}</p>
  <a href="{{ $slide['url'] }}">{{ $slide['button'] }}</a>
@endforeach
```

`$slide['lines']` مصفوفة. `$slide['url']` رابط الزر. `$slide['image']` مسار الصورة.

### `$homeAbout`

مصفوفة جاهزة لقسم «عن الشركة».

| المفتاح | الاستدعاء |
|---|---|
| `eyebrow` | `{{ $homeAbout['eyebrow'] }}` |
| `lines` | `{{ $homeAbout['lines'][0] ?? '' }}` ثم `[1]` |
| `text` | `{{ $homeAbout['text'] }}` |
| `button` | `{{ $homeAbout['button'] }}` |
| `url` | `{{ $homeAbout['url'] }}` |
| `images` | `{{ $homeAbout['images'][0] }}` صورة التفاصيل، `[1]` صورة السكشن |
| `alt` | `{{ $homeAbout['alt'] }}` |

### باقي أقسام الرئيسية

| المتغير | النوع | الاستدعاء |
|---|---|---|
| `$homeServicesIntro` | `Section` أو `null` | عنوان قسم الخدمات |
| `$homeServices` | مجموعة `Service` | الخدمات الجذرية |
| `$homeProjectsIntro` | `Section` أو `null` | عنوان الأعمال. السطر الذهبي: `{{ $homeProjectsIntro?->second_title }}` |
| `$homeProjectCategories` | أقسام المشاريع | التبويب: `{{ $category['name'] }}`. الأعمال: `@foreach ($category['projects'] as $project)` والرابط `{{ $project['url'] }}` |
| `$homeSelector` | `Section` أو `null` | قسم «ابدأ مشروعك» |
| `$homeSelectorFeatures` | مجموعة `Section` | خطوات المشروع |
| `$homeBrandsIntro` | `Section` أو `null` | عنوان الشركاء |
| `$homePartners` | مجموعة `Partner` | شركاء الصفحة الرئيسية |
| `$homeWhy` | مصفوفة جاهزة | «لماذا نحن»: `eyebrow` و `lines` و `text` و `benefits` و `stats` |
| `$homeBlogsIntro` | `Section` أو `null` | عنوان المقالات. السطر الذهبي: `{{ $homeBlogsIntro?->titleLines()[0] }}` والسطر الثاني: `{{ $homeBlogsIntro?->second_title }}` |
| `$homeFeaturedArticle` | مصفوفة أو `null` | أحدث مقال: `url` و `image` و `alt` و `date` و `title` و `excerpt` |
| `$homeArticles` | قائمة مصفوفات | باقي المقالات بنفس الحقول، من غير المقال المميز |
| `$homeContact` | مصفوفة جاهزة | التواصل: `eyebrow` و `line` و `second` و `text` و `cards` و `socials` و `form` و `mapHtml` و `mapUrl` |

خدمة:

```blade
@foreach ($homeServices as $service)
  <a href="{{ route('services.show', $service) }}">{{ $service->title }}</a>
  <img src="{{ $service->mainImageUrl() }}" alt="{{ $service->title }}">
  <p>{{ $service->short_text }}</p>
@endforeach
```

شريك:

```blade
@foreach ($homePartners as $partner)
  <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}">
@endforeach
```

مقال:

```blade
@if ($homeFeaturedArticle)
  <a href="{{ $homeFeaturedArticle['url'] }}">{{ $homeFeaturedArticle['title'] }}</a>
  <img src="{{ $homeFeaturedArticle['image'] }}" alt="{{ $homeFeaturedArticle['alt'] }}">
@endif
@foreach ($homeArticles as $article)
  <a href="{{ $article['url'] }}">{{ $article['title'] }}</a>
  <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}">
  <p>{{ $article['excerpt'] }}</p>
@endforeach
```

لماذا نحن:

```blade
{{ $homeWhy['eyebrow'] }}
{{ $homeWhy['lines'][0] ?? '' }}
{{ $homeWhy['lines'][1] ?? '' }}
{{ $homeWhy['text'] }}
@foreach ($homeWhy['benefits'] as $benefit)
  <i class="{{ $benefit['icon'] }}"></i>
  {{ $benefit['title'] }}
  {{ $benefit['text'] }}
@endforeach
@foreach ($homeWhy['stats'] as $stat)
  {{ $stat['suffix'] }}{{ $stat['number'] }}
  {{ $stat['text'] }}
@endforeach
```

## من نحن

الكونترولر: `AboutUsController`. الفيو: `site.about-us.index`.

| المتغير | النوع |
|---|---|
| `$breadcrumb` | `Section` أو `null` |
| `$about_us` | `Section` أو `null` |
| `$statistics` | مجموعة `Section` |
| `$vision` | `Section` أو `null` |
| `$mission` | `Section` أو `null` |
| `$founders` | مصفوفة جاهزة: `name` و`title` و`image` |

```blade
{{ $about_us?->sub_title }}
{{ $about_us?->titleLines()[0] ?? '' }}
{{ $about_us?->titleLines()[1] ?? '' }}
{{ $about_us?->text }}
{{ $about_us?->imageUrl('studio/about-main.jpg') }}
```

## الخدمات

الكونترولر: `ServiceController`.

### القائمة `site.services.index`

| المتغير | الاستدعاء |
|---|---|
| `$services_breadcrumb` | سكشن صورة الخبز |
| `$serviceCards` | كروت جاهزة |

```blade
@foreach ($serviceCards as $card)
  <a href="{{ $card->url }}">{{ $card->title }}</a>
  <span>{{ $card->number }}</span>
  <span>{{ $card->label }}</span>
  <p>{{ $card->excerpt }}</p>
  <img src="{{ $card->imageUrl }}" alt="{{ $card->title }}">
@endforeach
```

### خدمة لها أبناء `site.services.children`

نفس `$services_breadcrumb` و `$serviceCards`، بالإضافة إلى `$service` موديل الخدمة الحالية: `{{ $service->title }}`.

### تفاصيل الخدمة `site.services.show`

| المتغير | الاستدعاء |
|---|---|
| `$service` | `{{ $service->title }}` و `{{ $service->text }}` و `{{ $service->short_text }}` |
| `$services_breadcrumb` | سكشن |
| `$related_services` | مجموعة `Service` |
| `$solutionNumber` | رقم الخدمة، مثل `01` |
| `$galleryImages` | مصفوفة روابط. `{{ $galleryImages[0] }}` |
| `$turnstileEnabled` | من الإعدادات |
| `$turnstileSiteKey` | من الإعدادات |

## المنتجات

الكونترولر: `ProductController`.

### القائمة `site.products.index`

| المتغير | الاستدعاء |
|---|---|
| `$products` | صفحة من المنتجات. `@foreach ($products as $product)` |
| `$products_breadcrumb` | سكشن |
| `$filterCategories` | أقسام الفلتر. `{{ $category->name }}` و `{{ $category->products_count }}` |
| `$allProductsCount` | عدد كل المنتجات |
| `$activeCategoryId` | القسم المفتوح |
| `$activeCategorySlug` | الـ slug الإنجليزي |
| `$brands` | `{{ $brand['label'] }}` و `{{ $brand['value'] }}` و `{{ $brand['count'] }}` |

منتج في القائمة:

```blade
<a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
<img src="{{ $product->mainImageUrl() }}" alt="{{ $product->name }}">
<span>{{ $product->code }}</span>
```

### التفاصيل `site.products.show`

| المتغير | الاستدعاء |
|---|---|
| `$product` | المنتج الحالي |
| `$relatedProducts` | حتى 3 منتجات |
| `$productsBreadcrumb` | سكشن |

## الأقسام

الكونترولر: `CategoryController`. الفيو: `site.categories.index`.

```blade
@foreach ($categories as $category)
  <a href="{{ route('products.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a>
@endforeach
```

صفحة القسم نفسه بتحوّل إلى قائمة المنتجات.

## المشاريع

الكونترولر: `ProjectController`.

### القائمة `site.projects.index`

ألبومات حسب تصنيف المشروع. الاسم والصورة مش بيروحوا لصفحة التفاصيل؛ الصورة بتفتح الألبوم.

```blade
{{ $projects_breadcrumb?->title }}
@foreach ($projectCategories as $category)
  {{ $category['name'] }}
  @foreach ($category['projects'] as $project)
    {{ $project['name'] }}
    @foreach ($project['images'] as $image)
      <img src="{{ $image }}" alt="{{ $project['name'] }}">
    @endforeach
  @endforeach
@endforeach
```

### التفاصيل `site.projects.show`

| المتغير | الاستدعاء |
|---|---|
| `$project` | `{{ $project->title }}` و `{{ $project->text }}` |
| `$industries` | باقي المشاريع للقائمة الجانبية |
| `$industryNumber` | ترتيب المشروع، مثل `01` |
| `$bannerImage` | `{{ $bannerImage }}` |
| `$galleryImages` | مصفوفة روابط |
| `$turnstileEnabled` | من الإعدادات |
| `$turnstileSiteKey` | من الإعدادات |

## الأخبار

الكونترولر: `NewsController`.

### القائمة `site.news.index`

```blade
@foreach ($blogs as $blog)
  <a href="{{ route('news.show', $blog) }}">{{ $blog->title }}</a>
  <p>{{ $blog->excerpt }}</p>
@endforeach
{{ $blogs->links() }}
```

### المقال `site.news.show`

```blade
<h1>{{ $blog->title }}</h1>
{!! $blog->content !!}
@foreach ($latestPosts as $post)
  <a href="{{ route('news.show', $post) }}">{{ $post->title }}</a>
@endforeach
```

## الشركاء

الكونترولر: `PartnerController`. الفيو: `site.partners.index`.

| المتغير | الاستدعاء |
|---|---|
| `$clients_breadcrumb` | سكشن |
| `$sectorGroups` | كل مجموعة: `$group['sector']` و `$group['partners']` |
| `$unassignedPartners` | شركاء من غير قطاع |

```blade
@foreach ($sectorGroups as $group)
  <h2>{{ $group['sector']->getLabel() }}</h2>
  @foreach ($group['partners'] as $partner)
    <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}">
  @endforeach
@endforeach
```

## تواصل معنا

الكونترولر: `ContactUsController`. الفيو: `site.contact-us.index`.

| المتغير | النوع |
|---|---|
| `$breadcrumb` | `Section` أو `null` |
| `$contact_content` | `Section` أو `null`، عنوان ونص الصفحة |
| `$contact_form_section` | `Section` أو `null` |
| `$contactBadges` | مجموعة `Section` |
| `$contactChannelsIntro` | `Section` أو `null` |
| `$contactChannelCards` | مجموعة `Section` مفهرسة برقم الترتيب |
| `$contactWorkingHours` | نص مواعيد الفرع |
| `$contactServices` | خدمات قائمة النموذج |
| `$contactMapSrc` | رابط خريطة جوجل |
| `$mapTitle` | عنوان الخريطة |
| `$turnstileEnabled` | من الإعدادات |
| `$turnstileSiteKey` | من الإعدادات |

صفحة الشكر `site.contact-us.thanks` مالهاش متغيرات خاصة. بتستخدم بيانات اللايوت فقط.

## اختيار الطلمبة

الكونترولر: `PumpSelectorController`. الفيو: `site.pump-selector`.

| المتغير | الاستدعاء |
|---|---|
| `$duty` | نتيجة الحساب، أو `null` قبل الإرسال |
| `$matchedPumps` | منتجات مطابقة |
| `$unratedPumps` | منتجات من غير تقييم |
| `$homeSelector` | سكشن «ابدأ مشروعك» |
| `$homeSelectorFeatures` | خطوات المشروع |

## السلة

الكونترولر: `CartController`. الفيو: `site.cart`.

```blade
@foreach ($lines as $line)
  {{ $line['product']->name }}
  {{ $line['qty'] }}
@endforeach
<span>{{ $units }}</span>
```

## الصفحات الثابتة

الكونترولر: `PageController`.

| الفيو | المتغير |
|---|---|
| `site.pages.privacy-policy` | `$privacyPolicy` سكشن أو `null`. `{{ $privacyPolicy?->title }}` و `{!! $privacyPolicy?->text !!}` |
| `site.pages.terms-and-conditions` | `$termsAndConditions` بنفس الشكل |
