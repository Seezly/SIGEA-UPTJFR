import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Categories',
        href: '/admin/job-categories',
    },
];

export default function JobCategories() {
    const { jobCategories } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Job Categories" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Job Categories" description="Manage the job categories catalog" />

                    <ul>
                        {jobCategories.map((jobCategory) => {
                            return <li key={jobCategory.id}>{jobCategory.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
