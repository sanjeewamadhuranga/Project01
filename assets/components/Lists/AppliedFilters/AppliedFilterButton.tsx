import { useFormikContext } from "formik";
import React, { useMemo } from "react";
import { FormattedMessage } from "react-intl";
import { FilterType } from "../../../helpers/entries";
import { DateFieldValue } from "../../../models/form";
import { formatFromTo } from "../../../services/intl/dates";
import { companyLabel } from "../../Company/CompanyStatusLabel";
import { transactionStatusLabel } from "../../Transaction/TransactionStatus";
import { remittanceStatusLabel } from "../Remittance/RemittanceStatus";
import { useAppliedFilterButton } from "./AppliedFilterButton.hook";

interface AppliedFilterButtonProps<T> {
  name: string;
  value: T[keyof T] | string | string[] | number | DateFieldValue;
  type: FilterType;
}

export default function AppliedFilterButton<T>({
  name,
  value,
  type,
}: AppliedFilterButtonProps<T>) {
  const {
    intlPrefixes: { label, statusPrefix },
  } = useAppliedFilterButton(type);

  const { setFieldValue, handleSubmit } = useFormikContext();

  const statusObject = useMemo(() => {
    if (type === "merchant") {
      return companyLabel;
    } else if (type === "remittance") {
      return remittanceStatusLabel;
    }

    return transactionStatusLabel;
  }, [type]);

  const handleClearField = () => {
    setFieldValue(name, "");
    handleSubmit();
  };

  if (type === "merchantTransaction" && name === "merchant") return null;

  return (
    <div className="form-filter-result align-items-center my-1 mx-1">
      <p className="text-650 fw-semi-bold m-0">
        <FormattedMessage id={`${label}.form.${name}`} />
        {": "}
      </p>
      <strong style={{ marginLeft: 6, marginRight: 6 }}>
        {name === "status" ? (
          <FormattedMessage
            id={`${statusPrefix}.status.${
              statusObject[`${value}`] ?? "unknown"
            }`}
          />
        ) : name === "created" ||
          name === "confirmed" ||
          name === "merchantAdded" ? (
          typeof value === "object" && formatFromTo(value as DateFieldValue)
        ) : typeof value === "number" ? (
          value / 100
        ) : (
          `${value ?? "-"}`
        )}
      </strong>
      <span onClick={handleClearField} className="fas fa-xmark"></span>
    </div>
  );
}
