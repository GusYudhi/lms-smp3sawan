@props(['src', 'name' => 'User', 'size' => 'md', 'clickable' => true])

@php
    $sizeClasses = [
        'xs' => 'width: 32px; height: 32px;',
        'sm' => 'width: 48px; height: 48px;',
        'md' => 'width: 60px; height: 60px;',
        'lg' => 'width: 80px; height: 80px;',
        'xl' => 'width: 120px; height: 120px;',
    ];

    $selectedSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $hasPhoto = !empty($src) && $src !== asset('assets/image/default-avatar.png');
    $initial = strtoupper(substr($name, 0, 1));
@endphp

@if($hasPhoto)
    <div class="profile-photo-wrapper {{ $clickable ? 'cursor-pointer' : '' }}"
         @if($clickable)
         onclick="showPhotoModal('{{ $src }}', '{{ $name }}')"
         @endif
         style="{{ $selectedSize }} position: relative;">
        <img src="{{ $src }}"
             alt="{{ $name }}"
             class="rounded-circle object-fit-cover"
             style="width: 100%; height: 100%;"
             onerror="this.onerror=null; this.src='{{ asset('assets/image/default-avatar.png') }}';">
    </div>
@else
    <div class="avatar-circle d-flex align-items-center justify-content-center text-white fw-bold"
         style="{{ $selectedSize }} background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%;">
        {{ $initial }}
    </div>
@endif

@once
@push('styles')
<style>
    .profile-photo-wrapper {
        display: inline-block;
        position: relative;
        overflow: hidden;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .profile-photo-wrapper.cursor-pointer {
        cursor: pointer;
    }

    .profile-photo-wrapper:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .avatar-circle {
        font-size: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<!-- Global Photo Modal (only once per page) -->
<div class="modal fade" id="globalPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalPhotoModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="globalPhotoModalImage"
                     src=""
                     alt=""
                     class="img-fluid"
                     style="max-height: 70vh; width: auto;"
                     onerror="this.onerror=null; this.src='{{ asset('assets/image/default-avatar.png') }}';">
            </div>
        </div>
    </div>
</div>

<script>
    function showPhotoModal(imageSrc, userName) {
        // Update modal content
        document.getElementById('globalPhotoModalLabel').textContent = userName;
        document.getElementById('globalPhotoModalImage').src = imageSrc;
        document.getElementById('globalPhotoModalImage').alt = userName;

        // Show modal using Bootstrap 5
        var modal = new bootstrap.Modal(document.getElementById('globalPhotoModal'));
        modal.show();
    }
</script>
@endpush
@endonce
