@extends('layouts.user')

@section('title', 'Contact Us')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Contact us</h1>
            <p class="text-sm text-slate-500">Send us a message and we'll get back to you as soon as possible.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Form --}}
            <div class="lg:col-span-2">
                <div class="card p-5 sm:p-6">
                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="lbl" for="name">Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" class="inp" required>
                                @error('name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="lbl" for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="inp" required>
                                @error('email')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="lbl" for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="What is this about?" class="inp" required>
                            @error('subject')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label class="lbl" for="message">Message</label>
                            <textarea id="message" name="message" rows="6" class="inp" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-brand mt-5">Send Message</button>
                    </form>
                </div>
            </div>

            {{-- Info sidebar --}}
            <div class="space-y-4">
                <div class="card p-5">
                    <h3 class="font-semibold text-slate-800 mb-4">Reach us directly</h3>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <span class="text-lg">📍</span>
                            <div>
                                <div class="font-semibold text-slate-700">Address</div>
                                <div class="text-slate-500">123 Queue Street, Smart City, 10100</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-lg">📞</span>
                            <div>
                                <div class="font-semibold text-slate-700">Phone</div>
                                <div class="text-slate-500">+1 (555) 123-4567</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-lg">✉️</span>
                            <div>
                                <div class="font-semibold text-slate-700">Email</div>
                                <div class="text-slate-500">support@smartqueue.test</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-lg">🕐</span>
                            <div>
                                <div class="font-semibold text-slate-700">Working hours</div>
                                <div class="text-slate-500">09:00 – 17:00, Mon – Fri</div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card p-5 brand-soft border-blue-100">
                    <h3 class="font-semibold text-slate-800">Quick question?</h3>
                    <p class="text-sm text-slate-600 mt-1">Many answers are already in the Help Center — booking, cancelling, missed turns and more.</p>
                    <a href="{{ route('help') }}" class="btn btn-outline mt-3">Visit Help Center</a>
                </div>
            </div>
        </div>
    </div>
@endsection
