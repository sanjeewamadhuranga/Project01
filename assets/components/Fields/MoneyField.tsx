import React from "react";
import { Field } from "formik";
import { FieldProps } from "../../models/form";
import { FormGroup, FormLabel, FormControl } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";

interface Props extends FieldProps {
  placeHolder?: string;
}

export default function MoneyField({
  placeHolder,
  label,
  name,
}: Props): JSX.Element {
  const intl = useIntl();
  return (
    <div className="mb-3">
      <FormGroup>
        <FormLabel>
          <FormattedMessage id={label} />
        </FormLabel>
        <Field type="number" name={name}>
          {({ field, meta }) => (
            <>
              <FormControl
                name={name}
                type="number"
                placeholder={intl.formatMessage({ id: placeHolder ?? label })}
                isInvalid={(meta.error || meta.submitError) && meta.touched}
                {...field}
              />
              {(meta.error || meta.submitError) && meta.touched && (
                <FormControl.Feedback type="invalid">
                  {meta.error || meta.submitError}
                </FormControl.Feedback>
              )}
            </>
          )}
        </Field>
      </FormGroup>
    </div>
  );
}
