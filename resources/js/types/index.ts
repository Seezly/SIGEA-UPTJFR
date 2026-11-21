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

export interface Flash {
    success?: string;
    error?: string;
    [key: string]: unknown;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    flash: Flash;
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
    name: string;
    acronym: string;
    logo: string;
    facade: string;
}

export interface AboutUsInstitution extends Institution {
    about_us: string;
    mission: string;
    vision: string;
}

export interface Legal {
    sections: LegalSection[];
    updated_at: string;
}

export interface LegalSection {
    id: string;
    title: string;
    content: string;
}

export interface WelcomeProps extends SharedData {
    jobs: Job[] | null;
    institution: Institution;
}

export interface AboutUsProps extends SharedData {
    institution: AboutUsInstitution;
}

export interface TermsProps extends SharedData {
    institution: Institution;
    terms: Legal;
}

export interface PrivacyPoliciesProps extends SharedData {
    institution: Institution;
    policies: Legal;
}

export interface ContactProps extends SharedData {
    institution: Institution;
}

export interface RegisterForm {
    first_name: string;
    second_name: string;
    middle_name: string;
    last_name: string;
    id_prefix: string;
    id_number: string;
    address: string;
    phone_number: string;
    birth_date: string;
    gender: string;
    email: string;
    password: string;
    password_confirmation: string;
    terms_of_service: boolean;
    cv: File | null;
}

export type StepProps = {
    data: RegisterForm;
    errors: Record<string, string>;
    onChange: (field: keyof RegisterForm, value: string | boolean | File | null) => void;
};