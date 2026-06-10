@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Title & date diisi JS setelah API selesai --}}
<h1 class="page-title" id="pageTitle">Selamat Datang…</h1>
<p  class="page-date"  id="pageDate"></p>

{{-- Error banner (hidden by default) --}}
<div class="alert alert-error" id="apiError" style="display:none"></div>

{{-- ── Stat cards ── --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>
        <div class="stat-label">Total Survey</div>
        <div class="stat-value skeleton" id="valSurveys" style="width:80px;height:36px;border-radius:8px"></div>
        <div class="stat-trend" id="trendSurveys" style="visibility:hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
            Aktif
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
        </div>
        <div class="stat-label">Total Responses</div>
        <div class="stat-value skeleton" id="valResponses" style="width:80px;height:36px;border-radius:8px"></div>
        <div class="stat-trend" id="trendResponses" style="visibility:hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M2 12s3.64-7 10-7 10 7 10 7-3.64 7-10 7-10-7-10-7z"/>
            </svg>
        </div>
        <div class="stat-label">Total Reach</div>
        <div class="stat-value skeleton" id="valReach" style="width:80px;height:36px;border-radius:8px"></div>
        <div class="stat-trend" id="trendReach" style="visibility:hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
            Unique users
        </div>
    </div>

</div>

{{-- ── Active Surveys ── --}}
<div class="section-header">
    <h2 class="section-title">Survey Aktif</h2>
    <a href="{{ route('datasets.index') }}" class="section-link">Lihat semua &rarr;</a>
</div>

<div class="item-list" id="surveyList">
    {{-- Skeleton rows --}}
    @for($i = 0; $i < 3; $i++)
    <div class="item-card">
        <div class="item-icon skeleton" style="width:52px;height:52px"></div>
        <div class="item-info">
            <div class="skeleton" style="width:160px;height:13px;border-radius:4px;margin-bottom:8px"></div>
            <div class="skeleton" style="width:100px;height:11px;border-radius:4px"></div>
        </div>
        <div class="skeleton" style="width:60px;height:26px;border-radius:20px"></div>
    </div>
    @endfor
</div>

@endsection

@push('scripts')
<script>
// ─── Format angka jadi 8.7K / 19.4K ─────────────────────────────────────────
const fmt = n => n >= 1000 ? (n / 1000).toFixed(1).replace(/\.0$/, '') + 'K' : n;

// ─── Set nilai stat card ──────────────────────────────────────────────────────
function setStatCard(elId, trendId, value, trendText = null) {
    const el = document.getElementById(elId);
    el.classList.remove('skeleton');
    el.style = '';
    el.textContent = fmt(value);

    if (trendText !== null) {
        const tr = document.getElementById(trendId);
        tr.style.visibility = 'visible';
        // Append teks setelah SVG
        const svg = tr.querySelector('svg');
        tr.textContent = '';
        tr.appendChild(svg);
        tr.append(' ' + trendText);
    }
}

// ─── Build survey list ────────────────────────────────────────────────────────
const ICONS = ['📋','📊','📝','🗂️','📌','📎'];
function buildSurveyList(surveys) {
    const wrap = document.getElementById('surveyList');

    if (!surveys || surveys.length === 0) {
        wrap.innerHTML = '<p style="color:#999;font-size:.85rem;padding:12px 0">Belum ada survey aktif.</p>';
        return;
    }

    wrap.innerHTML = surveys.map((s, i) => `
        <div class="item-card">
            <div class="item-icon">${ICONS[i % ICONS.length]}</div>
            <div class="item-info">
                <div class="item-name">${s.title ?? 'Survey #' + s.id}</div>
                <div class="item-meta">${s.responses_count ?? 0} responden</div>
            </div>
            <span class="badge ${s.is_active ? 'badge-active' : 'badge-inactive'}">
                ${s.is_active ? 'Aktif' : 'Nonaktif'}
            </span>
        </div>
    `).join('');
}

// ─── Fetch /api/dashboard ─────────────────────────────────────────────────────
(async () => {
    try {
        const res = await fetch('/api/dashboard', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: 'same-origin',
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        const stats = data.stats;
        const user  = data.user;

        // Judul halaman
        document.getElementById('pageTitle').textContent = `Selamat Datang, ${user.name}`;
        document.getElementById('pageDate').textContent  =
            new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        // Stat cards
        setStatCard('valSurveys',   'trendSurveys',   stats.total_surveys,   `${stats.total_surveys} total`);
        setStatCard('valResponses', 'trendResponses',  stats.total_responses, `${stats.total_datasets} dataset`);
        setStatCard('valReach',     'trendReach',      stats.total_reach,     'Unique users');

        // Survey list
        buildSurveyList(data.active_surveys);

    } catch (err) {
        const errEl = document.getElementById('apiError');
        errEl.style.display = 'block';
        errEl.textContent   = 'Gagal memuat data dashboard. Silakan refresh halaman.';
        console.error('Dashboard API error:', err);
    }
})();
</script>
@endpush