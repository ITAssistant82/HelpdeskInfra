<style>
    .infra-tour-overlay { position: fixed; inset: 0; z-index: 9998; background: rgba(15, 23, 42, .62); }
    .infra-tour-highlight { position: fixed; z-index: 9999; border-radius: .65rem; outline: 3px solid #f59e0b; box-shadow: 0 0 0 9999px rgba(15, 23, 42, .62); pointer-events: none; transition: all .2s ease; }
    .infra-tour-popover { position: fixed; z-index: 10000; width: min(22rem, calc(100vw - 2rem)); padding: 1.25rem; border-radius: .8rem; background: #fff; color: #1f2937; box-shadow: 0 20px 45px rgba(15, 23, 42, .3); }
    .dark .infra-tour-popover { background: #1f2937; color: #f3f4f6; }
    .infra-tour-progress { margin: 0 0 .5rem; color: #b45309; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
    .infra-tour-popover h2 { margin: 0 0 .5rem; font-size: 1.05rem; font-weight: 700; }
    .infra-tour-popover p { margin: 0; color: #4b5563; font-size: .9rem; line-height: 1.5; }
    .dark .infra-tour-popover p { color: #d1d5db; }
    .infra-tour-actions { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; margin-top: 1.25rem; }
    .infra-tour-actions button { border: 0; border-radius: .45rem; padding: .5rem .75rem; font-size: .85rem; font-weight: 600; cursor: pointer; }
    .infra-tour-skip { margin-right: auto; color: #6b7280; background: transparent; }
    .infra-tour-next { color: #fff; background: #d97706; }
    .infra-tour-next:hover { background: #b45309; }
</style>

<script>
    (() => {
        const storageKey = 'infradesk-onboarding-completed';
        const pendingKey = 'infradesk-onboarding-step';
        const adminBase = @json(url('/admin'));
        let active = false;
        let currentStep = null;
        const field = (label) => Array.from(document.querySelectorAll('label'))
            .find((element) => element.textContent.trim().startsWith(label))
            ?.closest('[data-field-wrapper]') || null;

        const pages = {
            dashboard: [{
                target: () => document.querySelector('a[href="' + adminBase + '/tickets"]'),
                title: 'Kelola tiket dari sini',
                description: 'Klik menu Tickets untuk melihat laporan yang masuk atau membuat tiket baru.',
                next: 'tickets',
            }],
            tickets: [{
                target: () => document.querySelector('a[href="' + adminBase + '/tickets/create"]'),
                title: 'Buat tiket baru',
                description: 'Gunakan tombol ini ketika ingin melaporkan kendala atau meminta layanan IT.',
                next: 'create-ticket',
            }],
            'create-ticket': [
                {
                    target: () => field('Tipe'),
                    title: 'Pilih tipe tiket',
                    description: 'Pilih Incident untuk gangguan, atau Service Request untuk permintaan layanan.',
                },
                {
                    target: () => field('Kategori'),
                    title: 'Pilih kategori',
                    description: 'Pilih kategori yang paling sesuai agar tiket diteruskan ke tim IT yang tepat.',
                },
                {
                    target: () => field('Judul'),
                    title: 'Isi judul',
                    description: 'Tuliskan ringkasan kendala atau permintaan Anda secara singkat dan jelas.',
                },
                {
                    target: () => field('Deskripsi'),
                    title: 'Jelaskan kebutuhan Anda',
                    description: 'Sertakan detail masalah, pesan error, atau informasi lain yang membantu tim IT.',
                },
                {
                    target: () => field('Lokasi'),
                    title: 'Pilih lokasi',
                    description: 'Pilih lokasi Anda agar penanganan dapat dilakukan dengan tepat.',
                },
                {
                    target: () => field('Unit / Departemen'),
                    title: 'Isi unit atau departemen',
                    description: 'Kolom ini opsional, tetapi membantu kami memahami konteks permintaan Anda.',
                },
                {
                    target: () => field('Upload File') || field('Tambah File'),
                    title: 'Tambahkan lampiran',
                    description: 'Lampiran bersifat opsional. Anda dapat menambahkan screenshot atau dokumen pendukung bila diperlukan.',
                },
                {
                    target: () => Array.from(document.querySelectorAll('button[type="submit"]'))
                        .filter((button) => button.offsetParent !== null)
                        .at(-1),
                    title: 'Buat tiket',
                    description: 'Tombol Create Ticket ada di bagian paling bawah form. Periksa kembali informasi Anda, lalu klik tombol ini untuk mengirim tiket.',
                },
            ],
        };

        const tourOrder = [
            ['dashboard', 0],
            ['tickets', 0],
            ...pages['create-ticket'].map((_, index) => ['create-ticket', index]),
        ];
        const totalSteps = tourOrder.length;

        function globalStepNumber(step, index) {
            const position = tourOrder.findIndex(([stepName, stepIndex]) => stepName === step && stepIndex === index);
            return position < 0 ? 1 : position + 1;
        }

        function routeFor(step) {
            return { dashboard: adminBase, tickets: adminBase + '/tickets', 'create-ticket': adminBase + '/tickets/create' }[step];
        }

        function clearTour(completed = true) {
            document.querySelector('.infra-tour-overlay')?.remove();
            document.querySelector('.infra-tour-highlight')?.remove();
            document.querySelector('.infra-tour-popover')?.remove();
            active = false;
            currentStep = null;
            sessionStorage.removeItem(pendingKey);
            if (completed) localStorage.setItem(storageKey, 'true');
        }

        function showStep(step, index = 0) {
            const tourSteps = pages[step];
            const config = tourSteps?.[index];
            const target = config?.target();
            if (!config) return clearTour(false);
            if (!target) return showStep(step, index + 1);

            currentStep = step;
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => {
                const rect = target.getBoundingClientRect();
                const overlay = document.createElement('div');
                overlay.className = 'infra-tour-overlay';
                const highlight = document.createElement('div');
                highlight.className = 'infra-tour-highlight';
                Object.assign(highlight.style, { top: (rect.top - 5) + 'px', left: (rect.left - 5) + 'px', width: (rect.width + 10) + 'px', height: (rect.height + 10) + 'px' });
                const popover = document.createElement('section');
                popover.className = 'infra-tour-popover';
                const stepNumber = globalStepNumber(step, index);
                const isLast = stepNumber === totalSteps;
                popover.innerHTML = '<div class="infra-tour-progress">Langkah ' + stepNumber + ' dari ' + totalSteps + '</div><h2>' + config.title + '</h2><p>' + config.description + '</p><div class="infra-tour-actions"><button type="button" class="infra-tour-skip">Lewati</button><button type="button" class="infra-tour-next">' + (isLast ? 'Selesai' : 'Lanjut') + '</button></div>';
                const top = rect.bottom + 16 + 230 > window.innerHeight ? Math.max(16, rect.top - 225) : rect.bottom + 16;
                popover.style.top = top + 'px';
                popover.style.left = Math.min(Math.max(16, rect.left), window.innerWidth - Math.min(352, window.innerWidth - 32) - 16) + 'px';
                document.body.append(overlay, highlight, popover);
                popover.querySelector('.infra-tour-skip').onclick = () => clearTour();
                popover.querySelector('.infra-tour-next').onclick = () => {
                    if (config.next) {
                        sessionStorage.setItem(pendingKey, config.next);
                        window.location.assign(routeFor(config.next));
                    } else if (index < tourSteps.length - 1) {
                        overlay.remove(); highlight.remove(); popover.remove(); showStep(step, index + 1);
                    } else clearTour();
                };
            }, 350);
        }

        function start(step = 'dashboard') {
            if (active) return;
            active = true;
            const expected = routeFor(step);
            if (window.location.pathname !== new URL(expected).pathname) {
                sessionStorage.setItem(pendingKey, step);
                window.location.assign(expected);
                return;
            }
            window.setTimeout(() => showStep(step), 350);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[data-onboarding-tour-trigger]')?.addEventListener('click', () => start('dashboard'));
            const pending = sessionStorage.getItem(pendingKey);
            if (pending) {
                start(pending);
            } else if (!localStorage.getItem(storageKey) && window.location.pathname === new URL(adminBase).pathname) {
                start('dashboard');
            }
        });
    })();
</script>
