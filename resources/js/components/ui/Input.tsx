import * as React from 'react';

import { cn } from '@/lib/utils';

export const inputStyles =
    'flex w-full rounded-md border border-muted bg-white px-4 py-2 text-base text-text placeholder:text-secondary-text transition-colors outline-hidden focus:border-lighter-blue focus:ring-2 focus:ring-lighter-blue aria-invalid:border-red-500 aria-invalid:focus:ring-red-500 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';

const Input = React.forwardRef<HTMLInputElement, React.ComponentProps<'input'>>(({ className, type, ...props }, ref) => {
    return <input type={type} className={cn(inputStyles, className)} ref={ref} {...props} />;
});

Input.displayName = 'Input';

export { Input };
