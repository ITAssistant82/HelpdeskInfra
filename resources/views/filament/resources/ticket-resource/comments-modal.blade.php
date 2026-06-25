@php
    $comments = $ticket->comments->where('is_internal', false);
    if ($isStaff) {
        $comments = $ticket->comments;
    }
@endphp

<div style="max-height: 400px; overflow-y: auto; padding: 4px 0;">
    @forelse($comments->sortByDesc('created_at') as $comment)
        <div style="display:flex; gap:12px; padding:12px; margin-bottom:8px; border-radius:8px; {{ $comment->user && $comment->user->isStaff() ? 'background:#fefce8; border:1px solid #fde68a;' : 'background:#f9fafb; border:1px solid #e5e7eb;' }}">
            <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:600; color:white; {{ $comment->user && $comment->user->isStaff() ? 'background:#f59e0b;' : 'background:#9ca3af;' }}">
                {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
            </div>
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                    <span style="font-size:13px; font-weight:600; color:#374151;">
                        {{ $comment->user->name ?? 'Unknown' }}
                    </span>
                    @if($comment->user && $comment->user->isStaff())
                        <span style="font-size:11px; padding:1px 6px; border-radius:4px; background:#f59e0b; color:white; font-weight:500;">Admin</span>
                    @endif
                    @if($isStaff && $comment->is_internal)
                        <span style="font-size:11px; padding:1px 6px; border-radius:4px; background:#ef4444; color:white; font-weight:500;">Internal</span>
                    @endif
                    <span style="font-size:11px; color:#9ca3af; margin-left:auto;">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div style="font-size:13px; color:#4b5563; line-height:1.5; white-space:pre-wrap;">{{ $comment->content }}</div>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:32px 0; color:#9ca3af; font-size:14px;">Belum ada komentar</div>
    @endforelse
</div>

@if($isStaff)
    <div style="padding-top:8px; text-align:center;">
        <a href="{{ url('admin/tickets/' . $ticket->id) }}" style="font-size:13px; color:#f59e0b; text-decoration:none;">+ Tambah komentar di halaman detail tiket</a>
    </div>
@endif
