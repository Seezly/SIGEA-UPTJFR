import { CV_ACCEPT, CV_MAX_SIZE_MB, FilePicker } from '@/components/ui/FilePicker';
import { FormField } from '@/components/ui/FormField';
import { type StepProps } from '@/types';

export const CVStep = ({ data, errors, onChange }: StepProps) => (
    <div className="flex flex-col items-center justify-center gap-4">
        <FormField label="Curriculum Vitae" htmlFor="cv" required error={errors.cv}>
            <FilePicker id="cv" value={data.cv} onChange={(file) => onChange('cv', file)} accept={CV_ACCEPT} maxSizeMB={CV_MAX_SIZE_MB}>
                Arrastra tu Curriculum Vitae aquí o haz clic para seleccionarlo
            </FilePicker>
        </FormField>
    </div>
);
