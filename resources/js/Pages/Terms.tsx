import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { TermsProps, type BreadcrumbItem, type TermSection } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import ReactMarkdown from 'react-markdown';

export default function Terms() {
    const { auth, institution, terms } = usePage<TermsProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Inicio', href: route('home') },
        { title: 'Términos del servicio', href: route('terms') },
    ];

    const sections: TermSection[] = Array.isArray(terms.sections)
        ? terms.sections.map((section: TermSection) => {
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
                <Hero title="Términos del servicio" image={institution.facade} breadcrumbs={breadcrumbs} />

                {/* Docs Section */}
                <section
                    id="docs"
                    aria-labelledby="docs-heading"
                    className="flex w-full flex-col-reverse items-center justify-center gap-4 px-4 py-8 lg:flex-row-reverse"
                >
                    <div className="flex w-full flex-col items-start justify-start gap-8 lg:max-w-4xl">
                        <div>
                            <h2 id="docs-heading">Términos del servicio</h2>
                            {sections.map((section: TermSection) => {
                                return (
                                    <article id={section.id} className="flex flex-col gap-4" key={section.id}>
                                        <h3>{section.title}</h3>
                                        <ReactMarkdown>{section.content}</ReactMarkdown>
                                    </article>
                                );
                            })}
                        </div>
                        <p>Última actualización: {terms.updated_at ?? 'Nunca'}</p>
                    </div>
                    <aside className="w-full lg:max-w-xs">
                        <h3>Indice</h3>
                        <ol>
                            {sections.map((section: TermSection) => {
                                return (
                                    <li key={section.id}>
                                        <a href={`#${section.id}`}>{section.title}</a>
                                    </li>
                                );
                            })}
                        </ol>
                    </aside>
                </section>
            </main>
            <Footer institution={institution} />
        </>
    );
}
