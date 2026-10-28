import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    url: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    flash: { [key: string]: unknown };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Job {
    id: number;
    title: string;
    employment_type: string;
    position: string;
    area: string;
    category: string;
    published_at: string;
    application_deadline: string | null;
}

export interface Institution {
    id: number;
    name: string;
    acronym: string;
    description: string;
    logo: string;
    facade: string;
    about_us: string;
    mission: string;
    vision: string;
}

export interface WelcomeProps extends SharedData {
    jobs: Job[] | null;
    institution: Institution;
}

export interface AboutUsProps extends SharedData {
    institution: Institution;
}
