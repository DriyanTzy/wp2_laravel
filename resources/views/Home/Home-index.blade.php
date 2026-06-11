@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Search User -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4 relative">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchUser" placeholder="Cari user (min 2 karakter)..." 
                   class="w-full pl-10 pr-4 py-2 border rounded-lg">
            <div id="searchResults" class="absolute z-10 bg-white border rounded-lg shadow-lg w-full mt-1 hidden"></div>
        </div>
    </div>

    <!-- Form Post Baru -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <h3 class="font-semibold mb-2">Buat Postingan Baru</h3>
        <input type="text" id="postTitle" placeholder="Judul" class="w-full border rounded-lg p-2 mb-2">
        <textarea id="postContent" rows="2" placeholder="Ceritakan tentang dataset atau survei..." class="w-full border rounded-lg p-2 mb-2"></textarea>
        <input type="url" id="postLink" placeholder="Link survei (opsional)" class="w-full border rounded-lg p-2 mb-2">
        <button id="btnPost" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Posting</button>
        <div id="postMessage" class="text-sm mt-2 hidden"></div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex border-b">
            <button class="tab-btn flex-1 py-3 font-semibold text-blue-600 border-b-2 border-blue-600" data-tab="posts">Postingan</button>
            <button class="tab-btn flex-1 py-3 font-semibold text-gray-500" data-tab="datasets">Dataset</button>
        </div>
        <div id="tabContent" class="p-4">
            <div class="text-center py-10">Memuat...</div>
        </div>
    </div>
</div>

