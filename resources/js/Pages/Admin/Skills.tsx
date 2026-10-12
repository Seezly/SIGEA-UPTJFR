import HeadingSmall from '@/components/HeadingSmall';
import AppLayout from '@/layouts/AppLayout';
import SettingsLayout from '@/layouts/Settings/Layout';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Skills',
        href: '/admin/skills',
    },
];

export default function Skills() {
    const { skills } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Skills" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Skills" description="Manage the skills catalog" />

                    <ul>
                        {skills.map((skill) => {
                            return <li key={skill.id}>{skill.name}</li>;
                        })}
                    </ul>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
