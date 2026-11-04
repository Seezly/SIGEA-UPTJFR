import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'sonner';
import { route as routeFn } from 'ziggy-js';

declare global {
    const route: typeof routeFn;
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    setup({ el, App, props }) {
        if (!el) return;

        const root = createRoot(el);

        root.render(
            <>
                <Toaster richColors position="bottom-right" closeButton />

                <App {...props} />
            </>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
// initializeTheme();
