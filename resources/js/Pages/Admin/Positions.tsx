import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Positions',
        href: '/admin/positions',
    },
];

export default function Positions() {
    const { positions } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Positions" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Positions" description="Manage the institutional positions catalog" />

                    <ul>
                        {positions.map((position) => {
                            return <li key={position.id}>{position.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
