{{-- Quote forms: AJAX submit + Bootstrap toast (uses SiteAjax). Falls back to a normal POST without JS. --}}
<script>
    (function () {
        if (typeof SiteAjax === 'undefined') {
            return;
        }
        document.querySelectorAll('[data-quote-form]').forEach(function (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                }
                try {
                    var result = await SiteAjax.postForm(form);
                    var data = result.data || {};
                    if (!result.response.ok) {
                        var errText =
                            SiteAjax.firstValidationError(data.errors) ||
                            (data && data.message) ||
                            SiteAjax.ajaxFailed;
                        SiteAjax.showToast(errText, 'danger');
                        return;
                    }
                    SiteAjax.showToast(data.message || SiteAjax.ajaxFailed, 'success');
                    form.reset();
                    if (window.turnstile) {
                        window.turnstile.reset();
                    }
                } catch (err) {
                    SiteAjax.showToast(SiteAjax.ajaxFailed, 'danger');
                } finally {
                    if (btn) {
                        btn.disabled = false;
                    }
                }
            });
        });
    })();
</script>
