import React from "react";
import CustomSelect from "../Common/CustomSelect";
import { ChoiceOption, FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage, useIntl } from "react-intl";

const BooleanField = (props: FieldProps): JSX.Element => {
  const intl = useIntl();
  const options: Array<ChoiceOption> = [
    { value: "true", label: intl.formatMessage({ id: "common.answer.yes" }) },
    { value: "false", label: intl.formatMessage({ id: "common.answer.no" }) },
  ];

  return (
    <FormGroup>
      <FormLabel>
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field
        name={props.name}
        options={options}
        component={CustomSelect}
        placeholder="Select"
        isMulti={false}
      />
    </FormGroup>
  );
};

export default BooleanField;
