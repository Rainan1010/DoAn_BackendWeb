(function () {
    const POLL_MS = 3000;
    const SYNC_URL = document.querySelector('meta[name="price-sync-url"]')?.content || '/api/prices/sync';

    const versions = new Map();
    let pollTimer = null;
    let toastTimer = null;

    function formatVnd(amount) {
        const value = Number(amount);
        if (!Number.isFinite(value)) {
            return '0₫';
        }
        return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + '₫';
    }

    function collectProductIds() {
        const ids = new Set();
        document.querySelectorAll('[data-realtime-price][data-product-id]').forEach((el) => {
            const id = parseInt(el.dataset.productId, 10);
            if (id > 0) {
                ids.add(id);
            }
        });
        document.querySelectorAll('[data-realtime-price-root][data-product-id]').forEach((el) => {
            const id = parseInt(el.dataset.productId, 10);
            if (id > 0) {
                ids.add(id);
            }
        });
        return Array.from(ids);
    }

    function showToast(message) {
        let toast = document.getElementById('price-realtime-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'price-realtime-toast';
            toast.className = 'fixed bottom-6 right-6 z-[9999] max-w-sm px-5 py-4 rounded-2xl bg-[#0A2540] text-white text-sm font-bold shadow-2xl border border-white/10 transition-all duration-300 translate-y-4 opacity-0 pointer-events-none';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.remove('translate-y-4', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 4000);
    }

    function flashPrice(el) {
        el.classList.add('ring-2', 'ring-orange-400', 'ring-offset-2', 'rounded');
        setTimeout(() => {
            el.classList.remove('ring-2', 'ring-orange-400', 'ring-offset-2', 'rounded');
        }, 1600);
    }

    function updateListingPrices(productId, data, changed) {
        document.querySelectorAll(`[data-realtime-price][data-product-id="${productId}"]`).forEach((el) => {
            const variantId = el.dataset.variantId ? parseInt(el.dataset.variantId, 10) : null;
            let nextPrice = data.display_price;

            if (variantId && data.variants && data.variants[String(variantId)]) {
                nextPrice = data.variants[String(variantId)].display_price;
            }

            const formatted = formatVnd(nextPrice);
            if (el.textContent.trim() !== formatted) {
                el.textContent = formatted;
                flashPrice(el);
                changed.push(productId);
            }
        });
    }

    function updateProductDetail(productId, data, changed) {
        const root = document.querySelector(`[data-realtime-price-root][data-product-id="${productId}"]`);
        if (!root) {
            return;
        }

        const mainPrice = root.querySelector('#mainPrice');
        const activeVariantBtn = root.querySelector('.variant-btn.bg-blue-900, .variant-btn.border-blue-900');
        const activeVariantId = activeVariantBtn?.dataset?.variantId
            ? String(activeVariantBtn.dataset.variantId)
            : null;

        root.querySelectorAll('.variant-btn[data-variant-id]').forEach((btn) => {
            const variantId = String(btn.dataset.variantId);
            const variantData = data.variants?.[variantId];
            if (!variantData) {
                return;
            }

            const formatted = formatVnd(variantData.display_price);
            btn.dataset.price = formatted;
            btn.dataset.priceValue = String(variantData.display_price);
        });

        let nextMain = data.display_price;
        if (activeVariantId && data.variants?.[activeVariantId]) {
            nextMain = data.variants[activeVariantId].display_price;
        }

        if (mainPrice) {
            const formatted = formatVnd(nextMain);
            if (mainPrice.textContent.trim() !== formatted) {
                mainPrice.textContent = formatted;
                flashPrice(mainPrice);
                changed.push(productId);
            }
        }

        root.querySelectorAll('[data-realtime-price][data-product-id]').forEach((el) => {
            if (el.id === 'mainPrice') {
                return;
            }
            const formatted = formatVnd(nextMain);
            if (el.textContent.trim() !== formatted) {
                el.textContent = formatted;
                flashPrice(el);
            }
        });
    }

    async function syncPrices() {
        const ids = collectProductIds();
        if (!ids.length) {
            return;
        }

        try {
            const response = await fetch(`${SYNC_URL}?ids=${ids.join(',')}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                return;
            }

            const body = await response.json();
            const products = body.products || {};
            const changed = [];

            Object.keys(products).forEach((productId) => {
                const data = products[productId];
                const prevVersion = versions.get(productId);
                const hasChange = prevVersion !== undefined && prevVersion !== data.version;

                updateListingPrices(productId, data, changed);
                updateProductDetail(productId, data, changed);

                versions.set(productId, data.version);
            });

            if (changed.length) {
                showToast('Giá sản phẩm vừa được cập nhật tự động.');
            }
        } catch (error) {
            console.warn('[price-realtime]', error);
        }
    }

    function start() {
        syncPrices();
        pollTimer = setInterval(syncPrices, POLL_MS);
    }

    function stop() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
        } else {
            start();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
