import { Head, useForm } from '@inertiajs/react';
import { useState, type ReactNode } from 'react';
import { FormWizard, type FormWizardSchema, type WizardData } from 'react-form-wizard-component';
import 'react-form-wizard-component/styles.css';

import { Button } from '@/components/ui/Button';
import { FormField } from '@/components/ui/FormField';
import { Input } from '@/components/ui/Input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/Select';
import AuthLayout from '@/layouts/AuthLayout';

interface RegisterForm {
    first_name: string;
    second_name: string;
    middle_name: string;
    last_name: string;
    id_prefix: string;
    id_number: string;
    address: string;
    birth_date: string;
    gender: string;
}

type StepProps = {
    data: RegisterForm;
    errors: Record<string, string>;
    onChange: (field: keyof RegisterForm, value: string) => void;
};

const PersonalStep = ({ data, errors, onChange }: StepProps) => (
    <div className="grid gap-x-4 md:grid-cols-2">
        <FormField label="Primer nombre" htmlFor="first_name" required error={errors.first_name}>
            <Input
                id="first_name"
                defaultValue={data.first_name}
                onChange={(e) => onChange('first_name', e.target.value)}
                aria-invalid={!!errors.first_name}
            />
        </FormField>
        <FormField label="Segundo nombre" htmlFor="second_name" error={errors.second_name}>
            <Input
                id="second_name"
                defaultValue={data.second_name}
                onChange={(e) => onChange('second_name', e.target.value)}
                aria-invalid={!!errors.second_name}
            />
        </FormField>
        <FormField label="Primer apellido" htmlFor="middle_name" required error={errors.middle_name}>
            <Input
                id="middle_name"
                defaultValue={data.middle_name}
                onChange={(e) => onChange('middle_name', e.target.value)}
                aria-invalid={!!errors.middle_name}
            />
        </FormField>
        <FormField label="Segundo apellido" htmlFor="last_name" error={errors.last_name}>
            <Input
                id="last_name"
                defaultValue={data.last_name}
                onChange={(e) => onChange('last_name', e.target.value)}
                aria-invalid={!!errors.last_name}
            />
        </FormField>
        <FormField label="Nacionalidad" htmlFor="id_prefix" required error={errors.id_prefix}>
            <Select defaultValue={data.id_prefix} onValueChange={(value) => onChange('id_prefix', value)}>
                <SelectTrigger id="id_prefix" aria-invalid={!!errors.id_prefix}>
                    <SelectValue placeholder="Selecciona" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="V">Venezolano (V)</SelectItem>
                    <SelectItem value="E">Extranjero (E)</SelectItem>
                </SelectContent>
            </Select>
        </FormField>
        <FormField label="Cédula" htmlFor="id_number" required error={errors.id_number}>
            <Input
                id="id_number"
                inputMode="numeric"
                defaultValue={data.id_number}
                onChange={(e) => onChange('id_number', e.target.value)}
                aria-invalid={!!errors.id_number}
            />
        </FormField>
        <FormField label="Dirección" htmlFor="address" required error={errors.address} className="md:col-span-2">
            <Input id="address" defaultValue={data.address} onChange={(e) => onChange('address', e.target.value)} aria-invalid={!!errors.address} />
        </FormField>
        <FormField label="Fecha de nacimiento" htmlFor="birth_date" required error={errors.birth_date}>
            <Input
                id="birth_date"
                type="date"
                defaultValue={data.birth_date}
                onChange={(e) => onChange('birth_date', e.target.value)}
                aria-invalid={!!errors.birth_date}
            />
        </FormField>
        <FormField label="Género" htmlFor="gender" required error={errors.gender}>
            <Select defaultValue={data.gender} onValueChange={(value) => onChange('gender', value)}>
                <SelectTrigger id="gender" aria-invalid={!!errors.gender}>
                    <SelectValue placeholder="Selecciona" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="M">Masculino (M)</SelectItem>
                    <SelectItem value="F">Femenino (F)</SelectItem>
                </SelectContent>
            </Select>
        </FormField>
    </div>
);

