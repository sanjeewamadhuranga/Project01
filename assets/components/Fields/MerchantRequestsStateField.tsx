import React, { Component } from "react";
import { ChoiceOption, FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";
import { MerchantRequestState } from "../../models/MerchantRequestState";

type Props = FieldProps;

class MerchantRequestsStateField extends Component<Props> {
  static readonly options: Array<ChoiceOption> = [
    { value: MerchantRequestState.pending, label: "Pending" },
    { value: MerchantRequestState.assigned, label: "Assigned" },
    { value: MerchantRequestState.inProgress, label: "In Progress" },
    { value: MerchantRequestState.blocked, label: "Blocked" },
    { value: MerchantRequestState.inReview, label: "In review" },
    { value: MerchantRequestState.closed, label: "Closed" },
    { value: MerchantRequestState.rejected, label: "Rejected" },
  ];

  render(): JSX.Element {
    return (
      <FormGroup>
        <FormLabel>
          <FormattedMessage id={this.props.label} />
        </FormLabel>
        <Field
          name={this.props.name}
          options={MerchantRequestsStateField.options}
          component={CustomSelect}
          placeholder="Select"
          isMulti={false}
        />
      </FormGroup>
    );
  }
}

export default MerchantRequestsStateField;
