import React from "react";
import { Field } from "formik";
import { FieldProps } from "../../models/form";
import { Form, FormGroup } from "react-bootstrap";
import { useIntl } from "react-intl";

export default function SingleCheckbox(props: FieldProps): JSX.Element {
  const intl = useIntl();
  return (
    <FormGroup>
      <Field name={props.name} type="checkbox">
        {({ field, meta }) => (
          <>
            <Form.Check
              type="checkbox"
              name={props.name}
              label={intl.formatMessage({ id: props.label })}
              value={field.value}
              onChange={field.onChange}
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
