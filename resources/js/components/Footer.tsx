import { NavLink } from '@/components/ui/NavLink';
import { type Institution } from '@/types';

interface FooterProps {
    institution: Institution;
}

export function Footer({ institution }: FooterProps) {
    return (
        <footer className="bg-background-darker flex w-full items-center justify-center px-4 py-8">
            <div className="flex w-full max-w-6xl flex-col justify-between gap-4 lg:flex-row lg:items-center">
                <div>
                    <p className="text-blue text-xl font-bold">{institution.name}</p>
                    <p className="text-secondary-text">{institution.acronym}</p>
                    <small>
                        &copy; {new Date().getFullYear()} {institution.name}. Todos los derechos reservados.
                    </small>
                </div>
                <nav aria-label="Menú de navegación del footer">
                    <ul className="text-secondary-text flex w-full flex-col justify-end gap-4 lg:flex-row">
                        <li>
                            <NavLink href="#" variant="secondary">
                                Políticas de privacidad
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" variant="secondary">
                                Términos del servicio
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" variant="secondary">
                                Portal universitario
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" variant="secondary">
                                Contacto
                            </NavLink>
                        </li>
                    </ul>
                </nav>
            </div>
        </footer>
    );
}
