@extends('layouts.app')

@section('title', 'Datasets')

@section('content')
<style>
    .dataset-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .dataset-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .dataset-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #111;
    }
    .dataset-header p {
        color: #6b7280;
        font-size: 14px;
    }
    .btn-upload {
        background: #2563eb;
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.2s;
    }
    .btn-upload:hover {
        background: #1d4ed8;
    }
    .dataset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .dataset-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        transition: box-shadow 0.2s;
    }
    .dataset-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .dataset-card-title {
        font-weight: 600;
        font-size: 18px;
        color: #111;
        text-decoration: none;
    }
    .dataset-card-title:hover {
        color: #2563eb;
    }
    .dataset-card-class {
        font-size: 14px;
        color: #6b7280;
        margin: 4px 0 8px;
    }
    .dataset-card-desc {
        font-size: 14px;
        color: #4b5563;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dataset-card-meta {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #9ca3af;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }
    .dataset-card-meta a {
        color: #2563eb;
        text-decoration: none;
    }
    .empty-state {
        text-align: center;
        padding: 60px 0;
        color: #9ca3af;
    }
    .error-msg {
        background: #fee2e2;
        color: #dc2626;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
</style>

<div class="dataset-container">
    <div class="dataset-header">
        <div>
            <h1>Kelola Dataset</h1>
            <p>Temukan dan unduh dataset berkualitas</p>
        </div>
        <a href="#" class="btn-upload">+ Upload Dataset</a>
    </div>

    <div id="datasetList">
        <div class="empty-state">Memuat data...</div>
    </div>
</div>

<script>
    async function loadDatasets() {
        const container = document.getElementById('datasetList');
        try {
            const res = await fetch('/datasets-data', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            const datasets = data.datasets || [];

            if (!datasets.length) {
                container.innerHTML = '<div class="empty-state">Belum ada dataset yang diunggah.</div>';
                return;
            }

            let html = '<div class="dataset-grid">';
            datasets.forEach(ds => {
                html += `
                    <div class="dataset-card">
                        <a href="/datasets/${ds.id}" class="dataset-card-title">${escapeHtml(ds.title)}</a>
                        <div class="dataset-card-class">📁 ${escapeHtml(ds.class) || 'Umum'}</div>
                        <div class="dataset-card-desc">${escapeHtml(ds.description) || 'Tidak ada deskripsi'}</div>
                        <div class="dataset-card-meta">
                            <span>⬇️ ${ds.present_count || 0} akses</span>
                            <span>👤 ${escapeHtml(ds.user?.name)}</span>
                            <span>${formatDate(ds.created_at)}</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;

        } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="error-msg">⚠️ Terjadi kesalahan: ${err.message}</div>`;
        }
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

    function formatDate(dateStr) {
        if (!dateStr) return '';
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        } catch { return dateStr; }
    }

    loadDatasets();
</script>
@endsection