(function () {
    'use strict';
    if (typeof window.DealerSiteCore === 'undefined') return;

    const query = new URLSearchParams(window.location.search);
    const keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    const storageKey = 'dealer_campaign_attribution';
    let attribution = {};

    try {
        attribution = JSON.parse(sessionStorage.getItem(storageKey) || '{}');
        keys.forEach((key) => {
            const value = query.get(key);
            if (value) attribution[key] = value;
        });
        if (!attribution.landing_url) attribution.landing_url = window.location.href;
        if (!attribution.referrer && document.referrer) attribution.referrer = new URL(document.referrer).origin;
        sessionStorage.setItem(storageKey, JSON.stringify(attribution));
    } catch (error) {
        attribution = {};
    }

    document.querySelectorAll('[data-dealer-quote-form]').forEach((form) => {
        keys.forEach((key) => {
            const input = form.querySelector('[name="' + key + '"]');
            if (input) input.value = attribution[key] || query.get(key) || '';
        });
        const landing = form.querySelector('[name="landing_url"]');
        const referrer = form.querySelector('[name="referrer"]');
        const source = form.querySelector('[name="source_url"]');
        if (landing) landing.value = attribution.landing_url || window.location.href;
        if (referrer) referrer.value = attribution.referrer || '';
        if (source) source.value = window.location.href;

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            const message = form.querySelector('[data-dealer-form-message]');
            if (!button || !message) return;
            const data = Object.fromEntries(new FormData(form).entries());
            button.disabled = true;
            message.textContent = '';
            message.classList.remove('is-error', 'is-success');
            try {
                const response = await fetch(window.DealerSiteCore.endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || window.DealerSiteCore.errorMessage);
                message.textContent = window.DealerSiteCore.successMessage;
                message.classList.add('is-success');
                form.reset();
            } catch (error) {
                message.textContent = error.message || window.DealerSiteCore.errorMessage;
                message.classList.add('is-error');
            } finally {
                button.disabled = false;
            }
        });
    });
})();
