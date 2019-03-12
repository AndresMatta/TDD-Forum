@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="pb-2 mt-4 mb-2 border-bottom">
                   <avatar-form :user="{{ $profileUser }}"></avatar-form>
                </div>

                @forelse ($activities as $date => $record)
                    <div class="pb-2 mt-4 mb-2 border-bottom">
                        {{ $date }}
                    </div>
                    @foreach ($record as $activity)
                        @if(view()->exists("profiles.activities.{$activity->type}"))
                            @include("profiles.activities.{$activity->type}")
                        @endif
                        <br>
                    @endforeach
                @empty
                    <p>There is not activity for this user yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection