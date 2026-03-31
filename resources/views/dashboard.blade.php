<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Review the project quickly, then jump into the messenger demo.
                </p>
            </div>

            <a href="{{ route('messenger') }}" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white shadow transition" style="background: #0284c7;">
                Open Messenger
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-sky-600">Conversations</p>
                    <p class="mt-3 text-3xl font-black text-gray-900">{{ $stats['conversations'] }}</p>
                    <p class="mt-2 text-sm text-gray-500">Active conversations ready for demo.</p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-emerald-600">Contacts</p>
                    <p class="mt-3 text-3xl font-black text-gray-900">{{ $stats['contacts'] }}</p>
                    <p class="mt-2 text-sm text-gray-500">Seeded contacts available in the messenger.</p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-amber-600">Unread</p>
                    <p class="mt-3 text-3xl font-black text-gray-900">{{ $stats['unread'] }}</p>
                    <p class="mt-2 text-sm text-gray-500">Unread items to showcase live activity.</p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-indigo-600">Attachments</p>
                    <p class="mt-3 text-3xl font-black text-gray-900">{{ $stats['attachments'] }}</p>
                    <p class="mt-2 text-sm text-gray-500">Shared files currently available in chats.</p>
                </section>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-blue-600">Navigation</p>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Clean page flow</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        Use the dashboard as your control point, then open the messenger page for the main product presentation.
                    </p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-green-600">Presentation</p>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Portfolio ready</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        The public home page, auth screens, dashboard, and messenger now follow a clearer navigation model.
                    </p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <p class="text-sm font-medium text-indigo-600">Quick access</p>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Main demo screen</h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        Open the messenger to showcase conversations, unread counters, real-time updates, and attachments.
                    </p>
                </section>
            </div>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-sm font-medium text-slate-500">Presentation checklist</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">What to show during the portfolio walkthrough</h3>
                        <p class="mt-3 text-sm leading-7 text-gray-600">
                            Start at the public home page, move into the dashboard, then present the messenger workflow: open a conversation, send a message, attach a file, and demonstrate unread updates.
                        </p>
                    </div>

                    <div class="grid gap-3 text-sm text-gray-600">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">1. Public landing page</div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">2. Auth flow and dashboard</div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">3. Conversation search and filtering</div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">4. Message send, delete, and attachments</div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl p-8 text-white shadow-xl" style="background: linear-gradient(90deg, #0f172a 0%, #1e293b 50%, #0c4a6e 100%);">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm uppercase tracking-widest" style="color: #bae6fd;">Ready for demo</p>
                        <h3 class="mt-3 text-3xl font-bold">Present the messenger with confidence</h3>
                        <p class="mt-3 text-sm leading-7" style="color: #e2e8f0;">
                            Start from here, then move into the chat workspace for a focused walkthrough without broken routes or placeholder links.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('messenger') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 transition hover:bg-gray-100">
                            Open Messenger
                        </a>
                        <a href="{{ route('home') }}" class="rounded-xl px-5 py-3 text-sm font-semibold text-white transition" style="border: 1px solid rgba(255, 255, 255, 0.2);">
                            Public Home
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
