import React from "react";
import { ChoiceOption, FieldProps } from "../../models/form";
import { RemittanceStatus } from "../../models/company";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage, useIntl } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

const intlPrefix = "remittance.status";
const RemittanceStatusField = (props: FieldProps): JSX.Element => {
  const intl = useIntl();
  const options: Array<ChoiceOption> = [
    {
      value: RemittanceStatus.confirmed,
      label: intl.formatMessage({ id: `${intlPrefix}.confirmed` }),
    },
    {
      value: RemittanceStatus.pending,
      label: intl.formatMessage({ id: `${intlPrefix}.pending` }),
    },
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
      />
    </FormGroup>
  );
};

export default RemittanceStatusField;
