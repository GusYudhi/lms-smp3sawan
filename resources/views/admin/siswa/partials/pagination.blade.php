<div class="pagination-container">
    <div class="pagination-info">
        Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} data
    </div>
    <div class="pagination-controls">
        @if ($students->onFirstPage())
            <button class="btn btn-pagination" disabled>
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </button>
        @else
            <a href="#" class="btn btn-pagination" onclick="loadPage({{ $students->currentPage() - 1 }}); return false;">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
        @endif

        <span class="pagination-current">{{ $students->currentPage() }}</span>

        @if ($students->hasMorePages())
            <a href="#" class="btn btn-pagination" onclick="loadPage({{ $students->currentPage() + 1 }}); return false;">
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <button class="btn btn-pagination" disabled>
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </button>
        @endif
    </div>
</div>
