<!-- Profile Modal -->
@auth
<div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Profil Pengguna</span>
            <button class="modal-close" onclick="closeProfileModal()" aria-label="Tutup"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        <div class="modal-body">
            <div style="display:flex;flex-direction:column;align-items:flex-start;margin-bottom:16px">
                <div class="modal-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div style="margin-top:6px">
                    <strong style="color:var(--text-dark, #0d0d0d);font-size:15px">{{ auth()->user()->name }}</strong>
                    <div style="color:var(--text-muted, #9a9aaa);font-size:12px">{{ auth()->user()->email }} &middot; <span style="text-transform:capitalize">{{ auth()->user()->role->label() ?? auth()->user()->role->value }}</span></div>
                </div>
            </div>
            <div class="mform-group">
                <label class="mform-label">Nama Lengkap</label>
                <input type="text" class="mform-control" value="{{ auth()->user()->name }}" readonly style="background:#f8f9fa">
            </div>
            <div class="mform-group">
                <label class="mform-label">Email</label>
                <input type="email" class="mform-control" value="{{ auth()->user()->email }}" readonly style="background:#f8f9fa">
            </div>
            <div class="mform-group">
                <label class="mform-label">No. WhatsApp</label>
                <input type="text" class="mform-control" value="{{ auth()->user()->phone ?? '-' }}" readonly style="background:#f8f9fa">
            </div>
            <button type="button" class="btn-save" onclick="closeProfileModal()" style="margin-top:12px">Tutup</button>
        </div>
    </div>
</div>
@endauth
