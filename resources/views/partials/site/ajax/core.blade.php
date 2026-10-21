{{-- Site-wide AJAX helpers: reuse from any Blade partial (newsletter, forms, …). --}}
SiteAjax.ajaxFailed = @json(__('site.ajax_request_failed'));

SiteAjax.getCsrfToken = function () {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

/**
 * First message from Laravel validation JSON { errors: { field: ["…"] } }.
 */
SiteAjax.firstValidationError = function (errors) {
    if (!errors || typeof errors !== 'object') {
        return null;
    }
    var keys = Object.keys(errors);
    for (var i = 0; i < keys.length; i++) {
        var arr = errors[keys[i]];
        if (Array.isArray(arr) && arr[0]) {
            return arr[0];
        }
    }
    return null;
};

/**
 * Bootstrap 5 toast (top-right stack, 5s autohide).
 */
SiteAjax.queueToastForReload = function (message, variant) {
    if (!message) {
        return;
    }
    try {
        sessionStorage.setItem(
            'site-ajax-pending-toast',
            JSON.stringify({ message: message, variant: variant || 'success' })
        );
    } catch (e) {}
};

SiteAjax.flushPendingToast = function () {
    try {
        var raw = sessionStorage.getItem('site-ajax-pending-toast');
        if (!raw) {
            return;
        }
        sessionStorage.removeItem('site-ajax-pending-toast');
        var payload = JSON.parse(raw);
        if (payload && payload.message) {
            SiteAjax.showToast(payload.message, payload.variant || 'success');
        }
    } catch (e) {}
};

SiteAjax.showToast = function (message, variant) {
    variant = variant || 'success';
    var host = document.getElementById('site-ajax-toast-host');
    if (!host || !window.bootstrap || !window.bootstrap.Toast) {
        return;
    }
    var bg = variant === 'danger' ? 'danger' : 'success';
    var el = document.createElement('div');
    el.className =
        'toast align-items-center text-bg-' + bg + ' border-0 mb-2';
    el.setAttribute('role', 'status');
    var flex = document.createElement('div');
    flex.className = 'd-flex w-100';
    var body = document.createElement('div');
    body.className = 'toast-body';
    body.textContent = message;
    var close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close btn-close-white me-2 m-auto';
    close.setAttribute('data-bs-dismiss', 'toast');
    close.setAttribute('aria-label', 'Close');
    flex.appendChild(body);
    flex.appendChild(close);
    el.appendChild(flex);
    host.appendChild(el);
    var toast = new window.bootstrap.Toast(el, { autohide: true, delay: 5000 });
    el.addEventListener('hidden.bs.toast', function () {
        el.remove();
    });
    toast.show();
};

/**
 * POST a standard Laravel form (multipart or urlencoded) and parse JSON.
 * Returns Promise with { response, data }.
 */
SiteAjax.postForm = async function (form) {
    var fd = new FormData(form);
    var response = await fetch(form.action, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': SiteAjax.getCsrfToken(),
        },
        body: fd,
        credentials: 'same-origin',
    });
    var data = {};
    try {
        data = await response.json();
    } catch (e) {
        data = {};
    }
    return { response: response, data: data };
};

/**
 * JSON request with custom URL/body (e.g. future APIs).
 * url: string; options: { method, body, headers }.
 */
SiteAjax.request = async function (url, options) {
    options = options || {};
    var headers = Object.assign(
        {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': SiteAjax.getCsrfToken(),
        },
        options.headers || {}
    );
    var init = {
        method: options.method || 'GET',
        headers: headers,
        credentials: 'same-origin',
    };
    if (options.body !== undefined && options.body !== null) {
        init.body = options.body;
    }
    var response = await fetch(url, init);
    var data = {};
    try {
        data = await response.json();
    } catch (e) {
        data = {};
    }
    return { response: response, data: data };
};

/**
 * POST JSON body (e.g. small API actions). Sends Content-Type: application/json.
 */
SiteAjax.postJson = async function (url, payload) {
    return await SiteAjax.request(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload || {}),
    });
};
