import React from "react";
import { ChoiceOption, FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

interface Props extends FieldProps {
  isMultiple: boolean;
  closeOnSelect?: boolean;
  options: Array<ChoiceOption>;
}

export default function SelectField(props: Props): JSX.Element {
  return (
    <FormGroup>
      <FormLabel>
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field
        name={props.name}
        options={props.options}
        component={CustomSelect}
        placeholder="Select"
        isMulti={props.isMultiple}
      />
    </FormGroup>
  );
}
