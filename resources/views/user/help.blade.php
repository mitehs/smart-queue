@extends('layouts.user')

@section('title', 'Help Center')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Help Center</h1>
            <p class="text-sm text-slate-500">Answers to the most common questions about Smart Queue.</p>
        </div>

        <div class="card p-5 sm:p-6 mb-6">
            <h2 class="font-semibold text-slate-800 mb-4">Frequently asked questions</h2>

            <div class="space-y-3">
                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        How do I book a token?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Log in, click <strong>Book Token</strong> in the menu, then follow the steps: choose a place, pick the service you need, select a counter, and press <strong>Get My Token ✓</strong>. Your token number appears instantly with an estimated wait time.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        How do I know when it's my turn?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Open the <strong>Track live</strong> screen from your dashboard — it refreshes automatically every few seconds. When your token is called, a highlighted banner tells you which counter to go to. You can also listen for the counter announcement at the venue.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        Can I cancel my token?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Yes. While your token is still <strong>Waiting</strong>, open the live tracking page and click <strong>Cancel Token</strong>. You'll be asked to confirm. Once your token has been called, it can no longer be cancelled — contact the venue instead.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        What does my token number mean?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Your token number shows your position in the queue for that service, such as <strong>A-42</strong>. The letter refers to the service and the number is your place in line. "People ahead of you" tells you exactly how many tokens will be served before yours.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        What happens if I miss my turn?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        If you're not at the counter when your token is called, staff may mark it as <strong>Skipped</strong> or <strong>No show</strong>. You can ask the counter to recall your token if the staff member is still available, otherwise you'll need to book a new one.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        How do I change my personal details?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Go to <strong>Update Information</strong> (Dashboard → Update Info) to change your name, phone or email before you're served. For your password, use <strong>Settings</strong> in the mobile menu or the profile page.
                    </p>
                </details>

                <details class="group border border-slate-200 rounded-lg p-4 open:shadow-sm">
                    <summary class="cursor-pointer font-semibold text-slate-700 flex items-center justify-between list-none">
                        Where can I see my past visits?
                        <span class="text-slate-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Open <strong>History</strong> to search and filter every token you've ever taken. The <strong>My contribution</strong> page shows charts of how often you visit and which services you use most.
                    </p>
                </details>
            </div>
        </div>

        {{-- Still need help --}}
        <div class="card p-6 brand-soft border-blue-100 text-center">
            <h3 class="font-bold text-slate-800">Still need help?</h3>
            <p class="text-sm text-slate-600 mt-1">Can't find what you're looking for? Our team is happy to help — 09:00 to 17:00, Monday to Friday.</p>
            <a href="{{ route('contact') }}" class="btn btn-brand mt-4">Contact Support</a>
        </div>
    </div>
@endsection
