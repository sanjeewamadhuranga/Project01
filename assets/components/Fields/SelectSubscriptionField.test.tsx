import React from "react";
import "../../helpers/mocks/react-select";
import { renderWithForm } from "../../helpers/testHelpers";
import SelectSubscriptionField from "./SelectSubscriptionField";
import { waitFor } from "@testing-library/react";
import axios from "axios";
import Chance from "chance";

const chance = new Chance();

jest.mock("react-select-fetch", () => ({}));

describe("<SelectSubscriptionField /> component", () => {
  test("test select with loaded options", async () => {
    const responseData = [
      {
        label: chance.first(),
        value: chance.name(),
      },
      {
        label: chance.first(),
        value: chance.name(),
      },
    ];

    const axiosSpy: jest.SpyInstance = jest
      .spyOn(axios, "get")
      .mockResolvedValue({
        data: responseData,
      });

    const { getAllByRole, getByTestId, getByText, queryAllByRole } =
      renderWithForm(
        <SelectSubscriptionField
          label="merchant.merchants.form.subscriptionPlans"
          name="subscriptions"
        />
      );

    expect(getByTestId("select")).toBeInTheDocument();
    expect(getByText("Subscription Type")).toBeInTheDocument();
    expect(queryAllByRole("option")).toHaveLength(0);

    await waitFor(() =>
      expect(axiosSpy).toBeCalledWith("/quick-search/subscriptions")
    );
    await waitFor(() => expect(getAllByRole("option")).toHaveLength(2));
  });
});
