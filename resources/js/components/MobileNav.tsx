import { Button } from '@/components/ui/Button';
import { NavLink } from '@/components/ui/NavLink';
import { type Auth } from '@/types';
import { Link } from '@inertiajs/react';
import { type RefObject } from 'react';

import { navigationItems } from './MainNav';

interface MobileNavProps {
    auth: Auth;
    open: boolean;
    menuRef: RefObject<HTMLDivElement | null>;
}

export function MobileNav({ auth, open, menuRef }: MobileNavProps) {
    return (
        <div
            id="mobile-menu"
            ref={menuRef}
            aria-hidden={!open}
            inert={!open ? true : undefined}
            role="menu"
            className={`bg-blue text-blue-foreground flex ${open ? 'h-screen px-4 py-8' : 'max-h-0'} flex-col gap-8 overflow-hidden transition-all lg:hidden`}
            aria-label="Menú de navegación móvil"
        >
            <nav>
                <ul className="flex flex-col gap-2">
                    {navigationItems.map((item) => (
                        <li
                            className="text-lighter-blue border-lighter-blue active:border-muted active:text-muted border-b px-2 py-4 transition-colors"
                            key={item.label}
                        >
                            <NavLink href={item.href} className="block px-2 py-4">
                                {item.label}
                            </NavLink>
                        </li>
                    ))}
                </ul>
            </nav>

            <div className="flex flex-col gap-2">
                {auth.user ? (
                    <Button asChild variant="ghost" className="w-full justify-start">
                        <Link href="#">Mi Perfil</Link>
                    </Button>
                ) : (
                    <>
                        <Button asChild className="w-full">
                            <Link href={route('register')}>Regístrate</Link>
                        </Button>
                        <Button asChild variant="outline" className="w-full">
                            <Link href={route('login')}>Iniciar Sesión</Link>
                        </Button>
                    </>
                )}
            </div>
        </div>
    );
}
