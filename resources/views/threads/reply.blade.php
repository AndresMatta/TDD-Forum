<div class="card">
    <div class="card-header">
        <div class="level">
            <p class="flex">
                <a href="#">
                    {{ $reply->owner->name }}
                </a> said {{ $reply->created_at->diffForHumans() }}
            </p>
            <div>
                <form method="POST" action="/replies/{{ $reply->id }}/favorites">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                        {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>