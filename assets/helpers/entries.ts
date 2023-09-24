import { DateFieldValue } from "../models/form";

export type FormValueType =
  | Array<string>
  | string
  | number
  | boolean
  | Date
  | null
  | DateFieldValue;

export type FilterType =
  | "transaction"
  | "merchantTransaction"
  | "merchant"
  | "remittance"
  | "federatedIdentities";

export const toEntries = <
  Obj extends Record<string, FormValueType> = Record<string, FormValueType>
>(
  obj: Obj
): [string, unknown][] => Object.entries(obj);

export const clearEmptyProps = (values: Record<string, any>) =>
  Object.fromEntries(
    Object.entries(values).filter(([_, v]) => {
      if (Array.isArray(v)) {
        return !!v.length;
      }

      if (!!v) return v;
    })
  );
