export interface FieldProps {
  label: string;
  name: string;
}

export interface ChoiceOption {
  value: string | number;
  label: string;
}

export interface DateFieldValue {
  min: Date;
  max: Date;
}
