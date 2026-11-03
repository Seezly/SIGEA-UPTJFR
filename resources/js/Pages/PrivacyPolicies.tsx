import { Docs } from '@/components/Docs';
import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { PrivacyPoliciesProps, type BreadcrumbItem, type LegalSection } from '@/types';
import { Head, usePage } from '@inertiajs/react';

export default function PrivacyPolicies() {
    const { auth, institution, policies } = usePage<PrivacyPoliciesProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Inicio', href: route('home') },
        { title: 'Políticas de privacidad', href: route('privacy-policies') },
    ];

    const sections: LegalSection[] = Array.isArray(policies.sections)
        ? policies.sections.map((section: LegalSection) => {
              return {
                  id: section.id,
                  title: section.title,
                  content: section.content,
              };
          })
        : [
              {
                  id: 'default',
                  title: 'Políticas de privacidad',
                  content: 'No hay Políticas de privacidad disponibles.',
              },
          ];

    return (
        <>
            <Head title="Políticas de privacidad">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />
            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <Hero
                    title="Políticas de privacidad"
                    description={`Criterios propuestos para el tratamiento de datos personales en la ${institution.name}.`}
                    image={institution.facade}
                    breadcrumbs={breadcrumbs}
                />

                {/* Docs Section */}
                <Docs sections={sections} updatedAt={policies.updated_at} />
            </main>
            <Footer institution={institution} />
        </>
    );
}
