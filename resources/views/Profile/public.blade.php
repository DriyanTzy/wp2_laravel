@extends('layouts.app')

@section('content')
<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .profile-header {
        padding: 30px 32px;
        display: flex;
            align-items: center;
        gap: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #e5e7eb;
    }
    .profile-name {
        font-size: 24px;
        font-weight: 700;
        color: #111;
    }
    .profile-email {
        color: #6b7280;
        font-size: 14px;
    }
    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        padding: 24px 32px;
        background: #f9fafb;
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #111;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
    }
    .profile-tabs {
        display: flex;
        border-bottom: 2px solid #e5e7eb;
        padding: 0 32px;
    }
    .tab-btn {
        padding: 14px 20px;
        font-weight: 600;
        font-size: 14px;
        background: none;
        border: none;
        cursor: pointer;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .tab-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }
    .tab-content {
        padding: 24px 32px 32px;
    }
    .dataset-item {
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .dataset-item:last-child {
        border-bottom: none;
    }
    .dataset-title {
        font-weight: 600;
        font-size: 16px;
        color: #111;
        text-decoration: none;
    }
    .dataset-title:hover {
        color: #2563eb;
    }
    .dataset-meta {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    .dataset-actions {
        display: flex;
        gap: 16px;
        margin-top: 8px;
        font-size: 13px;
        color: #6b7280;
    }
    .post-item {
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .post-item:last-child {
        border-bottom: none;
    }
    .post-title {
        font-weight: 600;
        font-size: 16px;
        color: #111;
    }
    .post-content {
        color: #4b5563;
        font-size: 14px;
        margin: 6px 0;
    }
    .post-meta {
        font-size: 13px;
        color: #6b7280;
    }
    .post-stats {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: #6b7280;
        margin-top: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: #9ca3af;
    }
</style>

<div class="profile-container">
    <div id="profile-loading" style="padding:40px;text-align:center;color:#6b7280;">Memuat profil...</div>
    
    <div id="profile-content" style="display:none;">
        <!-- Header -->
        <div class="profile-header">
            <img id="profile-photo" src="" alt="Foto Profil" class="profile-avatar">
            <div>
                <div class="profile-name" id="profile-name"></div>
                <div class="profile-email" id="profile-email"></div>
            </div>
        </div>

        <!-- Stats -->
        <div class="profile-stats">
            <div>
                <div class="stat-number" id="stat-datasets">0</div>
                <div class="stat-label">Jumlah Dataset</div>
            </div>
            <div>
                <div class="stat-number" id="stat-downloads">0</div>
                <div class="stat-label">Total downloads</div>
            </div>
            <div>
                <div class="stat-number" id="stat-rating">0</div>
                <div class="stat-label">Rata-Rata rating</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="dataset">Dataset</button>
            <button class="tab-btn" data-tab="info">Info</button>
            <button class="tab-btn" data-tab="post">Post</button>
        </div>

        <!-- Tab Content -->
        <div id="tabContent" class="tab-content">
            <div class="empty-state">Loading...</div>
        </div>
    </div>

    <div id="profile-error" style="display:none;padding:40px;text-align:center;color:#dc2626;">Gagal memuat profil</div>
</div>

<script>
    const username = "{{ $username }}";

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        } catch { return dateStr; }
    }

    function renderProfile(data) {
        const user = data.user;
        const stats = data.stats;
        const datasets = data.datasets || [];
        const posts = data.posts || [];

        // Header
        document.getElementById('profile-name').textContent = user.name;
        document.getElementById('profile-email').textContent = user.email;
        document.getElementById('profile-photo').src = user.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=2563eb&color=fff&size=80`;

        // Stats
        document.getElementById('stat-datasets').textContent = stats.total_datasets || 0;
        document.getElementById('stat-downloads').textContent = (stats.total_downloads || 0).toLocaleString();
        document.getElementById('stat-rating').textContent = stats.avg_rating || 4.4;

        // Tab content functions
        function renderDatasetTab() {
            if (!datasets.length) {
                return '<div class="empty-state">Belum ada dataset yang diunggah.</div>';
            }
            return datasets.map(ds => `
                <div class="dataset-item">
                    <a href="/datasets/${ds.id}" class="dataset-title">${escapeHtml(ds.title)}</a>
                    <div class="dataset-meta">
                        ${escapeHtml(ds.class) || 'Umum'} • ${ds.present_count || 0} akses • ${formatDate(ds.created_at)}
                    </div>
                    <div class="dataset-actions">
                        <span>⬇️ ${ds.present_count || 0} downloads</span>
                    </div>
                </div>
            `).join('');
        }

        function renderInfoTab() {
            return `
                <div style="margin-bottom:20px;">
                    <h3 style="font-weight:600;font-size:16px;margin-bottom:8px;">Bio</h3>
                    <p style="color:#4b5563;">${escapeHtml(user.bio) || 'Belum mengisi bio.'}</p>
                </div>
                <div style="margin-bottom:20px;">
                    <h3 style="font-weight:600;font-size:16px;margin-bottom:8px;">Detail</h3>
                    <ul style="list-style:none;padding:0;color:#4b5563;font-size:14px;">
                        ${user.institution ? `<li style="margin-bottom:4px;"><span style="font-weight:500;">Institusi :</span> ${escapeHtml(user.institution)}</li>` : ''}
                        ${user.location ? `<li style="margin-bottom:4px;"><span style="font-weight:500;">Lokasi :</span> ${escapeHtml(user.location)}</li>` : ''}
                        <li><span style="font-weight:500;">Bergabung :</span> ${user.joined || 'Belum diketahui'}</li>
                    </ul>
                </div>
            `;
        }

        function renderPostTab() {
            if (!posts.length) {
                return '<div class="empty-state">Belum ada postingan.</div>';
            }
            return posts.map(post => `
                <div class="post-item">
                    <div class="post-title">${escapeHtml(post.content) || 'Postingan'}</div>
                    <div class="post-content">${escapeHtml(post.content) || ''}</div>
                    ${post.dataset ? `<div style="font-size:13px;color:#2563eb;margin:6px 0;">📊 Dataset terkait: ${escapeHtml(post.dataset.title)}</div>` : ''}
                    <div class="post-meta">${formatDate(post.created_at)}</div>
                    <div class="post-stats">
                        <span>❤️ ${post.likes_count || 0}</span>
                        <span>💬 ${post.comments_count || 0}</span>
                        <span>🔁 ${post.shares_count || 0}</span>
                    </div>
                </div>
            `).join('');
        }

        // Tab switching
        const tabContent = document.getElementById('tabContent');
        const tabs = document.querySelectorAll('.tab-btn');

        function switchTab(tab) {
            tabs.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.tab === tab) btn.classList.add('active');
            });

            if (tab === 'dataset') tabContent.innerHTML = renderDatasetTab();
            else if (tab === 'info') tabContent.innerHTML = renderInfoTab();
            else if (tab === 'post') tabContent.innerHTML = renderPostTab();
        }

        tabs.forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
        });

        // Default tab
        switchTab('dataset');
    }

    // Fetch data
    fetch('{{ url("/profile-data") }}/' + username, {
        headers: { 'Accept': 'application/json' },
        credentials: 'omit'
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    })
    .then(data => {
        document.getElementById('profile-loading').style.display = 'none';
        document.getElementById('profile-content').style.display = 'block';
        renderProfile(data);
    })
    .catch(err => {
        document.getElementById('profile-loading').style.display = 'none';
        document.getElementById('profile-error').style.display = 'block';
        document.getElementById('profile-error').textContent = 'Error: ' + err.message;
        console.error(err);
    });
</script>
@endsection