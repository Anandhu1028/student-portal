<ul class="timeline list-unstyled">
    @forelse($activities as $a)
        <li class="mb-3">
            <div class="d-flex gap-2">
                <div class="avatar bg-light rounded-circle px-2">
                    {{ strtoupper(substr($a->actor?->name ?? 'U',0,1)) }}
                </div>
                <div>
                    <strong>{{ $a->actor?->name ?? 'System' }}</strong>
                    <small class="text-muted"> • {{ $a->created_at->diffForHumans() }}</small>
                    <div class="small">{{ $a->message }}</div>

                    @foreach($a->attachments as $att)
                        <div>
                            <a href="{{ asset('storage/'.$att->file_path) }}" target="_blank">
                                📎 Attachment
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </li>
    @empty
        <li class="text-muted text-center">No activity yet</li>
    @endforelse
</ul>
