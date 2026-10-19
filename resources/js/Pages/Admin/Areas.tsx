import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Areas',
        href: '/admin/areas',
    },
];

export default function Areas() {
    const { areas } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Areas" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Areas" description="Manage the institutional areas catalog" />

                    <ul>
                        {areas.map((area) => {
                            return <li key={area.id}>{area.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
