import { NavLink } from '@/components/ui/NavLink';

const navigationItems = [
    { label: 'Inicio', href: route('home') },
    { label: 'Empleos', href: route('home') }, // TODO - Update the href to the actual job vacancies link
    { label: 'Nosotros', href: route('about-us') },
    { label: 'Contacto', href: route('contact') },
];

export function MainNav() {
    return (
        <nav className="hidden flex-1 items-center justify-center lg:flex" aria-label="Menú de navegación principal">
            <ul className="flex flex-1 items-center justify-center gap-8">
                {navigationItems.map((item) => (
                    <li className="flex items-center justify-center" key={item.label}>
                        <NavLink href={item.href}>{item.label}</NavLink>
                    </li>
                ))}
            </ul>
        </nav>
    );
}

export { navigationItems };