<script>
    let activeTab = 'posts';

    // ─── Search User ───────────────────────────────────────────────
    const searchInput = document.getElementById('searchUser');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`/api/users/search?q=${encodeURIComponent(query)}`);
                const json = await res.json();
                if (json.users && json.users.length) {
                    let html = '<ul class="divide-y">';
                    json.users.forEach(u => {
                        html += `<li class="p-2 hover:bg-gray-100 cursor-pointer flex items-center gap-3" data-username="${u.username}">
                                    <img src="${u.photo ? '/storage/'+u.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(u.name)}" class="w-8 h-8 rounded-full">
                                    <div><div class="font-semibold">${escapeHtml(u.name)}</div><div class="text-xs text-gray-500">@${u.username}</div></div>
                                </li>`;
                    });
                    html += '</ul>';
                    searchResults.innerHTML = html;
                    searchResults.classList.remove('hidden');

                    document.querySelectorAll('#searchResults li').forEach(li => {
                        li.addEventListener('click', () => {
                            window.location.href = `/profile/${li.dataset.username}`;
                        });
                    });
                } else {
                    searchResults.classList.add('hidden');
                }
            } catch (err) {
                console.error(err);
            }
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // ─── Load Posts Feed ─────────────────────────────────────────
    async function loadPosts() {
        try {
            const res = await fetch('/api/posts/feed');
            const json = await res.json();
            return json.posts || [];
        } catch (err) {
            console.error(err);
            return [];
        }
    }

    // Load Datasets Feed (sudah ada di API /api/datasets)
    async function loadDatasets() {
        try {
            const res = await fetch('/api/datasets');
            const json = await res.json();
            return json.datasets || [];
        } catch (err) {
            console.error(err);
            return [];
        }
    }

    function renderPosts(posts) {
        if (!posts.length) {
            return '<div class="text-center py-10 text-gray-500">Belum ada postingan. Jadi yang pertama!</div>';
        }
        let html = '';
        posts.forEach(post => {
            const user = post.user;
            const date = new Date(post.created_at).toLocaleString();
            html += `
                <div class="border-b pb-4 mb-4 last:border-0">
                    <div class="flex items-center gap-3 mb-2">
                        <img src="${user.photo ? '/storage/'+user.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)}" class="w-10 h-10 rounded-full">
                        <div>
                            <a href="/profile/${user.username}" class="font-semibold hover:underline">${escapeHtml(user.name)}</a>
                            <div class="text-xs text-gray-400">${date}</div>
                        </div>
                    </div>
                    <h4 class="font-bold text-gray-800">${escapeHtml(post.title)}</h4>
                    <p class="text-gray-700 mt-1">${escapeHtml(post.content) || ''}</p>
                    ${post.survey_link ? `<a href="${post.survey_link}" target="_blank" class="text-blue-500 text-sm mt-1 inline-block">🔗 Link Survei</a>` : ''}
                    <div class="flex gap-4 mt-2 text-sm text-gray-500">
                        <button class="like-btn" data-id="${post.id}">❤️ ${post.likes_count || 0} suka</button>
                        <button>💬 ${post.comments_count || 0} komentar</button>
                        <button>🔁 ${post.shares_count || 0} bagikan</button>
                    </div>
                </div>
            `;
        });
        return html;
    }

    function renderDatasets(datasets) {
        if (!datasets.length) {
            return '<div class="text-center py-10 text-gray-500">Belum ada dataset yang diunggah.</div>';
        }
        let html = '';
        datasets.forEach(ds => {
            const user = ds.user;
            html += `
                <div class="border-b pb-4 mb-4 last:border-0">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">📊</div>
                        <div>
                            <a href="/datasets/${ds.id}" class="font-semibold hover:underline">${escapeHtml(ds.title)}</a>
                            <div class="text-xs text-gray-400">${ds.class} • ${ds.present_count || 0} akses</div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm">${escapeHtml(ds.description) || 'Tidak ada deskripsi'}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-400">Diunggah oleh <a href="/profile/${user.username}" class="text-blue-500">${escapeHtml(user.name)}</a> • ${new Date(ds.created_at).toLocaleDateString()}</span>
                        <a href="/datasets/${ds.id}" class="bg-blue-50 text-blue-600 px-3 py-1 rounded text-sm">Lihat</a>
                    </div>
                </div>
            `;
        });
        return html;
    }

    async function switchTab(tab) {
        activeTab = tab;
        const container = document.getElementById('tabContent');
        container.innerHTML = '<div class="text-center py-10">Memuat...</div>';
        if (tab === 'posts') {
            const posts = await loadPosts();
            container.innerHTML = renderPosts(posts);
            attachLikeEvents();
        } else {
            const datasets = await loadDatasets();
            container.innerHTML = renderDatasets(datasets);
        }
    }

    function attachLikeEvents() {
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.removeEventListener('click', handleLike);
            btn.addEventListener('click', handleLike);
        });
    }

    async function handleLike(e) {
        const postId = e.currentTarget.dataset.id;
        const btn = e.currentTarget;
        try {
            const res = await fetch(`/api/posts/${postId}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            if (res.ok) {
                btn.innerHTML = `❤️ ${data.likes_count} suka`;
                if (data.liked) btn.style.color = '#ef4444';
                else btn.style.color = '';
            }
        } catch (err) {
            console.error(err);
        }
    }

    // ─── Posting baru ─────────────────────────────────────────────
    document.getElementById('btnPost').addEventListener('click', async () => {
        const title = document.getElementById('postTitle').value.trim();
        const content = document.getElementById('postContent').value.trim();
        const survey_link = document.getElementById('postLink').value.trim();

        if (!title) {
            alert('Judul harus diisi');
            return;
        }

        const btn = document.getElementById('btnPost');
        const originalText = btn.innerText;
        btn.innerText = 'Mengirim...';
        btn.disabled = true;

        try {
            const res = await fetch('/api/posts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title, content, survey_link })
            });
            const data = await res.json();
            if (res.ok) {
                document.getElementById('postTitle').value = '';
                document.getElementById('postContent').value = '';
                document.getElementById('postLink').value = '';
                const msgDiv = document.getElementById('postMessage');
                msgDiv.textContent = '✅ Postingan berhasil!';
                msgDiv.classList.remove('hidden');
                setTimeout(() => msgDiv.classList.add('hidden'), 3000);
                if (activeTab === 'posts') await switchTab('posts');
            } else {
                alert(data.message || 'Gagal memposting');
            }
        } catch (err) {
            alert('Terjadi kesalahan');
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    });

    // ─── Tab switching ────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-blue-600', 'border-blue-600');
                b.classList.add('text-gray-500', 'border-transparent');
            });
            btn.classList.add('text-blue-600', 'border-blue-600');
            btn.classList.remove('text-gray-500', 'border-transparent');
            switchTab(tab);
        });
    });

    // Initial load
    switchTab('posts');

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
</script>
@endsection