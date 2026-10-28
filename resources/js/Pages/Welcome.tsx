import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { Button } from '@/components/ui/Button';
import { type Job, type WelcomeProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth, jobs, institution } = usePage<WelcomeProps>().props;
    function dateFormat(date: string): string {
        const jobDate = new Date(date).getTime();
        const today = Date.now();
        let formattedDate;

        const diff = Math.abs(today - jobDate);

        const minutes = Math.floor(diff / 1000 / 60);

        const hours = Math.floor(minutes / 60);

        const days = Math.floor(hours / 24);

        const weeks = Math.floor(days / 7);

        const months = Math.floor(weeks / 4);

        if (months > 0) {
            formattedDate = `${months} mese(s)`;
        } else if (weeks > 0) {
            formattedDate = `${weeks} semana(s)`;
        } else if (days > 0) {
            formattedDate = `${days} dia(s)`;
        } else if (hours > 0) {
            formattedDate = `${hours} hora(s)`;
        } else {
            formattedDate = `${minutes} minuto(s)`;
        }

        return formattedDate;
    }

    return (
        <>
            <Head title="Inicio">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />

            {/* Main Content */}

            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <Hero
                    title="Impulsa tu futuro con nosotros"
                    description="Conecta con las propuestas de trabajo ofrecidas por nuestra prestigiosa universidad. Empieza tu carrera profesional con nosotros hoy."
                    image={institution.facade}
                >
                    <form
                        action=""
                        role="search"
                        aria-label="Buscar vacantes"
                        className="bg-background text-text flex w-full flex-col gap-4 rounded p-2 lg:flex-row"
                    >
                        <label htmlFor="job-search" className="border-background flex flex-col rounded border bg-white p-2 lg:w-10/12">
                            <div className="flex w-full items-center justify-between gap-4 lg:justify-center">
                                <div className="size-4 lg:size-8" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                        <path
                                            className="fill-mute"
                                            d="M480 272C480 317.9 465.1 360.3 440 394.7L566.6 521.4C579.1 533.9 579.1 554.2 566.6 566.7C554.1 579.2 533.8 579.2 521.3 566.7L394.7 440C360.3 465.1 317.9 480 272 480C157.1 480 64 386.9 64 272C64 157.1 157.1 64 272 64C386.9 64 480 157.1 480 272zM272 416C351.5 416 416 351.5 416 272C416 192.5 351.5 128 272 128C192.5 128 128 192.5 128 272C128 351.5 192.5 416 272 416z"
                                        />
                                    </svg>
                                </div>
                                <div className="flex flex-1 flex-col">
                                    Busca una vacante por título o palabras clave
                                    <input
                                        className="w-full outline-none"
                                        type="search"
                                        name="job-search"
                                        autoComplete="off"
                                        id="job-search"
                                        placeholder="ej. Profesor de física"
                                    />
                                </div>
                            </div>
                        </label>
                        <Button type="submit" variant="outline" className="cursor-pointer lg:w-2/12">
                            Buscar
                        </Button>
                    </form>
                </Hero>
                <section
                    id="featured-jobs"
                    aria-labelledby="featured-jobs-heading"
                    className="relative flex w-full items-center justify-center gap-4 px-4 py-8"
                >
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 flex items-center justify-between">
                            <div>
                                <h2 id="featured-jobs-heading">Oportunidades destacadas</h2>
                                <p className="text-secondary-text">Descubre los últimos puestos disponibles en nuestra universidad</p>
                            </div>
                            <div className="mt-2 hidden text-center md:block">
                                <Button asChild variant="link">
                                    <Link href="/jobs">Ver todos los empleos &rarr;</Link>
                                </Button>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {jobs === null ? (
                                <p role="alert" className="text-muted col-span-full text-center">
                                    Ha ocurrido un error, por favor inténtelo más tarde
                                </p>
                            ) : jobs.length > 0 ? (
                                jobs.map((job: Job) => (
                                    <div
                                        className="border-muted flex h-auto w-full flex-col justify-start gap-2 rounded border p-4 shadow transition-shadow hover:shadow-md active:shadow-sm"
                                        data-id={job.id}
                                        key={job.id}
                                    >
                                        <Link href={`/jobs/${job.id}`}>
                                            <div className="flex items-center justify-between">
                                                <div>{job.category}</div>
                                                <div className="bg-lighter-blue flex items-center justify-center px-4 py-2">
                                                    <span className="text-lighter-blue-foreground">{job.employment_type}</span>
                                                </div>
                                            </div>
                                            <div className="my-4 flex flex-col">
                                                <p className="text-3xl font-semibold">{job.title}</p>
                                                <p className="text-mute text-xs">{`${job.area} - ${job.position}`}</p>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <p className="text-mute text-sm">
                                                    Publicado hace <span className="text-blue">{dateFormat(job.published_at)}</span>
                                                </p>
                                                {job.application_deadline ? (
                                                    <p className="text-mute text-sm">
                                                        Expira en <span className="text-blue">{dateFormat(job.application_deadline)}</span>
                                                    </p>
                                                ) : null}
                                            </div>
                                        </Link>
                                    </div>
                                ))
                            ) : (
                                <p aria-live="polite" className="text-muted col-span-full text-center">
                                    No hay oportunidades de empleo disponibles en este momento
                                </p>
                            )}
                        </div>

                        <div className="mt-4 block text-center md:hidden">
                            <Button asChild variant="link">
                                <Link href="/jobs">Ver todos los empleos &rarr;</Link>
                            </Button>
                        </div>
                    </div>
                </section>
                <section
                    id="about"
                    aria-labelledby="about-heading"
                    className="bg-background-darker flex w-full flex-col items-center justify-center px-4 py-8"
                >
                    <div className="flex w-full max-w-6xl flex-col">
                        <div className="mb-8 text-center">
                            <h2 id="about-heading">Apoyamos tu crecimiento</h2>
                            <p className="text-secondary-text">Cerramos la brecha entre la excelencia académica y el saber profesional</p>
                        </div>
                        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                            <div className="bg-blue text-blue-foreground flex flex-col gap-2 rounded p-8 lg:col-span-2">
                                <div>Icon</div>
                                <h3>Para profesionales</h3>
                                <p className="text-lighter-blue">
                                    Acceso exclusivo a oportunidades de empleo dentro de la universidad. De profesionales para formar profesionales.
                                </p>
                            </div>
                            <div className="border-muted flex flex-col gap-2 rounded border bg-white p-8 lg:col-span-1">
                                <div>Icon</div>
                                <h3>Comunidad verificada</h3>
                                <p className="text-secondary-text">
                                    Todas las propuestas exhibidas son previamente aprobadas por las comisiones y organismos necesarios dentro de la
                                    universidad.
                                </p>
                            </div>
                            <div className="border-muted flex flex-col gap-2 rounded border bg-white p-8 lg:col-span-1">
                                <div>Icon</div>
                                <h3>Soporte de Carrera Profesional</h3>
                                <p className="text-secondary-text">
                                    Acceso a recursos, estadísticas y preparaciones para entrevistas por nuestro centro de empleos.
                                </p>
                            </div>
                            <div className="border-muted flex flex-col items-center gap-2 rounded border bg-white p-8 lg:col-span-2">
                                <h3>¿Buscas un nuevo trabajo?</h3>
                                <p className="text-secondary-text text-center">
                                    Considera la Universidad Politécnica Territorial José Félix Ribas como tu punto de partida hacia un trabajo de
                                    punta. Visita las propuestas, verifica el feedback de tus postulaciones, historial y muéstranos tu talento.
                                </p>
                                <Button asChild variant="default" className="mt-auto w-full">
                                    <Link href={route('register')}>Regístrate &rarr;</Link>
                                </Button>
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
