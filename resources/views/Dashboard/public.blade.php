@extends('layouts.app')

@section('content')
<div id="profile-loading">Memuat profil...</div>
<div id="profile-content" style="display: none;">
    <div class="profile-header">
        <img id="profile-photo" src="" alt="Foto" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
        <h1 id="profile-name"></h1>
        <p id="profile-username"></p>
        <p id="profile-bio"></p>
        <p id="profile-joined"></p>
    </div>
    <div class="profile-stats">
        <div><strong id="stat-datasets">0</strong> Dataset</div>
        <div><strong id="stat-downloads">0</strong> Downloads</div>
        <div><strong id="stat-rating">0</strong> Rating</div>
    </div>
    <h3>Dataset</h3>
    <ul id="dataset-list"></ul>
    <h3>Postingan</h3>
    <ul id="post-list"></ul>
</div>
<div id="profile-error" style="color:red;display:none;">Gagal memuat profil.</div>

<script>
    const username = "{{ $username }}";
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

        const user = data.user;
        document.getElementById('profile-name').textContent = user.name;
        document.getElementById('profile-username').textContent = '@' + user.username;
        document.getElementById('profile-bio').textContent = user.bio || 'Tidak ada bio';
        document.getElementById('profile-joined').textContent = 'Bergabung ' + user.joined;
        document.getElementById('profile-photo').src = user.photo || 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name);

        document.getElementById('stat-datasets').textContent = data.stats.total_datasets;
        document.getElementById('stat-downloads').textContent = data.stats.total_downloads;
        document.getElementById('stat-rating').textContent = data.stats.avg_rating;

        const datasetList = document.getElementById('dataset-list');
        datasetList.innerHTML = '';
        data.datasets.forEach(ds => {
            const li = document.createElement('li');
            li.innerHTML = `<strong>${ds.title}</strong> (${ds.class}) - ${ds.present_count} downloads, ${ds.created_at}`;
            datasetList.appendChild(li);
        });

        const postList = document.getElementById('post-list');
        postList.innerHTML = '';
        data.posts.forEach(post => {
            const li = document.createElement('li');
            li.innerHTML = `<p>${post.content}</p><small>${post.created_at} | Likes: ${post.likes_count} | Comments: ${post.comments_count} | Shares: ${post.shares_count}</small>`;
            if(post.dataset) li.innerHTML += `<br><em>Dataset: ${post.dataset.title}</em>`;
            postList.appendChild(li);
        });
    })
    .catch(err => {
        console.error(err);
        document.getElementById('profile-loading').style.display = 'none';
        document.getElementById('profile-error').style.display = 'block';
        document.getElementById('profile-error').textContent = 'Error: ' + err.message;
    });
</script>
@endsection