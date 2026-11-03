import { Docs } from '@/components/Docs';
import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { TermsProps, type BreadcrumbItem, type LegalSection } from '@/types';
import { Head, usePage } from '@inertiajs/react';

export default function Terms() {
    const { auth, institution, terms } = usePage<TermsProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Inicio', href: route('home') },
        { title: 'Términos del servicio', href: route('terms') },
    ];

    const sections: LegalSection[] = Array.isArray(terms.sections)
        ? terms.sections.map((section: LegalSection) => {
              return {
                  id: section.id,
                  title: section.title,
                  content: section.content,
              };
          })
        : [
              {
                  id: 'default',
                  title: 'Términos del servicio',
                  content: 'No hay términos disponibles.',
              },
          ];

    return (
        <>
            <Head title="Términos del servicio">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />
            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <Hero
                    title="Términos del servicio"
                    description={`Condiciones propuestas para un uso responsable de la ${institution.name} y sus servicios.`}
                    image={institution.facade}
                    breadcrumbs={breadcrumbs}
                />

                {/* Docs Section */}
                <Docs sections={sections} updatedAt={terms.updated_at} />
            </main>
            <Footer institution={institution} />
        </>
    );
}
