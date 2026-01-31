<div class="pagination-container">
    <div class="pagination-info">
        Menampilkan {{ $teachers->firstItem() ?? 0 }}-{{ $teachers->lastItem() ?? 0 }} dari {{ $teachers->total() }} data
    </div>
    <div class="pagination-controls">
        @if ($teachers->onFirstPage())
            <button class="btn btn-pagination" disabled>
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </button>
        @else
            <a href="javascript:void(0)" class="btn btn-pagination" onclick="loadPage({{ $teachers->currentPage() - 1 }}); return false;">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
        @endif

        <span class="pagination-current">{{ $teachers->currentPage() }}</span>

        @if ($teachers->hasMorePages())
            <a href="javascript:void(0)" class="btn btn-pagination" onclick="loadPage({{ $teachers->currentPage() + 1 }}); return false;">
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <button class="btn btn-pagination" disabled>
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </button>
        @endif
    </div>
</div>
