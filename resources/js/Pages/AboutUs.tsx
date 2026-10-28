import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { Button } from '@/components/ui/Button';
import { type AboutUsProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function AboutUs() {
    const { auth, institution } = usePage<AboutUsProps>().props;

    const breadcrumbs = [
        { title: 'Inicio', href: route('home') },
        { title: 'Nosotros', href: route('about-us') },
    ];

    return (
        <>
            <Head title="Nosotros">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />

            {/* Main Content */}

            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <Hero
                    title="Conectamos talento con oportunidades"
                    description={`Un espacio de encuentro entre la comunidad y el mundo laboral de la ${institution.name}.`}
                    image={institution.facade}
                    breadcrumbs={breadcrumbs}
                />

                {/* About Us Section */}
                <section
                    id="about-us"
                    aria-labelledby="about-us-heading"
                    className="flex w-full flex-col items-center justify-center gap-4 px-4 py-8"
                >
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div className="mb-4 lg:mb-0">
                                <h2 id="about-us-heading">Sobre nosotros</h2>
                                <p className="text-secondary-text">Conoce más sobre nuestra institución y sus valores.</p>
                            </div>
                            <div>
                                <p className="text-secondary-text">{institution.about_us}</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4 text-center">
                                <h3>Misión</h3>
                                <p className="text-secondary-text">{institution.mission}</p>
                            </div>
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4 text-center">
                                <h3>Visión</h3>
                                <p className="text-secondary-text">{institution.vision}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="principles"
                    aria-labelledby="principles-heading"
                    className="bg-background-darker flex w-full flex-col items-center justify-center gap-4 px-4 py-8"
                >
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 text-center">
                            <h2 id="principles-heading">Los valores que nos guían</h2>
                        </div>
                        <div className="grid grid-cols-1 gap-4 lg:grid-cols-4">
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4">
                                <h3>Compromiso</h3>
                                <p className="text-secondary-text">Acompañamos el desarrollo profesional de nuestra comunidad.</p>
                            </div>
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4">
                                <h3>Transparencia</h3>
                                <p className="text-secondary-text">Promovemos información clara para tomar decisiones laborales informadas.</p>
                            </div>
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4">
                                <h3>Inclusión</h3>
                                <p className="text-secondary-text">Valoramos la diversidad de talentos y la igualdad de oportunidades.</p>
                            </div>
                            <div className="border-muted flex flex-col items-start gap-2 rounded border bg-white p-4">
                                <h3>Colaboración</h3>
                                <p className="text-secondary-text">Impulsamos el trabajo en equipo para lograr objetivos comunes.</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* CTA Section */}

                <section
                    id="cta"
                    aria-labelledby="cta-heading"
                    className="flex w-full flex-col items-center justify-center gap-4 px-4 py-8 lg:min-h-screen"
                >
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 text-center">
                            <h2 id="cta-heading">¿Listo para comenzar un nuevo hito en tu carrera profesional?</h2>
                            <p className="text-secondary-text">
                                Únete a nuestro portal de empleos hoy. El registro y proceso de contratación es completamente gratuito
                            </p>
                        </div>
                        <div className="flex w-full flex-col items-center gap-2 lg:flex-row lg:justify-center">
                            <Button asChild variant="outline" className="w-full lg:w-auto">
                                <Link href={route('register')}>Crear una cuenta</Link>
                            </Button>
                            <Button asChild variant="outline-light" className="w-full lg:w-auto">
                                <Link href={route('register')}>Explorar los empleos disponibles</Link>
                            </Button>
                        </div>
                    </div>
                </section>
            </main>
            <Footer institution={institution} />
        </>
    );
}
