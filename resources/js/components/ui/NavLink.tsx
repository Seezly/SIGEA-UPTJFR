import { Link, type InertiaLinkProps } from '@inertiajs/react';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

import { cn } from '@/lib/utils';

const navLinkVariants = cva('transition-colors', {
    variants: {
        variant: {
            default:
                'text-lighter-blue active:text-muted after:bg-lighter-blue relative px-4 py-2 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:transition-all hover:text-white hover:after:w-full active:after:w-0',
            secondary: 'text-secondary-text text-sm underline hover:text-blue active:text-muted',
        },
    },
    defaultVariants: {
        variant: 'default',
    },
});

export interface NavLinkProps extends InertiaLinkProps, VariantProps<typeof navLinkVariants> {
    className?: string;
}

const NavLink = React.forwardRef<HTMLAnchorElement, NavLinkProps>(({ className, variant, ...props }, ref) => (
    <Link ref={ref} className={cn(navLinkVariants({ variant, className }))} {...props} />
));
NavLink.displayName = 'NavLink';

export { NavLink, navLinkVariants };
