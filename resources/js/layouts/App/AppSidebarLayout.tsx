import { AppContent } from '@/components/AppContent';
import { AppShell } from '@/components/AppShell';
import { AppSidebar } from '@/components/AppSidebar';
import { AppSidebarHeader } from '@/components/AppSidebarHeader';
import { type BreadcrumbItem } from '@/types';

export default function AppSidebarLayout({ children, breadcrumbs = [] }: { children: React.ReactNode; breadcrumbs?: BreadcrumbItem[] }) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
