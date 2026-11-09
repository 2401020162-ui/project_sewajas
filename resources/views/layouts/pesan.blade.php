@if(session('sukses'))
    <div class="alert alert-success" role="status">
        <span class="alert-icon">&#10003;</span>
        <div>{{ session('sukses') }}</div>
        <button type="button" class="alert-close" aria-label="Tutup" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" role="alert">
        <span class="alert-icon">!</span>
        <div>{{ session('error') }}</div>
        <button type="button" class="alert-close" aria-label="Tutup" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <span class="alert-icon">!</span>
        <div>
            <strong>Periksa kembali data berikut:</strong>
            <ul class="alert-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="alert-close" aria-label="Tutup" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif
