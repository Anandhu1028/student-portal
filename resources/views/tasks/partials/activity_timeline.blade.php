<ul class="list-unstyled mb-0">

    @forelse($activities as $activity)

        @php
            $actorName = optional($activity->actor)->name ?? 'System';
            $avatar = strtoupper(mb_substr($actorName, 0, 1));
        @endphp

        <li class="mb-3 d-flex gap-3">

            {{-- AVATAR --}}
            <div class="rounded-circle bg-primary text-white
                    d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:600;">
                {{ $avatar }}
            </div>

            {{-- CONTENT --}}
            <div class="flex-grow-1">

                <div class="d-flex justify-content-between align-items-center">
                    <strong>{{ $actorName }}</strong>
                    <small class="text-muted">
                        {{ $activity->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="mt-1">
                    {{ $activity->message }}
                </div>

                {{-- ATTACHMENTS --}}
                @if($activity->attachments && $activity->attachments->count())
                    <div class="mt-2 small text-muted">
                        @foreach($activity->attachments as $file)
                            <div>
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                    class="text-decoration-none text-primary">
                                    📎 {{ basename($file->file_path) }}
                                </a>
                            </div>
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