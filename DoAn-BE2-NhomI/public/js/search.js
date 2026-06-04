// public/js/search.js
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const searchContainer = document.getElementById('search-container');

    // Hàm lưu lịch sử (giữ nguyên logic cũ nhưng thêm kiểm tra an toàn)
    function saveSearchHistory(query) {
        if (!query || typeof query !== 'string' || query.trim().length < 2) return;
        
        let history = JSON.parse(localStorage.getItem('b_tris_history')) || [];
        const cleanQuery = query.trim();
        
        history = history.filter(item => item !== cleanQuery);
        history.unshift(cleanQuery);
        if (history.length > 5) history.pop();
        
        localStorage.setItem('b_tris_history', JSON.stringify(history));
    }

    function renderSearchHistory() {
        const history = JSON.parse(localStorage.getItem('b_tris_history')) || [];
        if (history.length > 0 && searchResults) {
            let html = `
                <div class="flex items-center justify-between p-4 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tìm kiếm gần đây</span>
                    <button id="clear-history" class="text-xs text-red-500 hover:underline">Xóa tất cả</button>
                </div>`;
            
            history.forEach(item => {
                html += `
                    <div class="flex items-center gap-4 p-4 hover:bg-slate-50 cursor-pointer group history-item" data-query="${item}">
                        <span class="material-symbols-outlined text-slate-300 text-sm">history</span>
                        <p class="text-sm text-slate-600 flex-1">${item}</p>
                    </div>`;
            });
            searchResults.innerHTML = html;
            searchResults.classList.remove('hidden');
        }
    }

    if (searchInput && searchResults) {
        // Fix lỗi 'trim' bằng cách dùng searchInput.value và kiểm tra null
        searchInput.addEventListener('focus', function() {
            const val = searchInput.value || "";
            if (val.trim() === '') {
                renderSearchHistory();
            }
        });

        searchInput.addEventListener('input', function () {
            const val = searchInput.value || "";
            const query = val.trim();

            if (query === '') {
                renderSearchHistory();
                return;
            }

            if (query.length >= 2) {
                fetch(`/search-ajax?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data && data.length > 0) {
                            searchResults.classList.remove('hidden');
                            data.forEach(product => {
                                searchResults.innerHTML += `
                                    <a href="/product/${product.product_id}" class="flex items-center gap-4 p-4 hover:bg-slate-100 border-b border-slate-50 group search-link" data-name="${product.name}">
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-brand-blue">${product.name}</p>
                                            <p class="text-xs text-brand-blue font-black">${new Intl.NumberFormat('vi-VN').format(product.base_price)}₫</p>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                                    </a>`;
                            });
                        } else {
                            searchResults.innerHTML = '<div class="p-4 text-sm text-slate-500">Không tìm thấy sản phẩm...</div>';
                            searchResults.classList.remove('hidden');
                        }
                    })
                    .catch(err => console.error("Lỗi fetch:", err));
            } else {
                searchResults.classList.add('hidden');
            }
        });
    }

    // Xử lý click
    document.addEventListener('click', function (e) {
        if (!e.target) return;

        const historyItem = e.target.closest('.history-item');
        if (historyItem) {
            const query = historyItem.dataset.query;
            searchInput.value = query;
            searchInput.dispatchEvent(new Event('input'));
            return;
        }

        const searchLink = e.target.closest('.search-link');
        if (searchLink) {
            saveSearchHistory(searchLink.dataset.name);
        }

        if (e.target.id === 'clear-history') {
            localStorage.removeItem('b_tris_history');
            searchResults.classList.add('hidden');
        }

        if (searchContainer && !searchContainer.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});