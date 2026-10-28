import { Breadcrumbs } from '@/components/Breadcrumbs';
import { BreadcrumbItem } from '@/types';

export function Hero({
    title,
    description,
    image,
    breadcrumbs,
    children,
}: {
    title: string;
    description?: string;
    image: string;
    breadcrumbs?: BreadcrumbItem[];
    children?: React.ReactNode;
}) {
    return (
        <section
            id="hero"
            aria-labelledby="hero-heading"
            className="text-blue-foreground relative flex w-full flex-col items-center justify-center gap-4 px-4 py-8 lg:min-h-[75vh]"
        >
            <div className="after:bg-blue/80 absolute inset-0 z-0 after:absolute after:inset-0">
                <img src={image} className="h-full w-full object-cover" alt="" aria-hidden="true" />
            </div>
            <div className="relative z-10 flex w-full max-w-6xl flex-col items-center gap-4">
                {breadcrumbs && <Breadcrumbs breadcrumbs={breadcrumbs} />}
                <div className="mb-8 text-center">
                    <h1 id="hero-heading">{title}</h1>
                    <p className="text-lighter-blue">{description}</p>
                </div>
                {children}
            </div>
        </section>
    );
}
