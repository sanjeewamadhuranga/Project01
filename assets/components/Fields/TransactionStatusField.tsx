import React, { Component } from "react";
import { ChoiceOption, FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { TransactionStatus } from "../../models/transaction";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

type Props = FieldProps;

class TransactionStatusField extends Component<Props> {
  static readonly options: Array<ChoiceOption> = [
    { value: TransactionStatus.authorized, label: "Authorized" },
    { value: TransactionStatus.qrGenerated, label: "QR code generated" },
    { value: TransactionStatus.confirmed, label: "Confirmed" },
    { value: TransactionStatus.cancelled, label: "Cancelled" },
    { value: TransactionStatus.voided, label: "Voided" },
    { value: TransactionStatus.failed, label: "Failed" },
    { value: TransactionStatus.refunded, label: "Refunded" },
    { value: TransactionStatus.refundRequested, label: "Refund requested" },
    { value: TransactionStatus.initiated, label: "Initiated" },
  ];

  render(): JSX.Element {
    return (
      <FormGroup>
        <FormLabel>
          <FormattedMessage id={this.props.label} />
        </FormLabel>
        <Field
          name={this.props.name}
          options={TransactionStatusField.options}
          component={CustomSelect}
          placeholder="Select"
          isMulti={true}
        />
      </FormGroup>
    );
  }
}

export default TransactionStatusField;
