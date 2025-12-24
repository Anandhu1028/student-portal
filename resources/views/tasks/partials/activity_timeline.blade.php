<ul class="list-unstyled mb-0">

@forelse($activities as $activity)

@php
    $actor = $activity->actor->name ?? 'System';
    $avatar = strtoupper(substr($actor, 0, 1));
@endphp

<li class="mb-3 d-flex gap-3">

    <div class="rounded-circle bg-primary text-white
                d-flex align-items-center justify-content-center"
         style="width:36px;height:36px;font-weight:600;">
        {{ $avatar }}
    </div>

    <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
            <strong>{{ $actor }}</strong>
            <small class="text-muted">
                {{ $activity->created_at->diffForHumans() }}
            </small>
        </div>

        <div class="mt-1">
            {{ $activity->message }}
        </div>

        @if($activity->attachments->count())
            <div class="mt-2">
                @foreach($activity->attachments as $file)
                    <a href="{{ asset('storage/'.$file->file_path) }}"
                       target="_blank"
                       class="me-2 text-decoration-none">
                        📎 Attachment
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</li>

@empty
<li class="text-center text-muted py-3">
    No activity yet
</li>
@endforelse

</ul>
