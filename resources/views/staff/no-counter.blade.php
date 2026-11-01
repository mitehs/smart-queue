@extends('layouts.staff')

@section('title', 'No Counter Assigned')
@section('page-title', 'My Counter')
@section('page-subtitle', 'Your counter assignment')
@section('active', '')

@section('content')
    <div class="card p-8 sm:p-12 max-w-xl mx-auto text-center mt-8">
        <div class="w-16 h-16 rounded-full brand-soft brand-text grid place-items-center text-3xl mx-auto mb-5">🏢</div>
        <h2 class="text-xl font-bold text-slate-800 mb-2">No counter assigned yet</h2>
        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Please contact your administrator to assign you to a counter/window.
            Once a counter is assigned, your queue tools will appear in the sidebar and you can start serving customers.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-brand">Back to My Counter</a>
            <a href="{{ route('profile.edit') }}" class="btn btn-ghost">Edit my profile</a>
        </div>
    </div>
@endsection
