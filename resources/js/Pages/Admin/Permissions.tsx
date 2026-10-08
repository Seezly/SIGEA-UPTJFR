import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permisos',
        href: '/admin/permissions',
    },
];

export default function Permissions() {
    const { permissions } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Permisos" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your name and email address" />

                    <ul>
                        {permissions.map((permission) => {
                            return <li key={permission.id}>{permission.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
