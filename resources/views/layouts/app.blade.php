<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SweetAlert2 (load early so pages can call Swal) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-200"
    x-data="{}" x-bind:class="{ 'overflow-hidden': $store.layout.mobileSidebarOpen }"
    x-on:keydown.window.escape="$store.layout.closeMobileSidebar()">
    <div class="min-h-screen flex">
        @include('layouts.sidebar')

        <div class="flex min-h-screen flex-1 flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-slate-200 bg-transparent dark:border-slate-800 dark:bg-transparent">
                    <div class="mx-auto flex w-full items-center px-6 py-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                <div class="mx-auto min-h-full w-full px-6 py-8 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <script>
        // Delegated confirmation handlers for approve/delete buttons.
        document.addEventListener('click', function (e) {
            const approveBtn = e.target.closest('[data-confirm-approve]');
            if (approveBtn) {
                e.preventDefault();
                const formId = approveBtn.dataset.confirmApprove;
                const name = approveBtn.dataset.participantName || '';
                const title = name ? `Setujui pengajuan ${name}?` : 'Setujui pengajuan ini?';

                const doSubmit = () => {
                    const form = document.getElementById(formId);
                    if (form) form.submit();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: 'Tindakan ini akan menyetujui pengajuan peserta.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Setujui',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10B981',
                    }).then((result) => {
                        if (result.isConfirmed) doSubmit();
                    });
                } else {
                    if (confirm(title + '\n\nTindakan ini akan menyetujui pengajuan peserta.')) doSubmit();
                }
            }

            const deleteBtn = e.target.closest('[data-confirm-delete]');
            if (deleteBtn) {
                e.preventDefault();
                const formId = deleteBtn.dataset.confirmDelete;
                const name = deleteBtn.dataset.participantName || '';
                const title = name ? `Hapus peserta ${name}?` : 'Hapus peserta ini?';

                const doSubmit = () => {
                    const form = document.getElementById(formId);
                    if (form) form.submit();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: 'Tindakan ini tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#DC2626',
                    }).then((result) => {
                        if (result.isConfirmed) doSubmit();
                    });
                } else {
                    if (confirm(title + '\n\nTindakan ini tidak dapat dikembalikan.')) doSubmit();
                }
            }

            const rejectBtn = e.target.closest('[data-confirm-reject]');
            if (rejectBtn) {
                e.preventDefault();
                const requestId = rejectBtn.dataset.confirmReject;
                const name = rejectBtn.dataset.participantName || '';
                const title = name ? `Tolak pengajuan ${name}?` : 'Tolak pengajuan ini?';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: 'Berikan alasan penolakan (opsional)',
                        input: 'textarea',
                        inputPlaceholder: 'Jelaskan alasan penolakan...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tolak Pengajuan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#DC2626',
                        inputValidator: () => {
                            // Allow empty notes
                            return null;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/position-requests/${requestId}/reject`;

                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);

                            if (result.value) {
                                const notesInput = document.createElement('input');
                                notesInput.type = 'hidden';
                                notesInput.name = 'notes';
                                notesInput.value = result.value;
                                form.appendChild(notesInput);
                            }

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                } else {
                    const notes = prompt(title + '\n\nBerikan alasan penolakan (opsional):');
                    if (notes !== null) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/position-requests/${requestId}/reject`;

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);

                        if (notes) {
                            const notesInput = document.createElement('input');
                            notesInput.type = 'hidden';
                            notesInput.name = 'notes';
                            notesInput.value = notes;
                            form.appendChild(notesInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }
        });
    </script>
</body>

</html>
