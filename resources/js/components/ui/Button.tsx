import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 whitespace-nowrap ring-offset-background transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
    {
        variants: {
            variant: {
                default: 'bg-white text-blue font-semibold hover:bg-lighter-blue active:bg-lighter-blue/90',
                destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90 active:bg-destructive/90',
                outline: 'bg-blue text-blue-foreground border border-white font-semibold hover:bg-white hover:text-blue active:bg-lighter-blue active:border-lighter-blue',
                'outline-light': 'bg-transparent text-blue border border-blue font-semibold hover:bg-blue hover:border-blue hover:text-blue-foreground active:bg-blue/90 active:border-blue/90',
                secondary: 'bg-lighter-blue text-lighter-blue-foreground hover:bg-lighter-blue/90 active:bg-lighter-blue/90',
                ghost: 'text-lighter-blue active:text-muted hover:text-white',
                link: 'text-blue font-medium hover:underline hover:text-blue/90 active:no-underline active:text-blue/80',
            },
            size: {
                default: 'px-4 py-2',
                sm: 'rounded-md px-3',
                lg: 'rounded-md px-8',
                icon: 'w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement>, VariantProps<typeof buttonVariants> {
    asChild?: boolean;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : 'button';
    return <Comp className={cn(buttonVariants({ variant, size, className }))} ref={ref} {...props} />;
});
Button.displayName = 'Button';

export { Button, buttonVariants };
