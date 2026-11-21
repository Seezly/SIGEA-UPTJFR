import { cn } from '@/lib/utils';
import { FileText, Upload, X } from 'lucide-react';
import { useState, type ReactNode } from 'react';
import { ErrorCode, useDropzone, type Accept, type FileRejection } from 'react-dropzone';

/**
 * CV formats accepted
 */
export const CV_ACCEPT: Accept = {
    'application/pdf': ['.pdf'],
    'application/msword': ['.doc'],
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': ['.docx'],
    'image/jpeg': ['.jpeg', '.jpg'],
    'image/png': ['.png'],
};

/** Max CV file size in megabytes */
export const CV_MAX_SIZE_MB = 2;

interface FilePickerProps {
    value?: File | null;
    onChange?: (file: File | null) => void;
    accept?: Accept;
    maxSizeMB?: number;
    id?: string;
    children?: ReactNode;
    hint?: ReactNode;
    ariaLabel?: string;
    onReject?: (rejections: FileRejection[]) => void;
    disabled?: boolean;
    className?: string;
    name: string;
}

const formatExtensions = (accept: Accept): string =>
    Object.values(accept)
        .map((extensions) => (typeof extensions === 'string' ? extensions : extensions.join(', ')))
        .join(', ');

const formatSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

/** Translate react-dropzone error messages to Spanish */
const describeRejections = (rejections: FileRejection[], accept?: Accept, maxSizeMB?: number): string => {
    const codes = new Set(rejections.flatMap(({ errors }) => errors.map(({ code }) => code)));
    const messages: string[] = [];

    if (codes.has(ErrorCode.FileInvalidType) && accept) {
        messages.push(`Tipo de archivo no válido. Aceptados: ${formatExtensions(accept)}.`);
    }
    if (codes.has(ErrorCode.FileTooLarge) && maxSizeMB) {
        messages.push(`El archivo supera el tamaño máximo de ${maxSizeMB} MB.`);
    }
    if (codes.has(ErrorCode.FileTooSmall)) messages.push('El archivo está vacío.');
    if (codes.has(ErrorCode.TooManyFiles)) messages.push('Solo puedes subir un archivo a la vez.');

    return messages.join(' ') || rejections[0]?.errors[0]?.message || 'No se pudo agregar el archivo.';
};

export function FilePicker({
    value = null,
    onChange,
    accept,
    maxSizeMB,
    id,
    name,
    children = 'Arrastra el archivo aquí o haz clic para seleccionarlo',
    hint,
    ariaLabel = 'Seleccionar archivo',
    onReject,
    disabled = false,
    className,
}: FilePickerProps) {
    const [rejection, setRejection] = useState<string | null>(null);

    const defaultHint = [accept ? `Formatos: ${formatExtensions(accept)}` : '', maxSizeMB ? `Hasta ${maxSizeMB} MB` : '']
        .filter(Boolean)
        .join(' · ');
    const resolvedHint = hint ?? (defaultHint || undefined);

    const { getRootProps, getInputProps, isDragActive, isDragAccept, isDragReject } = useDropzone({
        accept,
        disabled,
        multiple: false,
        maxSize: maxSizeMB === undefined ? undefined : maxSizeMB * 1024 * 1024,
        onDrop: (acceptedFiles, rejections) => {
            setRejection(rejections.length > 0 ? describeRejections(rejections, accept, maxSizeMB) : null);
            if (rejections.length > 0) onReject?.(rejections);
            if (acceptedFiles.length > 0) onChange?.(acceptedFiles[0]);
        },
    });

    const appearance = isDragReject
        ? 'border-red-500 bg-red-50'
        : isDragAccept
          ? 'border-green-500 bg-green-50'
          : isDragActive
            ? 'border-lighter-blue bg-lighter-blue/30'
            : 'border-muted bg-white';

    return (
        <div className={cn('w-full', className)}>
            <div
                {...getRootProps({
                    className: cn(
                        'cursor-pointer rounded-md border-2 border-dashed p-8 text-center transition-colors focus-visible:ring-2 focus-visible:ring-lighter-blue focus-visible:outline-hidden',
                        appearance,
                        !disabled && !isDragActive && 'hover:border-lighter-blue',
                        disabled && 'cursor-not-allowed opacity-50',
                    ),
                })}
            >
                <input {...getInputProps({ id, name, 'aria-label': ariaLabel })} />

                <Upload className={cn('mx-auto mb-3 size-8 text-secondary-text', isDragReject && 'text-red-500')} aria-hidden="true" />

                <p className="text-sm text-secondary-text">{children}</p>
                {resolvedHint ? <p className="mt-1 text-xs text-secondary-text/80">{resolvedHint}</p> : null}

                {value ? (
                    <div className="mt-4 flex items-center gap-2 rounded-md border border-muted bg-background px-4 py-2 text-left">
                        <FileText className="size-4 shrink-0 text-secondary-text" aria-hidden="true" />
                        <span className="min-w-0 flex-1 truncate text-sm text-text">{value.name}</span>
                        <span className="shrink-0 text-xs text-secondary-text">{formatSize(value.size)}</span>
                        <button
                            type="button"
                            onClick={(event) => {
                                event.stopPropagation();
                                setRejection(null);
                                onChange?.(null);
                            }}
                            aria-label={`Quitar ${value.name}`}
                            className="text-secondary-text cursor-pointer transition-colors hover:text-red-500 focus-visible:ring-2 focus-visible:ring-lighter-blue focus-visible:outline-hidden"
                        >
                            <X className="size-4" aria-hidden="true" />
                        </button>
                    </div>
                ) : null}
            </div>

            {rejection ? (
                <p role="alert" className="mt-2 text-sm text-red-600">
                    {rejection}
                </p>
            ) : null}
        </div>
    );
}
