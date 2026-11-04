import { Footer } from '@/components/Footer';
import { Header } from '@/components/Header';
import { Hero } from '@/components/Hero';
import { Button } from '@/components/ui/Button';
import { type ContactProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { GraduationCap, LifeBuoy, School } from 'lucide-react';
import { useEffect } from 'react';
import { toast } from 'sonner';

export default function Contact() {
    const { auth, institution, flash } = usePage<ContactProps>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash?.success);
        }
        if (flash?.error) {
            toast.error(flash?.error);
        }
    }, [flash]);

    const handleSubmit = (e: React.SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('contact.send'), {
            onSuccess: () => {
                reset();
            },
        });
    };

    const breadcrumbs = [
        { title: 'Inicio', href: route('home') },
        { title: 'Contacto', href: route('contact') },
    ];
    return (
        <>
            <Head title="Contacto">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <Header auth={auth} institution={institution} />

            {/* Main Content */}

            <main className="flex flex-col items-center justify-center">
                {/* Hero Section */}
                <Hero
                    title="Contacto"
                    description={`Comparte tus dudas sobre la ${institution.name}. Cuéntanos el motivo de tu consulta para orientarla mejor.`}
                    image={institution.facade}
                    breadcrumbs={breadcrumbs}
                />

                {/* Contact Section */}
                <section id="contact" aria-labelledby="contact-heading" className="flex w-full flex-col items-center justify-center gap-4 px-4 py-8">
                    <div className="flex w-full max-w-6xl flex-col justify-between gap-8 lg:flex-row">
                        <div className="rounded bg-white p-8 shadow lg:w-2/3">
                            <div className="mb-4">
                                <h2 id="contact-heading">Escríbenos</h2>
                                <p className="text-secondary-text">Los campos marcados con * son obligatorios</p>
                            </div>
                            <form onSubmit={handleSubmit}>
                                <div className="mb-4">
                                    <label htmlFor="name" className="block font-medium">
                                        Nombre <span className="text-blue">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        autoComplete="name"
                                        placeholder="Pedro Perez"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className={`border-muted focus:border-lighter-blue focus:ring-lighter-blue mt-2 block w-full rounded-md border px-4 py-2${errors.name ? 'border-red-500' : ''}`}
                                        required
                                    />
                                    {errors.name && <span className="text-sm text-red-500">{errors.name}</span>}
                                </div>
                                <div className="mb-4">
                                    <label htmlFor="email" className="block font-medium">
                                        Correo electrónico <span className="text-blue">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        autoComplete="email"
                                        placeholder="pedro@ejemplo.com"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className={`border-muted focus:border-lighter-blue focus:ring-lighter-blue mt-2 block w-full rounded-md border px-4 py-2${errors.email ? 'border-red-500' : ''}`}
                                        required
                                    />
                                    {errors.email && <span className="text-sm text-red-500">{errors.email}</span>}
                                </div>
                                <div className="mb-4">
                                    <label htmlFor="subject" className="block font-medium">
                                        Asunto <span className="text-blue">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="subject"
                                        name="subject"
                                        placeholder="Consulta sobre el proceso de admisión"
                                        value={data.subject}
                                        onChange={(e) => setData('subject', e.target.value)}
                                        className={`border-muted focus:border-lighter-blue focus:ring-lighter-blue mt-2 block w-full rounded-md border px-4 py-2${errors.subject ? 'border-red-500' : ''}`}
                                        required
                                    />
                                    {errors.subject && <span className="text-sm text-red-500">{errors.subject}</span>}
                                </div>
                                <div className="mb-4">
                                    <label htmlFor="message" className="block font-medium">
                                        Mensaje <span className="text-blue">*</span>
                                    </label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows={4}
                                        placeholder="Describe tu consulta con el detalle necesario para comprenderla"
                                        value={data.message}
                                        onChange={(e) => setData('message', e.target.value)}
                                        className={`border-muted focus:border-lighter-blue focus:ring-lighter-blue mt-2 block w-full rounded-md border px-4 py-2${errors.message ? 'border-red-500' : ''}`}
                                        required
                                    ></textarea>
                                    {errors.message && <span className="text-sm text-red-500">{errors.message}</span>}
                                </div>
                                <div className="mb-8 text-sm">
                                    <p className="text-secondary-text mb-4">
                                        Comparte solo la información necesaria. No incluyas contraseñas ni datos sensibles en tu mensaje.
                                    </p>
                                    <p className="text-secondary-text">
                                        Para conocer el tratamiento propuesto de los datos de esta consulta, revisa las{' '}
                                        <Button variant="link" asChild>
                                            <Link href={route('privacy-policies')}>Políticas de privacidad</Link>
                                        </Button>
                                    </p>
                                </div>
                                <div className="flex justify-start">
                                    <Button variant="outline" type="submit" disabled={processing}>
                                        {processing ? 'Enviando...' : 'Enviar mensaje'}
                                    </Button>
                                </div>
                            </form>
                        </div>
                        <div className="flex flex-col lg:w-1/3">
                            <div className="mb-4">
                                <h3>¿En qué podemos orientarte?</h3>
                                <p className="text-secondary-text">
                                    Selecciona un asunto claro y utiliza estas indicaciones para preparar tu mensaje.
                                </p>
                            </div>
                            <div className="flex flex-col gap-4">
                                <div className="flex gap-4">
                                    <div className="bg-background-darker flex size-10 items-center justify-center p-2">
                                        <GraduationCap color="var(--color-text)" />
                                    </div>
                                    <div>
                                        <h4>Para profesionales</h4>
                                        <p className="text-secondary-text">
                                            Si tu consulta es sobre tu perfil o una postulación, indica la oferta relacionada y explica en qué paso
                                            necesitas orientación.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex gap-4">
                                    <div className="bg-background-darker flex size-10 items-center justify-center p-2">
                                        <School color="var(--color-text)" />
                                    </div>
                                    <div>
                                        <h4>Para empleadores</h4>
                                        <p className="text-secondary-text">
                                            Para consultas sobre vacantes o candidaturas, identifica la publicación y describe la información que
                                            necesitas aclarar.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex gap-4">
                                    <div className="bg-background-darker flex size-10 items-center justify-center p-2">
                                        <LifeBuoy color="var(--color-text)" />
                                    </div>
                                    <div>
                                        <h4>Soporte del portal</h4>
                                        <p className="text-secondary-text">
                                            Si encuentras una dificultad de acceso o uso, describe qué intentabas hacer y el mensaje que aparece. No
                                            compartas tu contraseña.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div className="bg-background-darker mt-4 p-4">
                                <h5 className="text-blue">Sobre tus datos personales</h5>
                                <p className="text-secondary-text text-sm">
                                    Para solicitar acceso, corrección o eliminación, indica "Datos personales" en el asunto. Evita adjuntar
                                    información de otras personas.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
            <Footer institution={institution} />
        </>
    );
}
