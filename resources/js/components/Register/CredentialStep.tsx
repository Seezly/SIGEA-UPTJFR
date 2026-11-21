import { Button } from '@/components/ui/Button';
import { Checkbox } from '@/components/ui/Checkbox';
import { FormField } from '@/components/ui/FormField';
import { Input } from '@/components/ui/Input';
import { type StepProps } from '@/types';
import { Link } from '@inertiajs/react';

export const CredentialStep = ({ data, errors, onChange }: StepProps) => (
    <div className="grid gap-x-4 md:grid-cols-2">
        <FormField className="md:col-span-2" label="Correo electrónico" htmlFor="email" required error={errors.email}>
            <Input
                id="email"
                name="email"
                defaultValue={data.email}
                onChange={(e) => onChange('email', e.target.value.toLowerCase())}
                aria-invalid={!!errors.email}
            />
        </FormField>
        <FormField label="Contraseña" htmlFor="password" required error={errors.password}>
            <Input
                id="password"
                name="password"
                type="password"
                defaultValue={data.password}
                onChange={(e) => onChange('password', e.target.value)}
                aria-invalid={!!errors.password}
            />
        </FormField>
        <FormField label="Confirmar contraseña" htmlFor="password_confirmation" required error={errors.password_confirmation}>
            <Input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                defaultValue={data.password_confirmation}
                onChange={(e) => onChange('password_confirmation', e.target.value)}
                aria-invalid={!!errors.password_confirmation}
            />
        </FormField>
        <FormField className="md:col-span-2" label="Términos del servicio" htmlFor="terms_of_service" required error={errors.terms_of_service}>
            <div className="flex items-center justify-start gap-2">
                <Checkbox
                    id="terms_of_service"
                    name="terms_of_service"
                    checked={data.terms_of_service}
                    onCheckedChange={(checked) => onChange('terms_of_service', checked === true)}
                    aria-invalid={!!errors.terms_of_service}
                />
                Acepto los términos del servicio y la política de privacidad.{' '}
                <Button asChild variant="link">
                    <Link href={route('terms')} target="_blank">
                        Leer términos
                    </Link>
                </Button>
            </div>
        </FormField>
    </div>
);
