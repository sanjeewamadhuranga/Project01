import React from "react";
import "../../helpers/mocks/react-select";
import { renderWithForm } from "../../helpers/testHelpers";
import SelectField from "./SelectField";
import { fireEvent } from "@testing-library/dom";
import Chance from "chance";

const chance = new Chance();

jest.mock("react-select-fetch", () => ({}));

describe("<SelectField /> component", () => {
  test("test select with passed options", async () => {
    const optionsData = [
      {
        label: chance.first(),
        value: chance.name(),
      },
      {
        label: chance.first(),
        value: chance.name(),
      },
    ];
    const { getAllByRole, getByTestId } = renderWithForm(
      <SelectField
        options={optionsData}
        isMultiple={false}
        label="transaction.form.currencies"
        name="currencies"
      />
    );

    const options = getAllByRole("option");
    expect(options.length).toEqual(2);
    fireEvent.change(getByTestId("select"), {
      target: { value: optionsData[1].value },
    });
  });
});