const isAtLeast18 = (isoDate: string) => {
    const birth = new Date(`${isoDate}T00:00:00`);
    const cutoff = new Date();
    cutoff.setFullYear(cutoff.getFullYear() - 18);
    cutoff.setHours(0, 0, 0, 0);
    return birth.getTime() <= cutoff.getTime();
};

const validatePersonal = (data: RegisterForm): true | string => {
    if (!data.first_name?.trim()) return 'El nombre es obligatorio';
    if (!data.middle_name?.trim()) return 'El primer apellido es obligatorio';
    if (data.id_prefix !== 'V' && data.id_prefix !== 'E') return 'Selecciona la nacionalidad';
    if (!/^\d{1,9}$/.test(data.id_number)) return 'La cédula debe contener de 1 a 9 dígitos';
    if (data.address?.trim().length < 10) return 'La dirección debe tener al menos 10 caracteres';
    if (!data.birth_date) return 'La fecha de nacimiento es obligatoria';
    if (!isAtLeast18(data.birth_date)) return 'Debes ser mayor de 18 años para registrarte';
    if (data.gender !== 'M' && data.gender !== 'F') return 'Selecciona el género';
    return true;
};

const withMessage = (content: ReactNode, message: string | null) =>
    message ? (
        <>
            <p role="alert" className="mb-4 text-sm text-red-600">
                {message}
            </p>
            {content}
        </>
    ) : (
        content
    );

export default function Register() {
    const { data, setData, errors } = useForm<RegisterForm>({
        first_name: '',
        second_name: '',
        middle_name: '',
        last_name: '',
        id_prefix: '',
        id_number: '',
        address: '',
        birth_date: '',
        gender: '',
    });

    const [blockedMessage, setBlockedMessage] = useState<string | null>(null);

    const update = (field: keyof RegisterForm, value: string) => {
        setData((prev) => ({ ...prev, [field]: value }));
        setBlockedMessage(null);
    };

    const handleFinishClick = (complete: () => void) => {
        const result = validatePersonal(data);
        if (result !== true) {
            setBlockedMessage(result);
            return;
        }
        setBlockedMessage(null);
        complete();
    };

    const schema: FormWizardSchema = {
        steps: [
            {
                id: 'personal',
                title: 'Información Personal',
                validate: ({ data: stepData }) => validatePersonal(stepData as unknown as RegisterForm),
                content: withMessage(<PersonalStep data={data} errors={errors} onChange={update} />, blockedMessage),
            },
            {
                id: 'personal-2',
                title: 'Información Personal 2',
                validate: ({ data: stepData }) => validatePersonal(stepData as unknown as RegisterForm),
                content: withMessage(<PersonalStep data={data} errors={errors} onChange={update} />, blockedMessage),
            },
        ],
    };

    return (
        <AuthLayout title="Crea una cuenta" description="Ingresa tus datos para crear una cuenta en el sistema">
            <Head title="Registro" />
            <FormWizard
                schema={schema}
                data={data as unknown as WizardData}
                onDataChange={(nextData) => setData((prev) => ({ ...prev, ...(nextData as unknown as RegisterForm) }))}
                theme={{
                    primaryColor: '#001e40',
                    borderRadius: '0',
                    backgroundColor: '#ffffff',
                    textColor: '#1a1c1c',
                    subtitleColor: '#43474f',
                    errorColor: '#fb2c36',
                }}
                finishButtonTemplate={(complete) => (
                    <div className="wizard-footer-right">
                        <Button variant="outline" onClick={() => handleFinishClick(complete)}>
                            Finalizar registro
                        </Button>
                    </div>
                )}
                nextButtonTemplate={(complete) => (
                    <div className="wizard-footer-right">
                        <Button variant="outline" onClick={() => handleFinishClick(complete)}>
                            Siguiente
                        </Button>
                    </div>
                )}
                backButtonTemplate={(back) => (
                    <div className="wizard-footer-left">
                        <Button variant="outline-light" onClick={back}>
                            Anterior
                        </Button>
                    </div>
                )}
                classNames={{
                    root: 'p-4',
                }}
            />
        </AuthLayout>
    );
}
