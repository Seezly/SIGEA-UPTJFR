import InputError from '@/components/InputError';
import { Label } from '@/components/ui/Label';
import { cn } from '@/lib/utils';
import { ComponentProps, ReactNode } from 'react';

interface FormFieldProps {
    label: string;
    htmlFor: string;
    required?: boolean;
    error?: string;
    children: ReactNode;
    className?: string;
    labelProps?: ComponentProps<typeof Label>;
}

export function FormField({ label, htmlFor, required, error, children, className, labelProps }: FormFieldProps) {
    return (
        <div className={cn('mb-4', className)}>
            <Label htmlFor={htmlFor} required={required} {...labelProps}>
                {label}
            </Label>
            <div className="mt-2">{children}</div>
            <InputError message={error} />
        </div>
    );
}

export default FormField;
