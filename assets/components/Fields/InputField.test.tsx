import React from "react";
import { act, fireEvent } from "@testing-library/react";
import { renderWithForm } from "../../helpers/testHelpers";
import InputField, { Props } from "./InputField";
import Chance from "chance";

const chance = new Chance();

const intlPrefix = "merchant.merchants.form";
const renderComponent = (props: Props) =>
  renderWithForm(<InputField {...props} />);

describe("<InputField /> component", () => {
  describe("should display submit button", () => {
    test("size sm", () => {
      const { getByRole, queryByRole } = renderComponent({
        name: "submit",
        label: "common.actions.submit",
        type: "submit",
      });

      expect(getByRole("button")).toBeInTheDocument();
      expect(getByRole("button")).toHaveClass("btn-sm");
      expect(queryByRole("textbox")).toBeNull();
    });

    test("size lg", () => {
      const { getByRole, queryByRole } = renderComponent({
        name: "submit",
        size: "lg",
        label: "common.actions.submit",
        type: "submit",
      });

      expect(getByRole("button")).toBeInTheDocument();
      expect(getByRole("button")).toHaveClass("btn-lg");
      expect(queryByRole("textbox")).toBeNull();
    });
  });

  describe("should display text input, due to props type", () => {
    test("without placeholder", () => {
      const { getByText, queryByPlaceholderText, getByRole } = renderComponent({
        name: "searchMerchant",
        label: `${intlPrefix}.searchMerchantLabel`,
        type: "text",
      });

      expect(getByText("Search merchant")).toBeInTheDocument();
      expect(getByRole("textbox")).toBeInTheDocument();
      expect(
        queryByPlaceholderText(
          "Search for trading name, address, merchant ID etc."
        )
      ).toBeNull();
    });

    test("with passed placeholder", () => {
      const { getByText, getByPlaceholderText, getByRole } = renderComponent({
        name: "searchMerchant",
        label: `${intlPrefix}.searchMerchantLabel`,
        placeHolder: `${intlPrefix}.searchMerchantPlaceholder`,
        type: "text",
      });

      expect(getByText("Search merchant")).toBeInTheDocument();
      expect(getByRole("textbox")).toBeInTheDocument();
      expect(
        getByPlaceholderText(
          "Search for trading name, address, merchant ID etc."
        )
      ).toBeInTheDocument();
    });
  });

  describe("user interactions", () => {
    test("test typing on input", async () => {
      const { getByPlaceholderText } = renderComponent({
        name: "searchMerchant",
        label: `${intlPrefix}.searchMerchantLabel`,
        placeHolder: `${intlPrefix}.searchMerchantPlaceholder`,
        type: "text",
      });
      const textValue = chance.sentence();

      const input = getByPlaceholderText(
        "Search for trading name, address, merchant ID etc."
      );

      await act(async () => {
        fireEvent.change(input, { target: { value: textValue } });
      });

      expect(input).toHaveDisplayValue(textValue);
    });
  });
});
