import React from "react";
import { FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

export default function SelectCreateTagField(props: FieldProps): JSX.Element {
  return (
    <FormGroup>
      <FormLabel>
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field name={props.name} component={CustomSelect} isCreatable />
      <p className="text-500 fs--2">
        <FormattedMessage id="common.actions.selectCreateTagField" />
      </p>
    </FormGroup>
  );
}
