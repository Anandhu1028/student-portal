<!----------------------------------------------
       THIS THE ACTIVITY TIMELINE SECTION.
     ------------------------------------------------>


<ul class="list-unstyled mb-0 activity-timeline">

    @forelse($activities as $activity)

    @php
    $actorName = optional($activity->actor)->name ?? 'System';
    $avatar = strtoupper(mb_substr($actorName, 0, 1));
    @endphp

    <li class="activity-item d-flex gap-3 mb-3">

        {{-- AVATAR --}}
        <div class="activity-avatar">
            {{ $avatar }}
        </div>

        {{-- CONTENT --}}
        <div class="flex-grow-1">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-start">
                <strong class="activity-user">
                    {{ $actorName }}
                </strong>

                <small class="activity-time">
                    {{ $activity->created_at->diffForHumans() }}
                </small>
            </div>

            {{-- MESSAGE --}}
            <div class="activity-message mt-1">
                {!! nl2br(e(trim($activity->message))) !!}
            </div>

            {{-- ATTACHMENTS --}}
            @if($activity->attachments->count())
            <div class="activity-attachments mt-2 d-flex flex-wrap gap-2">
                @foreach($activity->attachments as $file)
                <a href="{{ asset('storage/'.$file->file_path) }}"
                    target="_blank"
                    class="activity-attachment"
                    title="{{ $file->original_name }}">

                    <img src="{{ asset('storage/'.$file->file_path) }}"
                        alt="attachment"
                        loading="lazy">
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



<style>
    /* ================= ACTIVITY TIMELINE ================= */

    .activity-timeline {
        font-size: 13px;
    }

    .activity-item {
        align-items: flex-start;
    }

    /* Avatar */
    .activity-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0c768a;
        color: #fff;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Header */
    .activity-user {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .activity-time {
        font-size: 11px;
        color: #6b7280;
        white-space: nowrap;
    }

    /* Message */
    .activity-message {
        font-size: 13px;
        line-height: 1.6;
        color: #F0c768;
        margin-top: 4px;
    }

    .activity-message br {
        display: block;
        margin-bottom: 4px;
    }

    /* Attachments */
    /* .activity-attachments {
        margin */
        
</style>