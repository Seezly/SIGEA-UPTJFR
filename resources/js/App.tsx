import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { route as routeFn } from 'ziggy-js';
import { initializeTheme } from './hooks/UseAppearance';

declare global {
    const route: typeof routeFn;
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
