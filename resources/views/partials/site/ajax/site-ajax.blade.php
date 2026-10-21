{{--
    Global AJAX utilities (window.SiteAjax). Load once after Bootstrap.
    Feature modules: @include this before feature-specific scripts.
    Usage: SiteAjax.postForm(form) | SiteAjax.postJson(url, {}) | SiteAjax.showToast('…', 'success')
--}}
@include('partials.site.ajax.toast-host')
<script>
    (function () {
        'use strict';
        window.SiteAjax = window.SiteAjax || {};
        @include('partials.site.ajax.core')
        SiteAjax.flushPendingToast();
        @if (session('success'))
            SiteAjax.showToast(@json(session('success')), 'success');
        @endif
    })();
</script>
