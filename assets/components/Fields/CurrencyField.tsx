import React, { ReactElement } from "react";
import { FieldProps } from "../../models/form";
import settings from "../../services/settings";
import SelectField from "./SelectField";

type Props = FieldProps;

export default function CurrencyField(props: Props): ReactElement {
  const getCurrencyOptions = () => {
    return settings.currencies.map((currency) => ({
      value: currency,
      label: currency,
    }));
  };

  return (
    <>
      <SelectField
        options={getCurrencyOptions()}
        label={props.label}
        name={props.name}
        isMultiple
      />
    </>
  );
}
