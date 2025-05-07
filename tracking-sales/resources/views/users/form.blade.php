@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $user->id ? 'Edit Commercial' : 'Add Commercial' }}</h2>

    <form action="{{ $user->id ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if($user->id)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label>Password {{ $user->id ? '(Leave blank to keep current password)' : '' }}</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">{{ $user->id ? 'Update' : 'Add' }}</button>
    </form>
</div>
@endsection
