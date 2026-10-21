<?php

namespace App\Services\Marketing;

use App\Models\Blog;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UtmAttribution
{
    public const SESSION_KEY = 'utm_attribution';

    /**
     * @var list<string>
     */
    public const KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * Capture UTM parameters from the current request into the session (last-touch).
     *
     * @return array<string, string> Newly captured UTM values from the query string (empty when none).
     */
    public function capture(Request $request): array
    {
        $captured = [];

        foreach (self::KEYS as $key) {
            $value = $request->query($key);

            if (! is_string($value)) {
                continue;
            }

            $value = Str::limit(trim($value), 191, '');

            if ($value !== '') {
                $captured[$key] = $value;
            }
        }

        if ($captured === []) {
            return [];
        }

        $existing = $this->fromSession($request);

        $attribution = array_merge($existing, $captured);
        $attribution['landing_page'] = Str::limit($request->fullUrl(), 500, '');

        $referrer = $request->headers->get('referer');
        if (filled($referrer)) {
            $attribution['referrer'] = Str::limit($referrer, 500, '');
        } elseif (filled($existing['referrer'] ?? null)) {
            $attribution['referrer'] = $existing['referrer'];
        }

        $request->session()->put(self::SESSION_KEY, $attribution);

        return $captured;
    }

    /**
     * @return array{
     *     utm_source?: string,
     *     utm_medium?: string,
     *     utm_campaign?: string,
     *     utm_term?: string,
     *     utm_content?: string,
     *     landing_page?: string,
     *     referrer?: string
     * }
     */
    public function fromSession(?Request $request = null): array
    {
        $request ??= request();

        if (! $request->hasSession()) {
            return [];
        }

        $stored = $request->session()->get(self::SESSION_KEY, []);

        return is_array($stored) ? $stored : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function mergeInto(array $payload, ?Request $request = null): array
    {
        $request ??= request();
        $attribution = $this->fromSession($request);

        foreach ([...self::KEYS, 'landing_page', 'referrer'] as $key) {
            if (filled($attribution[$key] ?? null)) {
                $payload[$key] = $attribution[$key];

                continue;
            }

            $fromRequest = $this->sanitize($request->input($key));
            if ($fromRequest !== null) {
                $payload[$key] = $fromRequest;
            }
        }

        return $payload;
    }

    public function sanitize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = Str::limit(trim($value), 500, '');

        return $value !== '' ? $value : null;
    }

    public function platformLabel(?string $source): ?string
    {
        if (blank($source)) {
            return null;
        }

        $normalized = Str::lower(trim($source));

        return match (true) {
            in_array($normalized, ['facebook', 'fb', 'fbads', 'meta'], true) => 'Facebook',
            in_array($normalized, ['instagram', 'ig', 'igads'], true) => 'Instagram',
            in_array($normalized, ['tiktok', 'tt', 'tiktokads'], true) => 'TikTok',
            in_array($normalized, ['google', 'googleads', 'gads', 'adwords', 'cpc'], true) => 'Google',
            in_array($normalized, ['youtube', 'yt'], true) => 'YouTube',
            in_array($normalized, ['linkedin', 'li'], true) => 'LinkedIn',
            in_array($normalized, ['twitter', 'x'], true) => 'X / Twitter',
            in_array($normalized, ['snapchat', 'snap'], true) => 'Snapchat',
            in_array($normalized, ['whatsapp', 'wa'], true) => 'WhatsApp',
            default => Str::headline($source),
        };
    }

    /**
     * @return array<string, string>
     */
    public function platformOptions(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'snapchat' => 'Snapchat',
            'google' => 'Google',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            'twitter' => 'X / Twitter',
            'whatsapp' => 'WhatsApp',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function destinationOptions(): array
    {
        return [
            'home' => __('admin_trans.utm_destination_home'),
            'contact-us' => __('admin_trans.utm_destination_contact'),
            'about-us' => __('admin_trans.utm_destination_about'),
            'services.index' => __('admin_trans.utm_destination_services'),
            'service' => __('admin_trans.utm_destination_service_item'),
            'products.index' => __('admin_trans.utm_destination_products'),
            'product' => __('admin_trans.utm_destination_product_item'),
            'blogs' => __('admin_trans.utm_destination_blogs'),
            'blog' => __('admin_trans.utm_destination_blog_item'),
            'projects.index' => __('admin_trans.utm_destination_projects'),
            'project' => __('admin_trans.utm_destination_project_item'),
        ];
    }

    /**
     * @return list<string>
     */
    public function destinationsRequiringRecord(): array
    {
        return ['service', 'product', 'blog', 'project'];
    }

    public function buildCampaignUrl(
        string $destination,
        string $source,
        ?string $medium = null,
        ?string $campaign = null,
        ?string $term = null,
        ?string $content = null,
        int|string|null $recordId = null,
    ): string {
        $path = $this->resolveDestinationPath($destination, $recordId);

        $query = array_filter([
            'utm_source' => $this->sanitize($source),
            'utm_medium' => $this->sanitize($medium),
            'utm_campaign' => $this->sanitize($campaign),
            'utm_term' => $this->sanitize($term),
            'utm_content' => $this->sanitize($content),
        ], fn (?string $value): bool => filled($value));

        $base = rtrim((string) config('app.url'), '/').($path === '/' ? '' : $path);

        if ($query === []) {
            return $base !== '' ? $base : rtrim((string) config('app.url'), '/');
        }

        return $base.'?'.http_build_query($query);
    }

    public function resolveDestinationPath(string $destination, int|string|null $recordId = null): string
    {
        return match ($destination) {
            'home' => '/',
            'contact-us' => '/contact-us',
            'about-us' => '/about-us',
            'services.index' => '/services',
            'service' => $this->entityPath(Service::class, $recordId, 'services'),
            'products.index' => '/products',
            'product' => $this->entityPath(Product::class, $recordId, 'products'),
            'blogs' => '/blogs',
            'blog' => $this->entityPath(Blog::class, $recordId, 'blogs'),
            'projects.index' => '/projects',
            'project' => $this->entityPath(Project::class, $recordId, 'projects'),
            default => '/contact-us',
        };
    }

    protected function entityPath(string $modelClass, int|string|null $recordId, string $prefix): string
    {
        if (blank($recordId)) {
            return '/'.$prefix;
        }

        /** @var Model|null $record */
        $record = $modelClass::query()->find($recordId);

        if ($record === null) {
            return '/'.$prefix;
        }

        $slug = '';

        if (method_exists($record, 'getTranslation')) {
            $locale = config('app.locale', 'en');
            $slug = (string) $record->getTranslation('slug', $locale, false);

            if ($slug === '' && method_exists($record, 'getTranslations')) {
                $all = $record->getTranslations('slug');
                $slug = (string) (reset($all) ?: '');
            }
        }

        $slug = trim($slug);

        return $slug === '' ? '/'.$prefix : '/'.$prefix.'/'.$slug;
    }
}
