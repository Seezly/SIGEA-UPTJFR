import { DatePicker, toIsoDate } from '@/components/ui/DatePicker';
import { FormField } from '@/components/ui/FormField';
import { Input } from '@/components/ui/Input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/Select';
import { type StepProps } from '@/types';

export const PersonalStep = ({ data, errors, onChange }: StepProps) => (
    <div className="grid gap-x-4 md:grid-cols-2">
        <FormField label="Primer nombre" htmlFor="first_name" required error={errors.first_name}>
            <Input
                id="first_name"
                name="first_name"
                defaultValue={data.first_name}
                onChange={(e) => onChange('first_name', e.target.value)}
                aria-invalid={!!errors.first_name}
            />
        </FormField>
        <FormField label="Segundo nombre" htmlFor="second_name" error={errors.second_name}>
            <Input
                id="second_name"
                name="second_name"
                defaultValue={data.second_name}
                onChange={(e) => onChange('second_name', e.target.value)}
                aria-invalid={!!errors.second_name}
            />
        </FormField>
        <FormField label="Primer apellido" htmlFor="middle_name" required error={errors.middle_name}>
            <Input
                id="middle_name"
                name="middle_name"
                defaultValue={data.middle_name}
                onChange={(e) => onChange('middle_name', e.target.value)}
                aria-invalid={!!errors.middle_name}
            />
        </FormField>
        <FormField label="Segundo apellido" htmlFor="last_name" error={errors.last_name}>
            <Input
                id="last_name"
                name="last_name"
                defaultValue={data.last_name}
                onChange={(e) => onChange('last_name', e.target.value)}
                aria-invalid={!!errors.last_name}
            />
        </FormField>
        <FormField label="Nacionalidad" htmlFor="id_prefix" required error={errors.id_prefix}>
            <Select defaultValue={data.id_prefix} onValueChange={(value) => onChange('id_prefix', value)}>
                <SelectTrigger id="id_prefix" name="id_prefix" aria-invalid={!!errors.id_prefix}>
                    <SelectValue placeholder="Selecciona" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="V">Venezolano (V)</SelectItem>
                    <SelectItem value="E">Extranjero (E)</SelectItem>
                </SelectContent>
            </Select>
        </FormField>
        <FormField label="Cédula" htmlFor="id_number" required error={errors.id_number}>
            <Input
                id="id_number"
                inputMode="numeric"
                defaultValue={data.id_number}
                onChange={(e) => onChange('id_number', e.target.value)}
                aria-invalid={!!errors.id_number}
                name="id_number"
            />
        </FormField>
        <FormField label="Dirección" htmlFor="address" required error={errors.address} className="md:col-span-2">
            <Input
                id="address"
                name="address"
                defaultValue={data.address}
                onChange={(e) => onChange('address', e.target.value)}
                aria-invalid={!!errors.address}
            />
        </FormField>
        <FormField className="md:col-span-2" label="Teléfono" htmlFor="phone_number" required error={errors.phone_number}>
            <Input
                id="phone_number"
                name="phone_number"
                inputMode="numeric"
                maxLength={11}
                defaultValue={data.phone_number}
                onChange={(e) => onChange('phone_number', e.target.value.replace(/\D/g, ''))}
                aria-invalid={!!errors.phone_number}
            />
        </FormField>
        <FormField label="Fecha de nacimiento" htmlFor="birth_date" required error={errors.birth_date}>
            <DatePicker
                id="birth_date"
                name="birth_date"
                value={data.birth_date}
                onChange={(value) => onChange('birth_date', value)}
                max={toIsoDate(new Date())}
                aria-invalid={!!errors.birth_date}
            />
        </FormField>
        <FormField label="Género" htmlFor="gender" required error={errors.gender}>
            <Select defaultValue={data.gender} onValueChange={(value) => onChange('gender', value)}>
                <SelectTrigger id="gender" name="gender" aria-invalid={!!errors.gender}>
                    <SelectValue placeholder="Selecciona" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="M">Masculino (M)</SelectItem>
                    <SelectItem value="F">Femenino (F)</SelectItem>
                </SelectContent>
            </Select>
        </FormField>
    </div>
);
