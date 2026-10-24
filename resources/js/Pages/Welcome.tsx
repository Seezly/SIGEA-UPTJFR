import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth, jobs } = usePage<SharedData>().props;

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

    return (
        <>
            <Head title="Inicio">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>

            {/* Header */}

            <header className="bg-blue text-blue-foreground sticky top-0 z-1000">
                <div className="flex w-full items-center justify-between p-4">
                    <div className="flex shrink-0 items-center gap-2">
                        <img
                            src="/logo_upt_vector.svg"
                            width="40"
                            height="40"
                            alt='Logo de la Universidad Politécnica Territorial "José Félix Ribas"'
                        />
                    </div>
                    <nav className="hidden md:block">
                        <ul>
                            <li>
                                <Link href="#">Inicio</Link>
                            </li>
                            <li>
                                <Link href="#">Empleos</Link>
                            </li>
                            <li>
                                <Link href="#">Nosotros</Link>
                            </li>
                            <li>
                                <Link href="#">Contacto</Link>
                            </li>
                        </ul>
                    </nav>
                    <div className="hidden md:block">
                        {auth.user ? (
                            <Link href="#">Mi Perfil</Link>
                        ) : (
                            <>
                                <Link href={route('register')} className="w-full">
                                    <button className="text-blue w-full bg-white px-4 py-2 font-semibold">Regístrate</button>
                                </Link>
                                <Link href={route('login')} className="w-full">
                                    <button className="bg-blue text-blue-foreground w-full border border-white px-4 py-2 font-semibold">
                                        Iniciar Sesión
                                    </button>
                                </Link>
                            </>
                        )}
                    </div>
                    <div className="block md:hidden">
                        <button id="mobile-menu-button" className="size-8" aria-label="Abrir menú de navegación" onClick={handleMobileMenuToggle}>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                <path
                                    fill="#ffffff"
                                    d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div
                    id="mobile-menu"
                    className="bg-blue text-blue-foreground flex h-screen max-h-0 flex-col gap-8 overflow-hidden transition-all md:hidden"
                >
                    <nav>
                        <ul className="flex flex-col gap-2">
                            <li className="border-lighter-blue border-b px-2 py-4">
                                <Link href="#">Inicio</Link>
                            </li>
                            <li className="border-lighter-blue border-b px-2 py-4">
                                <Link href="#">Empleos</Link>
                            </li>
                            <li className="border-lighter-blue border-b px-2 py-4">
                                <Link href="#">Nosotros</Link>
                            </li>
                            <li className="border-lighter-blue border-b px-2 py-4">
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
                                    <button className="text-blue w-full bg-white px-4 py-2 font-semibold">Regístrate</button>
                                </Link>
                                <Link href={route('login')} className="w-full">
                                    <button className="bg-blue text-blue-foreground w-full border border-white px-4 py-2 font-semibold">
                                        Iniciar Sesión
                                    </button>
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </header>

            {/* Main Content */}

            <main className="flex flex-col">
                {/* Hero Section */}
                <section id="hero" className="text-blue-foreground relative flex flex-col items-center gap-4 px-4 py-8">
                    <div className="after:bg-blue/80 absolute inset-0 z-0 after:absolute after:inset-0">
                        <img
                            src="/fachada_upt.jpg"
                            className="h-full w-full object-cover"
                            alt='Fachada de la Universidad Politécnica Territorial "José Félix Ribas"'
                        />
                    </div>
                    <div className="relative z-10 flex flex-col items-center gap-4">
                        <div className="text-center">
                            <h1>Impulsa tu futuro con nosotros</h1>
                            <p className="text-lighter-blue">
                                Conecta con las propuestas de trabajo ofrecidas por nuestra prestigiosa universidad. Empieza tu carrera profesional
                                con nosotros hoy.
                            </p>
                        </div>
                        <form action="" className="bg-background text-text flex flex-col gap-2 rounded p-2">
                            <div className="flex items-center justify-between gap-2">
                                <label htmlFor="job-search" className="border-background flex flex-col rounded border bg-white p-2">
                                    Busca una vacante por título o palabras clave
                                    <input type="search" name="job-search" id="job-search" placeholder="ej. Profesor de física" />
                                </label>
                            </div>
                            <button type="submit" className="bg-blue text-blue-foreground px-4 py-2 font-semibold">
                                Buscar
                            </button>
                        </form>
                    </div>
                </section>

                {/* Featured Jobs Section */}
                <section id="featured-jobs" className="relative flex flex-col gap-4 px-4 py-8">
                    <div>
                        <div>
                            <h2>Oportunidades destacadas</h2>
                            <p className="text-secondary-text">Descubre los últimos puestos disponibles en nuestra universidad</p>
                        </div>
                        <div className="mt-2 hidden text-center md:block">
                            <Link href="/jobs" className="text-blue font-medium hover:underline">
                                Ver todos los empleos &rarr;
                            </Link>
                        </div>
                    </div>

                    <div>
                        {jobs.length > 0 ? (
                            jobs.map((job) => (
                                <div key={job.id}>
                                    <div>
                                        <div>Icon</div>
                                        <div>
                                            <span>{job.employment_type}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p>{job.title}</p>
                                    </div>
                                    <div>
                                        <div>{job.published_at}</div>
                                        <div>{job.application_deadline}</div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-muted text-center">No hay oportunidades de empleo disponibles en este momento</p>
                        )}
                    </div>

                    <div className="mt-2 block text-center md:hidden">
                        <Link href="/jobs" className="text-blue font-medium active:underline">
                            Ver todos los empleos &rarr;
                        </Link>
                    </div>
                </section>

                {/* About Section */}
                <section id="about" className="bg-background-darker flex flex-col gap-4 px-4 py-8">
                    <div className="text-center">
                        <h2>Apoyamos tu crecimiento</h2>
                        <p className="text-secondary-text">Cerramos la brecha entre la excelencia académica y el saber profesional</p>
                    </div>
                    <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div className="bg-blue text-blue-foreground flex flex-col gap-2 rounded p-8">
                            <div>Icon</div>
                            <h3>Para profesionales</h3>
                            <p className="text-lighter-blue">
                                Acceso exclusivo a oportunidades de empleo dentro de la universidad. De profesionales para formar profesionales.
                            </p>
                        </div>
                        <div className="border-muted flex flex-col gap-2 rounded border bg-white p-8">
                            <div>Icon</div>
                            <h3>Comunidad verificada</h3>
                            <p className="text-secondary-text">
                                Todas las propuestas exhibidas son previamente aprobadas por las comisiones y organismos necesarios dentro de la
                                universidad.
                            </p>
                        </div>
                        <div className="border-muted flex flex-col gap-2 rounded border bg-white p-8">
                            <div>Icon</div>
                            <h3>Soporte de Carrera Profesional</h3>
                            <p className="text-secondary-text">
                                Acceso a recursos, estadísticas y preparaciones para entrevistas por nuestro centro de empleos.
                            </p>
                        </div>
                        <div className="border-muted flex flex-col items-center gap-2 rounded border bg-white p-8">
                            <h3>¿Buscas un nuevo trabajo?</h3>
                            <p className="text-secondary-text text-center">
                                Considera la Universidad Politécnica Territorial José Félix Ribas como tu punto de partida hacia un trabajo de punta.
                                Visita las propuestas, verifica el feedback de tus postulaciones, historial y muéstranos tu talento.
                            </p>
                            <Link href={route('register')} className="w-full">
                                <button className="text-blue border-blue w-full border bg-white px-4 py-2 font-semibold">Regístrate &rarr;</button>
                            </Link>
                        </div>
                    </div>
                </section>

                {/* CTA Section */}

                <section id="cta" className="flex flex-col items-center gap-4 px-4 py-8">
                    <div className="text-center">
                        <h2>¿Listo para comenzar un nuevo hito en tu carrera profesional?</h2>
                        <p className="text-secondary-text">
                            Únete a nuestro portal de empleos hoy. El registro y proceso de contratación es completamente gratuito
                        </p>
                    </div>
                    <div className="flex w-full flex-col items-center gap-2">
                        <Link href={route('register')} className="w-full">
                            <button className="bg-blue text-blue-foreground w-full px-4 py-2 font-semibold">Crear una cuenta</button>
                        </Link>
                        <Link href={route('register')} className="w-full">
                            <button className="text-blue border-blue w-full border bg-white px-4 py-2 font-semibold">
                                Explorar los empleos disponibles
                            </button>
                        </Link>
                    </div>
                </section>
            </main>
            <footer className="bg-background-darker flex flex-col items-center justify-between gap-4 px-4 py-8">
                <div>
                    <p className="text-blue text-xl font-bold">UPT José Félix Ribas</p>
                    <h6>&copy; 2026 UPT José Félix Ribas. Todos los derechos reservados.</h6>
                </div>
                <nav className="flex w-full">
                    <ul className="text-secondary-text flex w-full flex-col justify-center gap-2">
                        <li>
                            <Link href="#" className="underline">
                                Políticas de privacidad
                            </Link>
                        </li>
                        <li>
                            <Link href="#" className="underline">
                                Términos del servicio
                            </Link>
                        </li>
                        <li>
                            <Link href="#" className="underline">
                                Portal universitario
                            </Link>
                        </li>
                        <li>
                            <Link href="#" className="underline">
                                Contacto
                            </Link>
                        </li>
                    </ul>
                </nav>
            </footer>
        </>
    );
}
