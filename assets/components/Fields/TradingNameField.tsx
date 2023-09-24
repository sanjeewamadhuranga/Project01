import React, { ReactElement } from "react";
import { FieldProps } from "../../models/form";
type Props = FieldProps;
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

export default function TradingNameField(props: Props): ReactElement {
  return (
    <FormGroup>
      <FormLabel>
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field
        name={props.name}
        fetchUrl={"/quick-search/companies"}
        component={CustomSelect}
        placeholder="Select"
        isFetch={true}
        isMulti={true}
      />
    </FormGroup>
  );
}
