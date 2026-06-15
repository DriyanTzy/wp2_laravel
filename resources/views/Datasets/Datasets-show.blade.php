@extends('layouts.app')

@section('title', 'Detail Dataset – SurveySwap')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dataset-show.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="ds-wrap">

    {{-- Back link --}}
    <a href="{{ route('datasets.index') }}" class="ds-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    {{-- Main card --}}
    <div class="ds-card" id="detailContainer">
        <div class="ds-loading">Memuat data...</div>
    </div>

    {{-- Download button --}}
    <div class="ds-download-wrap" id="downloadSection" style="display:none">
        <button class="btn-download-dataset" id="accessBtn">
            <i class="fas fa-download"></i> Download Dataset
        </button>
        <div id="accessMessage" style="text-align:center;margin-top:10px;font-size:0.83rem"></div>
    </div>

</div>

<script>
    const datasetId = window.location.pathname.split('/').pop();

    async function loadDetail() {
        try {
            const res  = await fetch(`/api/datasets/${datasetId}`);
            const json = await res.json();
            if (json.dataset) {
                renderDetail(json.dataset);
                setupDownload(json.dataset);
            } else {
                document.getElementById('detailContainer').innerHTML = '<div class="ds-error">Dataset tidak ditemukan</div>';
            }
        } catch (err) {
            console.error(err);
            document.getElementById('detailContainer').innerHTML = '<div class="ds-error">Gagal memuat data</div>';
        }
    }

    function renderDetail(ds) {
        const rating     = (Math.random() * 2 + 7).toFixed(1);
        const downloads  = ds.present_count ?? 0;
        const thumbSrc = ds.thumbnail ? '/' + ds.thumbnail : '';
        const thumbHtml  = thumbSrc
            ? `<img class="ds-thumbnail" src="${thumbSrc}" alt="${escapeHtml(ds.title)}" onerror="this.style.background='#d0d8e8';this.removeAttribute('src')">`
            : `<div class="ds-thumbnail"></div>`;

        document.getElementById('detailContainer').innerHTML = `
            <div class="ds-header">
                ${thumbHtml}
                <div class="ds-info">
                    <h1>${escapeHtml(ds.title)}</h1>
                    <p class="ds-publisher">SurveySwab Inc.</p>
                    <div class="ds-stats">
                        <div class="ds-stat">
                            <div class="ds-stat-value">
                                ${rating} <i class="fas fa-star"></i>
                            </div>
                            <div class="ds-stat-label">1k reviews</div>
                        </div>
                        <div class="ds-stat">
                            <div class="ds-stat-value">${downloads}+</div>
                            <div class="ds-stat-label">Downloads</div>
                        </div>
                        <div class="ds-stat">
                            <div class="ds-stat-value">
                                <i class="fas fa-tag" style="color:#555;font-size:1rem"></i>
                            </div>
                            <div class="ds-stat-label">Everyone</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ds-about">
                <h2>Tentang Dataset</h2>
                <p>${escapeHtml(ds.description) || 'Tidak ada deskripsi.'}</p>

                <div class="ds-tags">
                    <span class="ds-tag">${escapeHtml(ds.class)}</span>
                    <span class="ds-tag">Survei</span>
                    <span class="ds-tag">2026</span>
                    <span class="ds-tag">Indonesia</span>
                </div>

                <div class="ds-meta-grid">
                    <div class="ds-meta-item">
                        <div class="ds-meta-label">Format File</div>
                        <div class="ds-meta-value">CSV/JSON</div>
                    </div>
                    <div class="ds-meta-item">
                        <div class="ds-meta-label">Ukuran</div>
                        <div class="ds-meta-value">500MB</div>
                    </div>
                    <div class="ds-meta-item">
                        <div class="ds-meta-label">Jumlah baris</div>
                        <div class="ds-meta-value">1.500</div>
                    </div>
                    <div class="ds-meta-item">
                        <div class="ds-meta-label">Lisensi</div>
                        <div class="ds-meta-value">CC BY 3.9</div>
                    </div>
                </div>
            </div>
        `;
    }

    function setupDownload(ds) {
        document.getElementById('downloadSection').style.display = 'block';
        document.getElementById('accessBtn').addEventListener('click', async () => {
            const msgDiv = document.getElementById('accessMessage');
            msgDiv.innerHTML = 'Memproses...';
            try {
                const res = await fetch(`/api/datasets/${datasetId}/access`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    msgDiv.innerHTML = `<span style="color:#22c55e">${data.message}</span>`;
                    if (data.file_url) window.open(data.file_url, '_blank');
                } else {
                    msgDiv.innerHTML = `<span style="color:#e74c3c">${data.message || 'Gagal mengakses'}</span>`;
                }
            } catch (err) {
                msgDiv.innerHTML = '<span style="color:#e74c3c">Terjadi kesalahan</span>';
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]));
    }

    loadDetail();
</script>

@endsection
