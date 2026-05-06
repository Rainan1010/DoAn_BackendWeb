// public/js/search.js
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const searchContainer = document.getElementById('search-container');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value;

            if (query.length >= 2) {
                fetch(`/search-ajax?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            searchResults.classList.remove('hidden');
                            data.forEach(product => {
                                searchResults.innerHTML += `
                                    <a href="/product/${product.product_id}" class="flex items-center gap-4 p-4 hover:bg-slate-100 border-b border-slate-50 transition-colors group">
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-brand-blue">${product.name}</p>
                                            <p class="text-xs text-brand-blue font-black">${new Intl.NumberFormat('vi-VN').format(product.base_price)}₫</p>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                                    </a>
                                `;
                            });
                        } else {
                            searchResults.innerHTML = '<div class="p-4 text-sm text-slate-500">Không tìm thấy sản phẩm...</div>';
                            searchResults.classList.remove('hidden');
                        }
                    });
            } else {
                searchResults.classList.add('hidden');
            }
        });
    }

    // Tắt bảng kết quả khi nhấn ra ngoài
    document.addEventListener('click', function (e) {
        if (searchContainer && !searchContainer.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});