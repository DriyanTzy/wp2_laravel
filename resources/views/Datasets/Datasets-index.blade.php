@extends('layouts.app')

@section('title', 'Datasets – SurveySwap')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dataset-index.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Banner --}}
<div class="banner">
    <div class="banner-img">
        <img src="{{ asset('images/gambar2.png') }}" alt="Dataset Banner">
    </div>
    <div class="banner-text">
        <h2>Kelola Dataset Anda</h2>
        <p>Temukan, kelola dan unduh berbagai dataset berkualitas untuk kebutuhan riset dan analisis anda</p>
    </div>
</div>

{{-- Section header --}}
<div class="dataset-section-header">
    <div class="dataset-section-title">
        <h2>Dataset saya</h2>
        <p>Berikut adalah koleksi Dataset yang tersedia.</p>
    </div>
    <div class="section-controls">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari dataset...">
        </div>
        <button class="filter-btn" id="filterBtn">
            <i class="fas fa-sliders-h"></i> Filter
        </button>
        <button class="chevron-btn">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
</div>

{{-- Dataset grid --}}
<div id="datasetList" class="dataset-grid">
    <div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#aaa;">Memuat data...</div>
</div>

<script>
    async function loadDatasets() {
        try {
            const res = await fetch('/api/datasets');
            const json = await res.json();
            if (json.datasets) {
                renderDatasets(json.datasets);
            } else {
                document.getElementById('datasetList').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#e74c3c;">Gagal memuat data</div>';
            }
        } catch (err) {
            console.error(err);
            document.getElementById('datasetList').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#e74c3c;">Terjadi kesalahan</div>';
        }
    }

    function renderDatasets(datasets) {
        if (!datasets.length) {
            document.getElementById('datasetList').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#aaa;">Belum ada dataset</div>';
            return;
        }

        let html = '';
        datasets.forEach(ds => {
            const username = ds.user?.username ?? 'Anonymous';
            const rating = (Math.random() * 2 + 7).toFixed(1); // ganti jika ada field rating
            html += `
                <div class="dataset-card">
                    <img class="dataset-card-img" src="${ds.thumbnail ? '/' + ds.thumbnail : ''}" alt="${escapeHtml(ds.title)}" onerror="this.style.background='#d0d8e8';this.removeAttribute('src')">
                    <div class="dataset-card-body">
                        <div class="card-top-row">
                            <span class="card-source">Survey Swap</span>
                            <button class="card-menu-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                        <div class="card-title">${escapeHtml(ds.title)}</div>
                        <div class="card-meta">Usability ${rating} . Update a day ago</div>
                        <div class="card-size">${ds.present_count ?? 0} downloads</div>
                        <div class="card-actions">
                            <a href="/datasets/${ds.id}" class="btn-detail">Lihat detail <i class="fas fa-chevron-right"></i></a>
                            <button class="btn-download"><i class="fas fa-download"></i></button>
                        </div>
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
        const items = document.querySelectorAll('#datasetList .dataset-card');
        items.forEach(item => {
            const title = item.querySelector('.card-title')?.innerText.toLowerCase() || '';
            item.style.display = title.includes(keyword) ? '' : 'none';
        });
    });
</script>

@endsection
