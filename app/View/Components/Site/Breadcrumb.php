<?php

namespace App\View\Components\Site;

use App\Helper\Path;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumb extends Component
{
    public string $backgroundImage;

    public string $pageTitle;

    public ?string $eyebrow;

    public ?string $subtitle;

    public HtmlString $headingHtml;

    public function __construct(
        ?string $image = null,
        ?string $title = null,
        public ?string $url = null,
        ?string $subtitle = null,
        public ?string $parentTitle = null,
        public ?string $parentUrl = null,
        public ?string $crumbTitle = null,
        ?string $eyebrow = null,
    ) {
        $this->backgroundImage = filled($image)
            ? $image
            : Path::imagesPath('sliders/slider2.jpg');

        $this->pageTitle = filled($title) ? $title : '';
        $this->eyebrow = filled($eyebrow) ? $eyebrow : null;
        $this->subtitle = filled($subtitle) ? $subtitle : null;
        $this->headingHtml = new HtmlString(nl2br(e($this->pageTitle), false));
    }

    public function render(): View
    {
        return view('components.site.breadcrumb');
    }
}
