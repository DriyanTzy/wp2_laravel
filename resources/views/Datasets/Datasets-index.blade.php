@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Dataset</h1>
        <p class="text-gray-600">Temukan dan unduh dataset berkualitas</p>
    </div>

    <div class="flex flex-wrap justify-between gap-4 mb-6">
        <input type="text" id="searchInput" placeholder="Cari dataset..." class="flex-1 max-w-md border rounded-lg px-4 py-2">
        <button id="filterBtn" class="px-4 py-2 border rounded-lg">Filter</button>
    </div>

    <div id="datasetList" class="space-y-4">
        <div class="text-center py-10">Memuat data...</div>
    </div>
</div>

<script>
    async function loadDatasets() {
        try {
            const res = await fetch('/api/datasets');
            const json = await res.json();
            if (json.datasets) {
                renderDatasets(json.datasets);
            } else {
                document.getElementById('datasetList').innerHTML = '<div class="text-center py-10 text-red-500">Gagal memuat data</div>';
            }
        } catch (err) {
            console.error(err);
            document.getElementById('datasetList').innerHTML = '<div class="text-center py-10 text-red-500">Terjadi kesalahan</div>';
        }
    }

    function renderDatasets(datasets) {
        if (!datasets.length) {
            document.getElementById('datasetList').innerHTML = '<div class="text-center py-10">Belum ada dataset</div>';
            return;
        }

        let html = '';
        datasets.forEach(ds => {
            const username = ds.user?.username ?? 'Anonymous';
            const rating = (Math.random() * 2 + 7).toFixed(1); // ganti jika ada field rating
            html += `
                <div class="bg-white rounded-xl shadow-sm border p-5 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-semibold">${escapeHtml(ds.title)}</h3>
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">${escapeHtml(ds.class)}</span>
                        </div>
                        <div class="text-sm text-gray-500 mb-2">
                            <span><i class="fas fa-user"></i> ${escapeHtml(username)}</span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-download"></i> ${ds.present_count ?? 0} akses</span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-star text-yellow-400"></i> ${rating}</span>
                        </div>
                        <p class="text-gray-600 text-sm line-clamp-2">${escapeHtml(ds.description) || 'Tidak ada deskripsi'}</p>
                        <a href="/datasets/${ds.id}" class="inline-block mt-2 text-blue-600 text-sm hover:underline">Lihat detail →</a>
                    </div>
                    <div class="text-right">
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">${ds.points_required ?? 0} poin</span>
                    </div>
                </div>
            `;
        });
        document.getElementById('datasetList').innerHTML = html;
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

    loadDatasets();

    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const keyword = e.target.value.toLowerCase();
        const items = document.querySelectorAll('#datasetList .bg-white');
        items.forEach(item => {
            const title = item.querySelector('h3')?.innerText.toLowerCase() || '';
            item.style.display = title.includes(keyword) ? '' : 'none';
        });
    });
</script>
@endsection