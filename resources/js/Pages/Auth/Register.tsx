import { type RegisterForm, type SharedData } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState, type ReactNode } from 'react';
import { FormWizard, type FormWizardMethods, type FormWizardSchema, type WizardData } from 'react-form-wizard-component';
import 'react-form-wizard-component/styles.css';

import { CredentialStep } from '@/components/Register/CredentialStep';
import { CVStep } from '@/components/Register/CVStep';
import { PersonalStep } from '@/components/Register/PersonalStep';
import { Button } from '@/components/ui/Button';
import { CV_ACCEPT, CV_MAX_SIZE_MB } from '@/components/ui/FilePicker';
import AuthLayout from '@/layouts/AuthLayout';
import { CircleX } from 'lucide-react';
import { toast } from 'sonner';

const isAtLeast18 = (isoDate: string) => {
    const birth = new Date(`${isoDate}T00:00:00`);
    const cutoff = new Date();
    cutoff.setFullYear(cutoff.getFullYear() - 18);
    cutoff.setHours(0, 0, 0, 0);
    return birth.getTime() <= cutoff.getTime();
};

/** El input date entrega yyyy-mm-dd y el backend exige d/m/Y: se convierte solo en el payload. */
const toDmy = (isoDate: string) => {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(isoDate)) return isoDate;
    const [year, month, day] = isoDate.split('-');
    return `${day}/${month}/${year}`;
};

const validatePersonal = (data: RegisterForm): true | string => {
    if (!data.first_name?.trim()) return 'El nombre es obligatorio';
    if (!data.middle_name?.trim()) return 'El primer apellido es obligatorio';
    if (data.id_prefix !== 'V' && data.id_prefix !== 'E') return 'Selecciona la nacionalidad';
    if (!/^\d{1,9}$/.test(data.id_number)) return 'La cédula debe contener de 1 a 9 dígitos';
    if (data.address?.trim().length < 10) return 'La dirección debe tener al menos 10 caracteres';
    if (!data.phone_number) return 'El teléfono es obligatorio';
    if (!/^\d{7,11}$/.test(data.phone_number)) return 'El teléfono debe contener de 7 a 11 dígitos';
    if (!data.birth_date) return 'La fecha de nacimiento es obligatoria';
    if (!isAtLeast18(data.birth_date)) return 'Debes ser mayor de 18 años para registrarte';
    if (data.gender !== 'M' && data.gender !== 'F') return 'Selecciona el género';
    return true;
};

const validateCredentials = (data: RegisterForm): true | string => {
    if (!data.email?.trim()) return 'El correo electrónico es obligatorio';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) return 'Ingresa un correo electrónico válido';
    if (!data.password) return 'La contraseña es obligatoria';
    if (data.password.length < 8) return 'La contraseña debe tener al menos 8 caracteres';
    if (!data.password.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/))
        return 'La contraseña debe tener al menos una letra mayúscula, una letra minúscula y un número';
    if (data.password !== data.password_confirmation) return 'Las contraseñas no coinciden';
    if (!data.terms_of_service) return 'Debes aceptar los términos y condiciones';
    return true;
};

const validateCV = (data: RegisterForm): true | string => {
    if (data?.cv?.size && data?.cv?.size > CV_MAX_SIZE_MB * 1024 * 1024) return `El archivo debe ser menor a ${CV_MAX_SIZE_MB} MB`;
    if (data?.cv?.type && !Object.keys(CV_ACCEPT).includes(data?.cv?.type)) return 'El archivo debe ser un PDF, Word, o una imagen (JPG, PNG)';
    return true;
};

const STEP_BY_FIELD: Record<string, string> = {
    email: 'credentials',
    password: 'credentials',
    password_confirmation: 'credentials',
    terms_of_service: 'credentials',
    cv: 'cv',
};

const STEP_ORDER = ['personal', 'credentials', 'cv'];

/** Paso del wizard que contiene al campo (los no listados pertenecen al primero). */
const stepForField = (field: string) => STEP_BY_FIELD[field] ?? 'personal';

/** Primer paso, en orden del wizard, que tenga algún campo con error. */
const firstStepWithError = (fields: string[]) => STEP_ORDER.find((step) => fields.some((field) => stepForField(field) === step));

const withMessage = (content: ReactNode, message: string | null) => (
    <>
        {message ? (
            <p role="alert" className="mb-4 text-sm text-red-600">
                {message}
            </p>
        ) : null}
        {content}
    </>
);

