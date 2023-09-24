import React from "react";
import { Field } from "formik";
import { FieldProps } from "../../models/form";
import { Button, Form } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";

export interface Props extends FieldProps {
  isDisabled?: boolean;
  validateOnBlur?: boolean;
  placeHolder?: string;
  size?: "sm" | "lg";
  type: string;
  onBlur?: (e: Event | React.FocusEvent<any, Element>) => void;
}

export default function InputField({
  validateOnBlur = true,
  isDisabled = false,
  onBlur,
  ...props
}: Props): JSX.Element {
  const intl = useIntl();
  return (
    <div className="mb-3">
      {props.type === "submit" && (
        <Button
          role="button"
          type="submit"
          size={props.size ?? "sm"}
          color="primary"
          disabled={isDisabled}
          className="w-100 mt-4 action-download-button"
        >
          <FormattedMessage id={props.label} />
        </Button>
      )}

      {props.type === "text" && props.label !== null && (
        <Form.Group>
          <Form.Label>
            <FormattedMessage id={props.label} />
          </Form.Label>
          <Field name={props.name}>
            {({ field, meta }) => {
              return (
                <div className="input-field">
                  <Form.Control
                    disabled={isDisabled}
                    type={props.type}
                    placeholder={
                      props.placeHolder
                        ? intl.formatMessage({ id: props.placeHolder })
                        : undefined
                    }
                    {...field}
                    onBlur={(e) => {
                      field.onBlur(e);
                      onBlur && onBlur(e);
                    }}
                  />
                  {meta.error &&
                  (!validateOnBlur || (validateOnBlur && meta.touched)) ? (
                    <Form.Control.Feedback
                      className="d-flex text-capitalize"
                      type="invalid"
                    >
                      {meta.error}
                    </Form.Control.Feedback>
                  ) : null}
                </div>
              );
            }}
          </Field>
        </Form.Group>
      )}
    </div>
  );
}
