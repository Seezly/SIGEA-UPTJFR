import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/admin/roles',
    },
];

export default function Modules() {
    const { modules } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Módulos" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your name and email address" />

                    <ul>
                        {modules.map((module) => {
                            return <li key={module.id}>{module.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