export default function Register() {
    const { data, setData, errors, post, processing, transform, clearErrors } = useForm<RegisterForm>({
        first_name: '',
        second_name: '',
        middle_name: '',
        last_name: '',
        id_prefix: '',
        id_number: '',
        address: '',
        phone_number: '',
        birth_date: '',
        gender: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms_of_service: false,
        cv: null,
    });

    const [screen, setScreen] = useState<'form' | 'error'>('form');

    const [blockedMessage, setBlockedMessage] = useState<string | null>(null);

    const wizardRef = useRef<FormWizardMethods>(null);

    const { flash } = usePage<SharedData>().props;

    // El throttle de registro redirige de vuelta al formulario con un mensaje
    // en la sesión (flash); sin esto el usuario no veía por qué falló.
    useEffect(() => {
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    const update = (field: keyof RegisterForm, value: string | boolean | File | null) => {
        setData((prev) => ({ ...prev, [field]: value }));
        setBlockedMessage(null);
    };

    const handleFinishClick = () => {
        const validatePersonalResult = validatePersonal(data);
        const validateCredentialsResult = validateCredentials(data);
        const validateCVResult = validateCV(data);
        if (validatePersonalResult !== true) {
            setBlockedMessage(validatePersonalResult);
            return;
        }
        if (validateCredentialsResult !== true) {
            setBlockedMessage(validateCredentialsResult);
            return;
        }
        if (validateCVResult !== true) {
            setBlockedMessage(validateCVResult);
            return;
        }
        setBlockedMessage(null);
        transform((d) => ({ ...d, birth_date: toDmy(d.birth_date) }));
        post(route('register'), {
            forceFormData: true,
            // Errores de validación (422/302 con errors en sesión): nos quedamos
            // en el formulario, mostramos el banner y saltamos al primer paso
            // que contiene un campo con error.
            onError: (serverErrors) => {
                const fields = Object.keys(serverErrors);
                const step = firstStepWithError(fields);
                if (!step) return;
                const firstField = fields.find((field) => stepForField(field) === step);
                if (firstField) setBlockedMessage(serverErrors[firstField]);
                wizardRef.current?.goToTabById(step);
            },
            // 419 (CSRF), 429, 500 y respuestas no-Inertia: pantalla de error.
            // Devolver false cancela el diálogo HTML crudo de Inertia.
            onHttpException: () => {
                setScreen('error');
                return false;
            },
            onNetworkError: () => {
                setScreen('error');
                return false;
            },
            // Un registro exitoso siempre redirige al dashboard (el componente
            // se desmonta), así que no hay nada que limpiar. Resetear aquí
            // borraría el formulario cuando el throttle rebota a esta página.
        });
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
                id: 'credentials',
                title: 'Credenciales de Acceso',
                validate: ({ data: stepData }) => validateCredentials(stepData as unknown as RegisterForm),
                content: withMessage(<CredentialStep data={data} errors={errors} onChange={update} />, blockedMessage),
            },
            {
                id: 'cv',
                title: 'Curriculum Vitae',
                validate: ({ data: stepData }) => validateCV(stepData as unknown as RegisterForm),
                content: withMessage(<CVStep data={data} errors={errors} onChange={update} />, blockedMessage),
            },
        ],
    };

    return (
        <AuthLayout title="Crea una cuenta" description="Ingresa tus datos para crear una cuenta en el sistema">
            <Head title="Registro" />
            {screen === 'error' ? (
                <div className="mx-auto flex w-full flex-col items-center justify-between bg-white p-8 shadow">
                    <CircleX className="size-32 text-red-500" />
                    <div className="my-8 text-center">
                        <p className="text-xl font-medium text-red-500">Ocurrió un error al registrar tu cuenta</p>
                        <span className="text-secondary-text text-sm">Por favor, inténtalo de nuevo más tarde.</span>
                    </div>
                    <Button
                        variant="outline"
                        size="full"
                        onClick={() => {
                            setScreen('form');
                            setBlockedMessage(null);
                            clearErrors();
                        }}
                    >
                        Volver a intentarlo
                    </Button>
                </div>
            ) : (
                <FormWizard
                    ref={wizardRef}
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
                    finishButtonTemplate={() => (
                        <div className="wizard-footer-right">
                            <Button variant="outline" onClick={() => handleFinishClick()} disabled={processing}>
                                {processing ? 'Enviando…' : 'Finalizar registro'}
                            </Button>
                        </div>
                    )}
                    nextButtonTemplate={(next) => (
                        <div className="wizard-footer-right">
                            <Button variant="outline" onClick={next}>
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
            )}
        </AuthLayout>
    );
}
