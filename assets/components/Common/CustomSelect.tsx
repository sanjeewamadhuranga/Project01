import { FieldProps } from "formik";
import React from "react";
import Select, {
  GroupBase,
  MultiValue,
  OnChangeValue,
  OptionsOrGroups,
  SingleValue,
} from "react-select";
import { selectStyles } from "../../styles/select";
import { SelectFetch } from "react-select-fetch";
import CreatableSelect from "react-select/creatable";

interface Option {
  label: string;
  value: string;
}

interface CustomSelectProps extends FieldProps {
  options: OptionsOrGroups<Option, any>;
  isMulti?: boolean;
  className?: string;
  classNamePrefix?: string;
  placeholder?: string;
  fetchUrl?: string;
  isFetch?: boolean;
  isCreatable?: boolean;
  defaultValue?: Option;
  onChange?: (
    option: SingleValue<Option | Option[]> | MultiValue<Option | Option[]>
  ) => void;
  isDisabled?: boolean;
  dataTestId?: string;
}

export const CustomSelect = ({
  classNamePrefix,
  className,
  placeholder,
  field,
  form,
  options,
  fetchUrl,
  defaultValue,
  isMulti = false,
  isFetch = false,
  isCreatable = false,
  // eslint-disable-next-line @typescript-eslint/no-empty-function
  onChange = () => {},
  isDisabled = false,
  dataTestId = "",
}: CustomSelectProps) => {
  const onChangeValue = (option: OnChangeValue<Option | Option[], boolean>) => {
    onChange(option);
    form.setFieldValue(
      field.name,
      isFetch
        ? option
        : isMulti
        ? (option as Option[]).map((item: Option) => item.value)
        : (option as Option).value
    );
  };

  const getValue = () => {
    if (isCreatable) {
      return field.value;
    }

    if (!field.value) {
      return isMulti ? [] : defaultValue || ("" as string);
    }

    if (!options && isFetch) {
      return field.value;
    }

    if (options) {
      return isMulti
        ? options.filter((option) => field.value.indexOf(option.value) >= 0)
        : options.find((option) => option.value === field.value);
    } else {
      return isMulti ? [] : ("" as string);
    }
  };

  if (isFetch && fetchUrl) {
    return (
      <SelectFetch<Option, GroupBase<Option>, boolean>
        data-testid={dataTestId}
        isMulti={isMulti}
        url={fetchUrl}
        value={getValue()}
        onChange={onChangeValue}
        styles={selectStyles}
        name={field.name}
        // Just to satisfy the `unknown` type for MapResponse `rawResponse`
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        mapResponse={(response: any) => {
          return {
            options: response.items,
            hasMore: response.pagination.has_next_page,
          };
        }}
        searchParamName={"q"}
        queryParams={{
          limit: 100,
        }}
      />
    );
  }

  if (isCreatable) {
    return (
      <CreatableSelect
        isClearable={true}
        isMulti={isMulti}
        name={field.name}
        onChange={onChangeValue}
        data-testid={dataTestId}
      />
    );
  }

  return (
    <Select
      classNamePrefix={classNamePrefix}
      className={className}
      name={field.name}
      value={getValue()}
      onChange={onChangeValue}
      styles={selectStyles}
      placeholder={placeholder}
      options={options}
      isMulti={isMulti}
      isDisabled={isDisabled}
      data-testid={dataTestId}
    />
  );
};

export default CustomSelect;
