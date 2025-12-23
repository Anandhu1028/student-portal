<ul class="timeline list-unstyled">
    @forelse($activities as $a)
        <li class="mb-3">
            <div class="d-flex align-items-start gap-2">
                <div class="avatar rounded-circle bg-light p-2">{{ strtoupper(substr($a->actor?->name ?? 'U',0,1)) }}</div>
                <div>
                    <div><strong>{{ $a->actor?->name ?? 'System' }}</strong> <small class="text-muted">• {{ $a->created_at->diffForHumans() }}</small></div>
                    <div class="small">{{ $a->message }}</div>
                    @if($a->attachments->isNotEmpty())
                        <div class="mt-1 small">
                            @foreach($a->attachments as $att)
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank">Attachment</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </li>
    @empty
        <li class="text-center text-muted">No activity yet</li>
    @endforelse
</ul>
