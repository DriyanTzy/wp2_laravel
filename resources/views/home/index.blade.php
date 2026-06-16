@extends('layouts.app')

@section('content')
<div style="max-width: 672px; margin-left: auto; margin-right: auto;">

    <!-- Search User -->
    <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 16px; margin-bottom: 16px; position: relative;">
        <div style="position: relative;">
            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;">🔍</span>
            <input type="text" id="searchUser" placeholder="Cari user (min 2 karakter)..." 
                   style="width: 100%; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; font-size: 0.95rem;">
            <div id="searchResults" style="position: absolute; z-index: 10; background: #fff; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; margin-top: 4px; display: none;"></div>
        </div>
    </div>

    <!-- Form Post Baru -->
    <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 16px; margin-bottom: 16px;">
        <h3 style="font-weight: 600; margin-bottom: 8px; color: #111;">Buat Postingan Baru</h3>
        <input type="text" id="postTitle" placeholder="Judul" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; margin-bottom: 8px; font-size: 0.95rem;">
        <textarea id="postContent" rows="2" placeholder="Ceritakan tentang dataset atau survei..." style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; margin-bottom: 8px; font-family: inherit; font-size: 0.95rem;"></textarea>
        <input type="url" id="postLink" placeholder="Link survei (opsional)" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; margin-bottom: 8px; font-size: 0.95rem;">
        <button id="btnPost" style="background: #2563eb; color: #fff; padding: 8px 16px; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">Posting</button>
        <div id="postMessage" style="font-size: 0.9rem; margin-top: 8px; display: none;"></div>
    </div>

    <!-- Tabs -->
    <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="display: flex; border-bottom: 1px solid #e5e7eb;">
            <button class="tab-btn" data-tab="posts" style="flex: 1; padding: 12px; font-weight: 600; background: none; border: none; cursor: pointer; color: #2563eb; border-bottom: 2px solid #2563eb; font-size: 0.95rem;">Postingan</button>
            <button class="tab-btn" data-tab="datasets" style="flex: 1; padding: 12px; font-weight: 600; background: none; border: none; cursor: pointer; color: #6b7280; border-bottom: 2px solid transparent; font-size: 0.95rem;">Dataset</button>
        </div>
        <div id="tabContent" style="padding: 16px;">
            <div style="text-align: center; padding: 40px 0; color: #9ca3af;">Memuat...</div>
        </div>
    </div>
</div>

<script>
    // ─── Semua JavaScript tetap sama ──────────────────────────────
    let activeTab = 'posts';
    const searchInput = document.getElementById('searchUser');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`/api/users/search?q=${encodeURIComponent(query)}`);
                const json = await res.json();
                if (json.users && json.users.length) {
                    let html = '<ul style="list-style:none;margin:0;padding:0;border-top:1px solid #e5e7eb;">';
                    json.users.forEach(u => {
                        html += `<li style="padding:8px 12px;cursor:pointer;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f3f4f6;" data-username="${u.username}">
                                    <img src="${u.photo ? '/storage/'+u.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(u.name)}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                    <div><div style="font-weight:600;font-size:0.95rem;">${escapeHtml(u.name)}</div><div style="font-size:0.75rem;color:#6b7280;">@${u.username}</div></div>
                                </li>`;
                    });
                    html += '</ul>';
                    searchResults.innerHTML = html;
                    searchResults.style.display = 'block';

                    document.querySelectorAll('#searchResults li').forEach(li => {
                        li.addEventListener('click', () => {
                            window.location.href = `/profile/${li.dataset.username}`;
                        });
                    });
                } else {
                    searchResults.style.display = 'none';
                }
            } catch (err) {
                console.error(err);
            }
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

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
            return '<div style="text-align:center;padding:40px 0;color:#6b7280;">Belum ada postingan. Jadi yang pertama!</div>';
        }
        let html = '';
        posts.forEach(post => {
            const user = post.user || { name: 'User', username: 'user', photo: null };
            const date = new Date(post.created_at).toLocaleString();
            html += `
                <div style="border-bottom:1px solid #e5e7eb;padding-bottom:16px;margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <img src="${user.photo ? '/storage/'+user.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <div>
                            <a href="/profile/${user.username}" style="font-weight:600;color:#111;text-decoration:none;">${escapeHtml(user.name)}</a>
                            <div style="font-size:0.75rem;color:#9ca3af;">${date}</div>
                        </div>
                    </div>
                    <h4 style="font-weight:700;color:#111;margin:4px 0;">${escapeHtml(post.title)}</h4>
                    <p style="color:#374151;margin:4px 0;">${escapeHtml(post.content) || ''}</p>
                    ${post.survey_link ? `<a href="${post.survey_link}" target="_blank" style="color:#2563eb;font-size:0.9rem;display:inline-block;margin-top:4px;">🔗 Link Survei</a>` : ''}
                    <div style="display:flex;gap:16px;margin-top:8px;font-size:0.9rem;color:#6b7280;">
                        <button class="like-btn" data-id="${post.id}" style="background:none;border:none;cursor:pointer;color:#6b7280;">❤️ ${post.likes_count || 0} suka</button>
                        <button style="background:none;border:none;cursor:pointer;color:#6b7280;">💬 ${post.comments_count || 0} komentar</button>
                        <button style="background:none;border:none;cursor:pointer;color:#6b7280;">🔁 ${post.shares_count || 0} bagikan</button>
                    </div>
                </div>
            `;
        });
        return html;
    }

    function renderDatasets(datasets) {
        if (!datasets.length) {
            return '<div style="text-align:center;padding:40px 0;color:#6b7280;">Belum ada dataset yang diunggah.</div>';
        }
        let html = '';
        datasets.forEach(ds => {
            const user = ds.user || { name: 'User', username: 'user' };
            html += `
                <div style="border-bottom:1px solid #e5e7eb;padding-bottom:16px;margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <div style="width:40px;height:40px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📊</div>
                        <div>
                            <a href="/datasets/${ds.id}" style="font-weight:600;color:#111;text-decoration:none;">${escapeHtml(ds.title)}</a>
                            <div style="font-size:0.75rem;color:#6b7280;">${ds.class || ''} ${ds.present_count ? '• ' + ds.present_count + ' akses' : ''}</div>
                        </div>
                    </div>
                    <p style="color:#4b5563;font-size:0.9rem;">${escapeHtml(ds.description) || 'Tidak ada deskripsi'}</p>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                        <span style="font-size:0.75rem;color:#6b7280;">Diunggah oleh <a href="/profile/${user.username}" style="color:#2563eb;text-decoration:none;">${escapeHtml(user.name)}</a> • ${new Date(ds.created_at).toLocaleDateString()}</span>
                        <a href="/datasets/${ds.id}" style="background:#eff6ff;color:#2563eb;padding:4px 12px;border-radius:4px;font-size:0.85rem;text-decoration:none;">Lihat</a>
                    </div>
                </div>
            `;
        });
        return html;
    }

    async function switchTab(tab) {
        activeTab = tab;
        const container = document.getElementById('tabContent');
        container.innerHTML = '<div style="text-align:center;padding:40px 0;color:#6b7280;">Memuat...</div>';
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
                msgDiv.style.display = 'block';
                msgDiv.style.color = '#16a34a';
                setTimeout(() => msgDiv.style.display = 'none', 3000);
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

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.color = '#6b7280';
                b.style.borderBottom = '2px solid transparent';
            });
            btn.style.color = '#2563eb';
            btn.style.borderBottom = '2px solid #2563eb';
            switchTab(tab);
        });
    });

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