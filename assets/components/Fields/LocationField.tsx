import React, { ReactElement } from "react";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import CustomSelect from "../Common/CustomSelect";
import { FormattedMessage } from "react-intl";
import { FieldProps } from "../../models/form";

type Props = FieldProps;

export default function LocationField(props: Props): ReactElement {
  return (
    <FormGroup>
      <FormLabel>
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field
        name={props.name}
        fetchUrl={"/quick-search/locations"}
        component={CustomSelect}
        placeholder="Select"
        isMulti={true}
        isFetch={true}
      />
    </FormGroup>
  );
}
