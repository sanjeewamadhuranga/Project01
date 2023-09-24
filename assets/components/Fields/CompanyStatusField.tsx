import React from "react";
import CustomSelect from "../Common/CustomSelect";
import { ChoiceOption, FieldProps } from "../../models/form";
import { CompanyStatus } from "../../models/company";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage, useIntl } from "react-intl";

const intlPrefix = "merchant.status";

const CompanyStatusField = (props: FieldProps): JSX.Element => {
  const intl = useIntl();
  const options: Array<ChoiceOption> = [
    {
      value: "",
      label: intl.formatMessage({ id: `${intlPrefix}.all` }),
    },
    {
      label: intl.formatMessage({ id: `${intlPrefix}.verified` }),
      value: CompanyStatus.verified,
    },
    {
      value: CompanyStatus.pending,
      label: intl.formatMessage({ id: `${intlPrefix}.pending` }),
    },
    {
      value: CompanyStatus.rejected,
      label: intl.formatMessage({ id: `${intlPrefix}.rejected` }),
    },
    {
      value: CompanyStatus.terminated,
      label: intl.formatMessage({ id: `${intlPrefix}.terminated` }),
    },
    {
      value: CompanyStatus.blacklisted,
      label: intl.formatMessage({ id: `${intlPrefix}.blacklisted` }),
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
        isMulti={false}
        defaultValue={options[0]}
        className="no-lastpass"
      />
    </FormGroup>
  );
};

export default CompanyStatusField;
