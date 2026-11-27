import { DayPicker, type DayButtonProps } from '@daypicker/react';
import { es } from '@daypicker/react/locale/es';
import { CalendarIcon } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

import { Input } from '@/components/ui/Input';
import { cn } from '@/lib/utils';

const pad = (value: number) => String(value).padStart(2, '0');

/** Format a Date as an ISO date string (yyyy-mm-dd) using local time. */
export const toIsoDate = (date: Date): string => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

const parseIsoDate = (iso: string): Date | null => {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso);

    if (!match) {
        return null;
    }

    const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));

    return Number.isNaN(date.getTime()) ? null : date;
};

const formatDisplayDate = (iso: string): string => {
    const date = parseIsoDate(iso);

    return date ? `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()}` : '';
};

const NAV_BUTTON_CLASS =
    'flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-secondary-text transition-colors hover:bg-background-darker hover:text-blue aria-disabled:cursor-not-allowed aria-disabled:opacity-40';

const DAY_BUTTON_CLASS =
    'flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-sm transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-blue';

const DAY_PICKER_CLASS_NAMES = {
    root: 'relative',
    month: 'relative',
    month_caption: 'flex h-11 items-center',
    dropdowns: 'relative inline-flex items-center gap-2',
    dropdown_root: 'relative inline-flex items-center rounded-md focus-within:ring-2 focus-within:ring-blue',
    dropdown: 'absolute inset-y-0 left-0 z-[2] m-0 h-full w-full cursor-pointer appearance-none border-none p-0 opacity-0',
    caption_label: 'relative z-[1] inline-flex items-center gap-1 whitespace-nowrap text-sm font-medium text-text',
    chevron: 'size-4 shrink-0 fill-current',
    nav: 'absolute top-0 right-0 flex h-11 items-center gap-1',
    button_previous: NAV_BUTTON_CLASS,
    button_next: NAV_BUTTON_CLASS,
    month_grid: 'border-separate border-spacing-1',
    weekday: 'w-9 px-0 py-2 text-center text-xs font-medium text-secondary-text',
    day: 'h-9 w-9 p-0 text-center',
    day_button: DAY_BUTTON_CLASS,
};

/**
 * The library applies modifier classes (selected, today, disabled) to the grid
 * cell, not to the button, so the button reads them from its props instead.
 */
function DayButton({ day, modifiers, className, ...props }: DayButtonProps) {
    const ref = useRef<HTMLButtonElement>(null);

    useEffect(() => {
        if (modifiers.focused) {
            ref.current?.focus();
        }
    }, [modifiers.focused]);

    return (
        <button
            ref={ref}
            {...props}
            data-day={day.isoDate}
            className={cn(
                className,
                modifiers.disabled
                    ? 'cursor-not-allowed text-muted opacity-50'
                    : modifiers.selected
                      ? 'bg-blue font-medium text-blue-foreground hover:bg-blue'
                      : modifiers.today
                        ? 'border border-blue text-blue hover:bg-lighter-blue'
                        : 'text-text hover:bg-lighter-blue',
            )}
        />
    );
}

interface DatePickerProps {
    id?: string;
    name?: string;
    /** Selected date as an ISO string (yyyy-mm-dd) or an empty string. */
    value: string;
    onChange: (value: string) => void;
    /** Earliest selectable date as an ISO string (yyyy-mm-dd). */
    min?: string;
    /** Latest selectable date as an ISO string (yyyy-mm-dd). */
    max?: string;
    disabled?: boolean;
    'aria-invalid'?: boolean;
    className?: string;
}

/**
 * Date picker that always displays dd/mm/yyyy and keeps an ISO (yyyy-mm-dd)
 * value, so the backend and the age validation keep working unchanged.
 */
export function DatePicker({ id, name, value, onChange, min, max, disabled, 'aria-invalid': ariaInvalid, className }: DatePickerProps) {
    const [open, setOpen] = useState(false);
    const inputRef = useRef<HTMLInputElement>(null);

    const minDate = min ? parseIsoDate(min) : null;
    const maxDate = max ? parseIsoDate(max) : null;
    const selectedDate = parseIsoDate(value);

    useEffect(() => {
        if (!open) {
            return;
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setOpen(false);
                inputRef.current?.focus();
            }
        };

        document.addEventListener('keydown', handleKeyDown);

        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [open]);

    const closeCalendar = () => setOpen(false);

    const handleToggle = () => {
        if (open) {
            closeCalendar();
        } else {
            setOpen(true);
        }
    };

    const handleDaySelect = (day: Date | undefined) => {
        if (day) {
            onChange(toIsoDate(day));
        }

        closeCalendar();
        inputRef.current?.focus();
    };

    const disabledMatchers = [...(minDate ? [{ before: minDate }] : []), ...(maxDate ? [{ after: maxDate }] : [])];

    return (
        <div className={cn('relative', className)}>
            <Input
                ref={inputRef}
                id={id}
                name={name}
                readOnly
                value={formatDisplayDate(value)}
                placeholder="dd/mm/yyyy"
                disabled={disabled}
                aria-invalid={ariaInvalid}
                aria-haspopup="dialog"
                aria-expanded={open}
                onClick={() => setOpen(true)}
                className="pr-10"
            />
            <button
                type="button"
                disabled={disabled}
                onClick={handleToggle}
                aria-label="Seleccionar fecha"
                aria-haspopup="dialog"
                aria-expanded={open}
                className="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-3 text-secondary-text transition-colors hover:text-blue disabled:cursor-not-allowed disabled:opacity-50"
            >
                <CalendarIcon className="size-4" aria-hidden="true" />
            </button>

            {open && (
                <>
                    <div className="fixed inset-0 z-10" onClick={closeCalendar} aria-hidden="true" />
                    <div
                        role="dialog"
                        aria-label="Seleccionar fecha"
                        className="absolute left-0 top-full z-20 mt-1 right-0 rounded-md border border-muted bg-white p-3 shadow-lg"
                    >
                        <DayPicker
                            mode="single"
                            selected={selectedDate ?? undefined}
                            onSelect={handleDaySelect}
                            defaultMonth={selectedDate ?? new Date()}
                            captionLayout="dropdown"
                            navLayout="after"
                            reverseYears
                            startMonth={minDate ?? new Date(1900, 0, 1)}
                            endMonth={maxDate ?? undefined}
                            disabled={disabledMatchers}
                            weekStartsOn={1}
                            locale={es}
                            components={{ DayButton }}
                            classNames={DAY_PICKER_CLASS_NAMES}
                        />
                    </div>
                </>
            )}
        </div>
    );
}

export default DatePicker;
