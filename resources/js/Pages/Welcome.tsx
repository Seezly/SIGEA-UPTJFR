import { type Job, type WelcomeProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth, jobs, institution } = usePage<WelcomeProps>().props;

    function handleMobileMenuToggle() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('max-h-0');
                mobileMenu.classList.toggle('py-8');
                mobileMenu.classList.toggle('px-4');
            });
        }
    }

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

            {/* Header */}

            <header className="bg-blue text-blue-foreground sticky top-0 z-1000">
                <div className="flex w-full items-center justify-between p-4">
                    <div className="flex flex-1 items-center justify-start gap-2">
                        <img className="size-8" src={institution.logo} alt='Logo de la Universidad Politécnica Territorial "José Félix Ribas"' />
                    </div>
                    <nav className="hidden flex-1 items-center justify-center lg:flex">
                        <ul className="flex flex-1 items-center justify-center gap-8">
                            {/* TODO change href to real ones */}
                            <li className="flex items-center justify-center">
                                <Link
                                    className="text-lighter-blue active:text-muted after:bg-lighter-blue relative px-4 py-2 transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all hover:text-white hover:after:w-full active:after:w-0"
                                    href="#"
                                >
                                    Inicio
                                </Link>
                            </li>
                            <li className="flex items-center justify-center">
                                <Link
                                    className="text-lighter-blue active:text-muted after:bg-lighter-blue relative px-4 py-2 transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all hover:text-white hover:after:w-full active:after:w-0"
                                    href="#"
                                >
                                    Empleos
                                </Link>
                            </li>
                            <li className="flex items-center justify-center">
                                <Link
                                    className="text-lighter-blue active:text-muted after:bg-lighter-blue relative px-4 py-2 transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all hover:text-white hover:after:w-full active:after:w-0"
                                    href="#"
                                >
                                    Nosotros
                                </Link>
                            </li>
                            <li className="flex items-center justify-center">
                                <Link
                                    className="text-lighter-blue active:text-muted after:bg-lighter-blue relative px-4 py-2 transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all hover:text-white hover:after:w-full active:after:w-0"
                                    href="#"
                                >
                                    Contacto
                                </Link>
                            </li>
                        </ul>
                    </nav>
                    <div className="hidden flex-1 items-center justify-end gap-4 lg:flex">
                        {auth.user ? (
                            <Link href="#">Mi Perfil</Link>
                        ) : (
                            <>
                                <Link href={route('register')}>
                                    <button className="text-blue w-full cursor-pointer bg-white px-4 py-2 font-semibold">Regístrate</button>
                                </Link>
                                <Link href={route('login')}>
                                    <button className="bg-blue text-blue-foreground w-full cursor-pointer border border-white px-4 py-2 font-semibold">
                                        Iniciar Sesión
                                    </button>
                                </Link>
                            </>
                        )}
                    </div>
                    <div className="block lg:hidden">
                        <button
                            id="mobile-menu-button"
                            className="flex size-8 items-center justify-center"
                            aria-label="Abrir menú de navegación"
                            onClick={handleMobileMenuToggle}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                <path
                                    className="fill-white"
                                    d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div
                    id="mobile-menu"
                    className="bg-blue text-blue-foreground flex h-screen max-h-0 flex-col gap-8 overflow-hidden transition-all lg:hidden"
                >
                    <nav>
                        {' '}
                        {/* TODO change href to real ones */}
                        <ul className="flex flex-col gap-2">
                            <li className="text-lighter-blue border-lighter-blue active:border-muted active:text-muted border-b px-2 py-4 transition-colors">
                                <Link href="#">Inicio</Link>
                            </li>
                            <li className="text-lighter-blue border-lighter-blue active:border-muted active:text-muted border-b px-2 py-4 transition-colors">
                                <Link href="#">Empleos</Link>
                            </li>
                            <li className="text-lighter-blue border-lighter-blue active:border-muted active:text-muted border-b px-2 py-4 transition-colors">
                                <Link href="#">Nosotros</Link>
                            </li>
                            <li className="text-lighter-blue border-lighter-blue active:border-muted active:text-muted border-b px-2 py-4 transition-colors">
                                <Link href="#">Contacto</Link>
                            </li>
                        </ul>
                    </nav>

                    <div className="flex flex-col gap-2">
                        {auth.user ? (
                            <Link href="#">Mi Perfil</Link>
                        ) : (
                            <>
                                <Link href={route('register')} className="w-full">
                                    <button className="text-blue w-full cursor-pointer bg-white px-4 py-2 font-semibold">Regístrate</button>
                                </Link>
                                <Link href={route('login')} className="w-full">
                                    <button className="bg-blue text-blue-foreground w-full cursor-pointer border border-white px-4 py-2 font-semibold">
                                        Iniciar Sesión
                                    </button>
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </header>

            {/* Main Content */}

            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <section
                    id="hero"
                    className="text-blue-foreground relative flex w-full flex-col items-center justify-center gap-4 px-4 py-8 lg:min-h-[75vh]"
                >
                    <div className="after:bg-blue/80 absolute inset-0 z-0 after:absolute after:inset-0">
                        <img
                            src={institution.facade}
                            className="h-full w-full object-cover"
                            alt='Fachada de la Universidad Politécnica Territorial "José Félix Ribas"'
                        />
                    </div>
                    <div className="relative z-10 flex w-full max-w-6xl flex-col items-center gap-4">
                        <div className="mb-8 text-center">
                            <h1>Impulsa tu futuro con nosotros</h1>
                            <p className="text-lighter-blue">
                                Conecta con las propuestas de trabajo ofrecidas por nuestra prestigiosa universidad. Empieza tu carrera profesional
                                con nosotros hoy.
                            </p>
                        </div>
                        <form action="" className="bg-background text-text flex w-full flex-col gap-4 rounded p-2 lg:flex-row">
                            <label htmlFor="job-search" className="border-background flex flex-col rounded border bg-white p-2 lg:w-10/12">
                                <div className="flex w-full items-center justify-between gap-4 lg:justify-center">
                                    <div className="size-4 lg:size-8">
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
                                            id="job-search"
                                            placeholder="ej. Profesor de física"
                                        />
                                    </div>
                                </div>
                            </label>
                            <button type="submit" className="bg-blue text-blue-foreground cursor-pointer px-4 py-2 font-semibold lg:w-2/12">
                                Buscar
                            </button>
                        </form>
                    </div>
                </section>

                {/* Featured Jobs Section */}
                <section id="featured-jobs" className="relative flex w-full items-center justify-center gap-4 px-4 py-8">
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 flex items-center justify-between">
                            <div>
                                <h2>Oportunidades destacadas</h2>
                                <p className="text-secondary-text">Descubre los últimos puestos disponibles en nuestra universidad</p>
                            </div>
                            <div className="mt-2 hidden text-center md:block">
                                <Link href="/jobs" className="text-blue active:text-muted font-medium hover:underline active:no-underline">
                                    Ver todos los empleos &rarr;
                                </Link>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {jobs === null ? (
                                <p className="text-muted col-span-full text-center">Ha ocurrido un error, por favor inténtelo más tarde</p>
                            ) : jobs.length > 0 ? (
                                jobs.map((job: Job) => (
                                    <Link
                                        href={`/jobs/${job.id}`}
                                        className="border-muted flex flex-col gap-2 rounded border p-4 shadow transition-shadow hover:shadow-md active:shadow-sm"
                                        data-id={job.id}
                                        key={job.id}
                                    >
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
                                ))
                            ) : (
                                <p className="text-muted col-span-full text-center">No hay oportunidades de empleo disponibles en este momento</p>
                            )}
                        </div>

                        <div className="mt-4 block text-center md:hidden">
                            <Link href="/jobs" className="text-blue font-medium active:underline">
                                Ver todos los empleos &rarr;
                            </Link>
                        </div>
                    </div>
                </section>

                {/* About Section */}
                <section id="about" className="bg-background-darker flex w-full flex-col items-center justify-center px-4 py-8">
                    <div className="flex w-full max-w-6xl flex-col">
                        <div className="mb-8 text-center">
                            <h2>Apoyamos tu crecimiento</h2>
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
                                <Link href={route('register')} className="mt-auto w-full">
                                    <button className="text-blue border-blue w-full cursor-pointer border bg-white px-4 py-2 font-semibold">
                                        Regístrate &rarr;
                                    </button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* CTA Section */}

                <section id="cta" className="flex w-full flex-col items-center justify-center gap-4 px-4 py-8 lg:min-h-screen">
                    <div className="w-full max-w-6xl">
                        <div className="mb-8 text-center">
                            <h2>¿Listo para comenzar un nuevo hito en tu carrera profesional?</h2>
                            <p className="text-secondary-text">
                                Únete a nuestro portal de empleos hoy. El registro y proceso de contratación es completamente gratuito
                            </p>
                        </div>
                        <div className="flex w-full flex-col items-center gap-2 lg:flex-row lg:justify-center">
                            <Link href={route('register')} className="w-full lg:w-auto">
                                <button className="bg-blue text-blue-foreground w-full cursor-pointer px-4 py-2 font-semibold">
                                    Crear una cuenta
                                </button>
                            </Link>
                            <Link href={route('register')} className="w-full lg:w-auto">
                                <button className="text-blue border-blue w-full cursor-pointer border bg-white px-4 py-2 font-semibold">
                                    Explorar los empleos disponibles
                                </button>
                            </Link>
                        </div>
                    </div>
                </section>
            </main>
            <footer className="bg-background-darker flex w-full items-center justify-center px-4 py-8">
                <div className="flex w-full max-w-6xl flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <p className="text-blue text-xl font-bold">{institution.name}</p>
                        <p className="text-secondary-text">{institution.acronym}</p>
                        <h6>&copy; 2026 {institution.name}. Todos los derechos reservados.</h6>
                    </div>
                    <nav>
                        {' '}
                        {/* TODO change href to real ones */}
                        <ul className="text-secondary-text flex w-full flex-col justify-end gap-4 lg:flex-row">
                            <li>
                                <Link href="#" className="hover:text-blue active:text-muted text-sm underline transition-colors">
                                    Políticas de privacidad
                                </Link>
                            </li>
                            <li>
                                <Link href="#" className="hover:text-blue active:text-muted text-sm underline transition-colors">
                                    Términos del servicio
                                </Link>
                            </li>
                            <li>
                                <Link href="#" className="hover:text-blue active:text-muted text-sm underline transition-colors">
                                    Portal universitario
                                </Link>
                            </li>
                            <li>
                                <Link href="#" className="hover:text-blue active:text-muted text-sm underline transition-colors">
                                    Contacto
                                </Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </footer>
        </>
    );
}
