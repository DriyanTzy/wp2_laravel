@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('datasets.index') }}" class="inline-flex items-center gap-2 text-gray-600 mb-6 hover:text-black">
        ← kembali
    </a>

    <div id="detailContainer" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 text-center text-gray-400">Memuat data...</div>
    </div>
</div>

<script>
    const datasetId = window.location.pathname.split('/').pop();

    async function loadDetail() {
        try {
            const res = await fetch(`/api/datasets/${datasetId}`);
            const json = await res.json();
            if (json.dataset) {
                renderDetail(json.dataset);
            } else {
                document.getElementById('detailContainer').innerHTML =
                    '<div class="p-6 text-center text-red-500">Dataset tidak ditemukan</div>';
            }
        } catch (err) {
            document.getElementById('detailContainer').innerHTML =
                '<div class="p-6 text-center text-red-500">Gagal memuat data</div>';
        }
    }

    function renderDetail(ds) {
        const username = ds.user?.username ?? 'Unknown';
        const rating = (Math.random() * 2 + 7).toFixed(1);

        document.getElementById('detailContainer').innerHTML = `
            <div class="p-6">

                <!-- Header: thumbnail + judul -->
                <div class="flex flex-col md:flex-row gap-6 mb-6">
                    <div class="w-full md:w-60 h-44 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                        ${ds.thumbnail
                            ? `<img src="/storage/${ds.thumbnail}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Tidak ada thumbnail</div>`
                        }
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">${escapeHtml(ds.title)}</h1>
                        <p class="text-gray-400 text-sm mb-4">SurveySwap Inc.</p>

                        <!-- Stats -->
                        <div class="flex items-center gap-0 border border-gray-200 rounded-xl overflow-hidden text-center text-sm mb-4 divide-x divide-gray-200">
                            <div class="flex-1 py-3 px-2">
                                <div class="font-bold text-base">${rating} ★</div>
                                <div class="text-gray-400 text-xs">1k reviews</div>
                            </div>
                            <div class="flex-1 py-3 px-2">
                                <div class="font-bold text-base">${ds.present_count ?? 0}+</div>
                                <div class="text-gray-400 text-xs">Downloads</div>
                            </div>
                            <div class="flex-1 py-3 px-2">
                                <div class="font-bold text-base">E</div>
                                <div class="text-gray-400 text-xs">Everyone</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Download -->
                <button id="accessBtn"
                    class="w-full border border-gray-300 rounded-xl py-3 flex items-center justify-center gap-2 text-gray-700 hover:bg-gray-50 transition mb-2">
                    ⬇ Download Dataset
                </button>
                <div id="accessMessage" class="text-center text-sm mb-6"></div>

                <!-- Tentang Dataset -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h2 class="text-lg font-bold mb-3">Tentang Dataset</h2>
                    <p class="text-gray-700 text-sm leading-relaxed mb-5">
                        ${escapeHtml(ds.description) || 'Tidak ada deskripsi.'}
                    </p>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="bg-gray-200 text-gray-600 text-xs px-4 py-1.5 rounded-full">${escapeHtml(ds.class)}</span>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-gray-400 text-xs mb-1">Format File</div>
                            <div class="font-semibold">CSV/JSON</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-gray-400 text-xs mb-1">Ukuran</div>
                            <div class="font-semibold">—</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-gray-400 text-xs mb-1">Jumlah baris</div>
                            <div class="font-semibold">—</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-gray-400 text-xs mb-1">Lisensi</div>
                            <div class="font-semibold">CC BY 4.0</div>
                        </div>
                    </div>
                </div>

            </div>
        `;

        document.getElementById('accessBtn').addEventListener('click', () => handleAccess(ds));
    }

    async function handleAccess(ds) {
        const btn = document.getElementById('accessBtn');
        const msgDiv = document.getElementById('accessMessage');

        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        try {
            const res = await fetch(`/api/datasets/${datasetId}/access`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            if (res.ok) {
                msgDiv.innerHTML = `<span class="text-green-600">${data.message}</span>`;
                btn.innerHTML = '✓ Dataset Diakses';
                if (data.file_url) window.open(data.file_url, '_blank');
            } else {
                msgDiv.innerHTML = `<span class="text-red-500">${data.message || 'Gagal mengakses'}</span>`;
                btn.disabled = false;
                btn.innerHTML = '⬇ Download Dataset';
            }
        } catch (err) {
            msgDiv.innerHTML = '<span class="text-red-500">Terjadi kesalahan</span>';
            btn.disabled = false;
            btn.innerHTML = '⬇ Download Dataset';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));
    }

    loadDetail();
</script>
@endsection