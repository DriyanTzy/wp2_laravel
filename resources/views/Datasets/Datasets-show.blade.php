@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('datasets.index') }}" class="inline-flex items-center text-blue-600 mb-4 hover:underline">
        <i class="fas fa-arrow-left mr-1"></i> kembali
    </a>

    <div id="detailContainer" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 text-center">Memuat data...</div>
    </div>

    <div id="accessSection" class="mt-6 text-center"></div>
</div>

<script>
    const datasetId = window.location.pathname.split('/').pop();
    let currentDataset = null;

    async function loadDetail() {
        try {
            const res = await fetch(`/api/datasets/${datasetId}`);
            const json = await res.json();
            if (json.dataset) {
                currentDataset = json.dataset;
                renderDetail(json.dataset);
                renderAccessButton(json.dataset);
            } else {
                document.getElementById('detailContainer').innerHTML = '<div class="p-6 text-center text-red-500">Dataset tidak ditemukan</div>';
            }
        } catch (err) {
            console.error(err);
            document.getElementById('detailContainer').innerHTML = '<div class="p-6 text-center text-red-500">Gagal memuat data</div>';
        }
    }

    function renderDetail(ds) {
        const username = ds.user?.username ?? 'Unknown';
        const userPhoto = ds.user?.photo ? '/storage/' + ds.user.photo : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(username);
        const rating = (Math.random() * 2 + 7).toFixed(1);

        const html = `
            <div class="p-6 border-b flex items-start gap-4">
                <img src="${userPhoto}" class="w-16 h-16 rounded-full object-cover">
                <div>
                    <h1 class="text-2xl font-bold">${escapeHtml(ds.title)}</h1>
                    <p class="text-gray-500">Oleh ${escapeHtml(username)} • ${escapeHtml(ds.class)}</p>
                    <div class="flex gap-4 mt-2 text-sm">
                        <span><i class="fas fa-download"></i> ${ds.present_count ?? 0} akses</span>
                        <span><i class="fas fa-star text-yellow-400"></i> ${rating}</span>
                        <span><i class="fas fa-coins"></i> ${ds.points_required ?? 0} poin</span>
                    </div>
                </div>
            </div>
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold mb-2">Deskripsi</h2>
                <p class="text-gray-700">${escapeHtml(ds.description) || 'Tidak ada deskripsi.'}</p>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4 text-sm bg-gray-50">
                <div><span class="font-semibold">Class:</span> ${escapeHtml(ds.class)}</div>
                <div><span class="font-semibold">Poin required:</span> ${ds.points_required ?? 0}</div>
                <div><span class="font-semibold">File:</span> ${ds.file_path ? 'Tersedia' : 'Tidak ada file'}</div>
                <div><span class="font-semibold">Thumbnail:</span> ${ds.thumbnail ? 'Ada' : 'Tidak'}</div>
            </div>
        `;
        document.getElementById('detailContainer').innerHTML = html;
    }

    function renderAccessButton(ds) {
        const container = document.getElementById('accessSection');
        container.innerHTML = `
            <button id="accessBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                <i class="fas fa-download mr-2"></i> Akses Dataset (${ds.points_required ?? 0} poin)
            </button>
            <div id="accessMessage" class="mt-3 text-sm"></div>
        `;

        document.getElementById('accessBtn').addEventListener('click', async () => {
            const msgDiv = document.getElementById('accessMessage');
            msgDiv.innerHTML = 'Memproses...';
            try {
                const res = await fetch(`/api/datasets/${datasetId}/access`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                });
                const data = await res.json();
                if (res.ok) {
                    msgDiv.innerHTML = `<span class="text-green-600">${data.message}</span>`;
                    if (data.file_url) {
                        window.open(data.file_url, '_blank');
                    }
                } else {
                    msgDiv.innerHTML = `<span class="text-red-600">${data.message || 'Gagal mengakses'}</span>`;
                }
            } catch (err) {
                msgDiv.innerHTML = '<span class="text-red-600">Terjadi kesalahan</span>';
            }
        });
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

    loadDetail();
</script>
@endsection