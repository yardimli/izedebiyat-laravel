@php
    $avatarName = trim($user->name ?: $user->username ?: '?');
    $initial = mb_strtoupper(mb_substr($avatarName, 0, 1));
    $avatarColors = ['#9adfe1', '#a996e5', '#c6e796', '#e99da3', '#99bee9', '#d09ae8', '#eadf9b'];
    $avatarColor = $avatarColors[$user->id % count($avatarColors)];
@endphp
@if($user->avatar)
    <img class="forum-avatar {{ $class ?? '' }}" src="{{ Storage::url($user->avatar) }}" alt="{{ $avatarName }}">
@else
    <span class="forum-avatar forum-avatar-initial {{ $class ?? '' }}" style="--avatar-color: {{ $avatarColor }}">{{ $initial }}</span>
@endif
