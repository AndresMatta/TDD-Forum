@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @forelse($threads as $thread)
                <div class="card">
                    <div class="card-header">
                        <div class=level>
                            <h4 class="flex">
                                <a href="{{ $thread->path() }}">
                                    @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                        <strong>
                                            {{ $thread->title }}
                                        </strong>
                                    @else
                                        {{ $thread->title }}
                                    @endif
                                </a>
                            </h4>
                            <strong>
                                <a href="{{ $thread->path() }}">{{ $thread->replies_count }} {{ str_plural('reply'), $thread->replies_count }}</a>
                            </strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>{{ $thread->body }}</div>
                    </div>
                </div>
                <br>
            @empty
                <p>There are no relevant results at this time.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
