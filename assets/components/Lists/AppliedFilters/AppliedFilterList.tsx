import { useFormikContext } from "formik";
import React, { useMemo } from "react";
import { FormattedMessage } from "react-intl";
import { FormValueType, FilterType, toEntries } from "../../../helpers/entries";
import AppliedFilterButton from "./AppliedFilterButton";

interface AppliedFilterListProps<
  T extends Record<string, FormValueType> = Record<string, FormValueType>
> {
  submittedValues: T;
  filterType: FilterType;
}

export default function AppliedFilterList<
  T extends Record<string, FormValueType> = Record<string, FormValueType>
>({ submittedValues, filterType }: AppliedFilterListProps<T>) {
  const { resetForm, handleSubmit } = useFormikContext();

  const handleClearForm = () => {
    resetForm();
    handleSubmit();
  };

  const entriesValues = useMemo(
    () =>
      toEntries<T>(submittedValues).filter(([_, value]) =>
        typeof value === "number" || typeof value === "object"
          ? !!value
          : !!value && !!(value as ArrayLike<unknown>).length
      ),
    [submittedValues]
  );

  return (
    <>
      <div className="d-flex align-items-center flex-wrap">
        <label className="form-label text-950 mb-0 me-1">
          <FormattedMessage id="common.resultFilteredBy" />
        </label>
        {!!entriesValues.length &&
          entriesValues.map(([key, value]) => {
            return (
              <AppliedFilterButton
                key={key}
                name={key}
                value={value as string}
                type={filterType}
              />
            );
          })}
      </div>
      <div className="col-auto">
        <button
          className="btn btn-sm btn-falcon-default"
          onClick={handleClearForm}
        >
          <FormattedMessage id="common.actions.clearSearch" />
        </button>
      </div>
    </>
  );
}
