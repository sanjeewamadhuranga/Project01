import React from "react";
/* eslint-disable react/display-name */

jest.mock(
  "react-select",
  () =>
    ({ options, value, onChange, getOptionValue, id }: any) => {
      const handleChange = (event: React.ChangeEvent<HTMLSelectElement>) => {
        const option = options.filter((option) => {
          const value = getOptionValue?.(option) ?? option.value;
          return value.toString() === event.target.value.toString();
        });
        onChange(option);
      };
      return (
        <select
          data-testid="select"
          id={id}
          onChange={handleChange}
          value={value ? getOptionValue?.(value) ?? value.value : ""}
        >
          {options.map(({ value, label }, idx) => {
            return (
              <option key={idx} value={value}>
                {label}
              </option>
            );
          })}
        </select>
      );
    }
);
