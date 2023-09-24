import React from "react";
import { ChoiceOption, FieldProps } from "../../models/form";
import { ManagerTasks } from "../../models/company";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage, useIntl } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

const intlPrefix = "merchantRequests.status";

const RequestTypeField = (props: FieldProps): JSX.Element => {
  const intl = useIntl();
  const options: Array<ChoiceOption> = [
    {
      value: ManagerTasks.deleteCompany,
      label: intl.formatMessage({ id: `${intlPrefix}.deleteCompany` }),
    },
    {
      value: ManagerTasks.deleteUserDetails,
      label: intl.formatMessage({ id: `${intlPrefix}.deleteUserDetail` }),
    },
    {
      value: ManagerTasks.requestPaymentProvider,
      label: intl.formatMessage({ id: `${intlPrefix}.requestPaymentProvider` }),
    },
    {
      value: ManagerTasks.reviewCompany,
      label: intl.formatMessage({ id: `${intlPrefix}.reviewCompany` }),
    },
    {
      value: ManagerTasks.loadSupplierCardFailed,
      label: intl.formatMessage({ id: `${intlPrefix}.loadSupplierCardFailed` }),
    },
    {
      value: ManagerTasks.withdrawal,
      label: intl.formatMessage({ id: `${intlPrefix}.withdrawal` }),
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

export default RequestTypeField;
