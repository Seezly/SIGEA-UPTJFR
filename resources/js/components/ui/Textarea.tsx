import * as React from 'react';

import { cn } from '@/lib/utils';

import { inputStyles } from '@/components/ui/Input';

const Textarea = React.forwardRef<HTMLTextAreaElement, React.ComponentProps<'textarea'>>(({ className, ...props }, ref) => {
    return <textarea className={cn(inputStyles, 'min-h-28 resize-y', className)} ref={ref} {...props} />;
});

Textarea.displayName = 'Textarea';

export { Textarea };
