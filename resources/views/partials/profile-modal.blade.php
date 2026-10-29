<!-- Profile Modal -->
@auth
<div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Profil</span>
            <button class="modal-close" onclick="closeProfileModal()" aria-label="Tutup"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('profile.update') }}" id="profileForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div class="modal-avatar" id="avatarPreviewContainer" style="overflow:hidden;flex-shrink:0">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                        @else
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80" alt="Avatar" id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                        @endif
                    </div>
                    <input type="file" name="avatar" id="avatarFileInput" accept="image/*" style="display:none" onchange="previewProfileAvatar(this)">
                    <button type="button" class="modal-edit-btn" onclick="document.getElementById('avatarFileInput').click()" style="margin-bottom:0">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit gambar
                    </button>
                </div>
                <div class="mform-group">
                    <label class="mform-label">Nama Lengkap</label>
                    <input type="text" name="name" class="mform-control" placeholder="Nama Lengkap" value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="mform-group">
                    <label class="mform-label">No WhatsApp</label>
                    <input type="text" name="phone" class="mform-control" placeholder="12345678910" value="{{ old('phone', auth()->user()->phone) }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. KTP</label>
                    <input type="text" name="ktp_number" class="mform-control" placeholder="12345678910" value="{{ old('ktp_number', auth()->user()->ktp_number) }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. SIM</label>
                    <input type="text" name="sim_number" class="mform-control" placeholder="12345678910" value="{{ old('sim_number', auth()->user()->sim_number) }}">
                </div>
                <button type="submit" class="btn-save">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
function previewProfileAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreviewImg');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endauth
