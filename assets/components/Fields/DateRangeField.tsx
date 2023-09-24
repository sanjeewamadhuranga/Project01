import React, { useCallback, useRef } from "react";
import Popup from "reactjs-popup";
import DateCalendar from "../DateCalendar/DateCalendar";
import { FormControl, FormGroup, FormLabel } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";
import { Field, FieldProps as FieldRenderProps } from "formik";
import { DateFieldValue, FieldProps } from "../../models/form";
import { PopupActions, PopupPosition } from "reactjs-popup/dist/types";
import { format } from "date-fns";

interface Props extends FieldProps {
  placeHolder?: string;
  position?: PopupPosition;
}

export default function DateRangeField({
  placeHolder,
  position,
  label,
  name,
}: Props): JSX.Element {
  const intl = useIntl();
  const popupRef = useRef<PopupActions>(null);

  const formatDateToDisplay = useCallback((val: DateFieldValue | undefined) => {
    if (!val || typeof val !== "object") return;
    return (
      format(new Date(val["min"] ?? new Date()), "yyyy/MM/dd") +
      " - " +
      format(new Date(val["max"] ?? new Date()), "yyyy/MM/dd")
    );
  }, []);

  return (
    <Field name={name}>
      {({ field, form }: FieldRenderProps) => {
        return (
          <Popup
            ref={popupRef}
            trigger={() => (
              <FormGroup>
                <FormLabel>
                  <FormattedMessage id={label} />
                </FormLabel>
                <FormControl
                  type="text"
                  defaultValue={formatDateToDisplay(field.value)}
                  placeholder={
                    placeHolder
                      ? intl.formatMessage({ id: placeHolder })
                      : undefined
                  }
                />
              </FormGroup>
            )}
            position={position ?? "bottom right"}
            closeOnDocumentClick
          >
            <DateCalendar
              startDate={
                typeof field.value === "object"
                  ? new Date(field.value["min"] ?? new Date())
                  : new Date()
              }
              endDate={
                typeof field.value === "object"
                  ? new Date(field.value["min"] ?? new Date())
                  : new Date()
              }
              setDate={(val) => form.setFieldValue(name, val)}
              close={() => popupRef.current?.close()}
            />
          </Popup>
        );
      }}
    </Field>
  );
}
