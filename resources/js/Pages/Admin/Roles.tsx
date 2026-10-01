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

export default function Roles() {
    const { roles } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roles" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile information" description="Update your name and email address" />

                    <ul>
                        {roles.map((role) => {
                            return <li key={role.id}>{role.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
