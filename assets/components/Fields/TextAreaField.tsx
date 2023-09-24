import React from "react";
import { Field } from "formik";
import { FieldProps } from "../../models/form";
import { Form, FormGroup, FormLabel } from "react-bootstrap";
import { FormattedMessage } from "react-intl";

export interface Props extends FieldProps {
  isDisabled?: boolean;
  validateOnBlur?: boolean;
  placeHolder?: string;
  size?: "sm" | "lg";
  type?: string;
  onBlur?: (e: Event | React.FocusEvent<any, Element>) => void;
}

export default function TextAreaField(props: Props): JSX.Element {
  return (
    <FormGroup>
      <FormLabel className="form-label">
        <FormattedMessage id={props.label} />
      </FormLabel>
      <Field name={props.name}>
        {({ field, meta }) => (
          <>
            <Form.Control
              as="textarea"
              rows={3}
              isInvalid={(meta.error || meta.submitError) && meta.touched}
              {...field}
            />
            {(meta.error || meta.submitError) && meta.touched && (
              <Form.Control.Feedback type="invalid">
                {meta.error || meta.submitError}
              </Form.Control.Feedback>
            )}
          </>
        )}
      </Field>
    </FormGroup>
  );
}
