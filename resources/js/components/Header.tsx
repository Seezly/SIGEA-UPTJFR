import { MainNav } from '@/components/MainNav';
import { MobileNav } from '@/components/MobileNav';
import { Button } from '@/components/ui/Button';
import { type Auth, type Institution } from '@/types';
import { Link } from '@inertiajs/react';
import { useRef, useState } from 'react';

interface HeaderProps {
    auth: Auth;
    institution: Institution;
}

export function Header({ auth, institution }: HeaderProps) {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const mobileMenuButtonRef = useRef<HTMLButtonElement>(null);
    const mobileMenuRef = useRef<HTMLDivElement>(null);

    return (
        <header className="bg-blue text-blue-foreground sticky top-0 z-1000">
            <div className="flex w-full items-center justify-between p-4">
                <div className="flex flex-1 items-center justify-start gap-2">
                    <img className="size-8" src={institution.logo} alt='Logo de la Universidad Politécnica Territorial "José Félix Ribas"' />
                </div>
                <MainNav />
                <div className="hidden flex-1 items-center justify-end gap-4 lg:flex">
                    {auth.user ? (
                        <Button asChild variant="ghost">
                            <Link href="#">Mi Perfil</Link>
                        </Button>
                    ) : (
                        <>
                            <Button asChild>
                                <Link href={route('register')}>Regístrate</Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href={route('login')}>Iniciar Sesión</Link>
                            </Button>
                        </>
                    )}
                </div>
                <div className="block lg:hidden">
                    <Button
                        variant="ghost"
                        size="icon"
                        id="mobile-menu-button"
                        className="flex size-8 items-center justify-center"
                        ref={mobileMenuButtonRef}
                        onClick={() => setMobileMenuOpen((open) => !open)}
                        aria-label={mobileMenuOpen ? 'Cerrar menú de navegación' : 'Abrir menú de navegación'}
                        aria-expanded={mobileMenuOpen}
                        aria-controls="mobile-menu"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path
                                className="fill-white"
                                d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"
                            />
                        </svg>
                    </Button>
                </div>
            </div>
            <MobileNav auth={auth} open={mobileMenuOpen} menuRef={mobileMenuRef} />
        </header>
    );
}
