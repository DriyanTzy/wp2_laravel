@extends('layouts.app')

@section('title', 'Profile User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div id="profileContainer" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="text-center py-10">Memuat profil...</div>
    </div>
</div>

<script>
    const username = '{{ $username }}';

    async function loadProfile() {
        try {
            const res = await fetch(`/api/profile/${username}`);
            const data = await res.json();
            renderProfile(data);
        } catch (err) {
            document.getElementById('profileContainer').innerHTML = '<div class="p-6 text-center text-red-500">User tidak ditemukan</div>';
        }
    }

    function renderProfile(data) {
        const u = data.user;
        const stats = data.stats;
        const datasets = data.datasets;
        const posts = data.posts;

        let html = `
            <div class="p-6 border-b flex items-start gap-4">
                <img src="${u.photo ? '/storage/'+u.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(u.name)}" class="w-20 h-20 rounded-full object-cover">
                <div>
                    <h1 class="text-2xl font-bold">${escapeHtml(u.name)}</h1>
                    <p class="text-gray-500">${escapeHtml(u.email)}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 p-6 text-center border-b">
                <div><span class="text-2xl font-bold">${stats.total_datasets}</span><br>Jumlah Dataset</div>
                <div><span class="text-2xl font-bold">${stats.total_downloads.toLocaleString()}</span><br>Total downloads</div>
                <div><span class="text-2xl font-bold">${stats.avg_rating}</span><br>Rata-Rata rating</div>
            </div>
            <div class="border-b">
                <div class="flex">
                    <button class="tab-btn px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600" data-tab="dataset">Dataset</button>
                    <button class="tab-btn px-6 py-3 font-semibold text-gray-500" data-tab="info">Info</button>
                    <button class="tab-btn px-6 py-3 font-semibold text-gray-500" data-tab="post">Post</button>
                </div>
            </div>
            <div id="tabContent" class="p-6"></div>
        `;
        document.getElementById('profileContainer').innerHTML = html;

        // Konten tab
        const datasetHtml = datasets.length ? `
            <div class="space-y-4">
                ${datasets.map(ds => `
                    <div class="border-b pb-3">
                        <a href="/datasets/${ds.id}" class="text-lg font-semibold text-blue-600 hover:underline">${escapeHtml(ds.title)}</a>
                        <div class="text-sm text-gray-500">${ds.class} • ${ds.format} • ${ds.rows} baris • ${ds.present_count} akses • ${ds.created_at}</div>
                    </div>
                `).join('')}
            </div>
        ` : '<p class="text-gray-500">Belum ada dataset.</p>';

        const infoHtml = `
            <div class="space-y-3">
                <div>
                    <h3 class="font-semibold">Bio</h3>
                    <p class="text-gray-700">${escapeHtml(u.bio) || 'Belum mengisi bio.'}</p>
                </div>
                <div>
                    <h3 class="font-semibold">Detail</h3>
                    <ul class="mt-1 space-y-1 text-gray-600">
                        <li><span class="font-medium">Poin:</span> ${u.points ?? 0}</li>
                        <li><span class="font-medium">Bergabung:</span> ${u.joined}</li>
                    </ul>
                </div>
            </div>
        `;

        const postHtml = posts.length ? `
            <div class="space-y-4">
                ${posts.map(post => `
                    <div class="border-b pb-3">
                        <h4 class="font-semibold text-gray-800">${escapeHtml(post.title) || 'Tanpa judul'}</h4>
                        <p class="text-gray-600 text-sm">${escapeHtml(post.content)}</p>
                        ${post.survey_link ? `<a href="${post.survey_link}" target="_blank" class="text-blue-500 text-sm">Link Survey →</a>` : ''}
                        <div class="text-xs text-gray-400 mt-1">${post.created_at}</div>
                    </div>
                `).join('')}
            </div>
        ` : '<p class="text-gray-500">Belum ada postingan.</p>';

        function setTab(tab) {
            const tabContent = document.getElementById('tabContent');
            if (tab === 'dataset') tabContent.innerHTML = datasetHtml;
            else if (tab === 'info') tabContent.innerHTML = infoHtml;
            else tabContent.innerHTML = postHtml;

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-blue-600');
                btn.classList.add('text-gray-500', 'border-transparent');
                if (btn.dataset.tab === tab) {
                    btn.classList.add('text-blue-600', 'border-blue-600');
                    btn.classList.remove('text-gray-500', 'border-transparent');
                }
            });
        }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => setTab(btn.dataset.tab));
        });
        setTab('dataset');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    loadProfile();
</script>
@endsection