import React from "react";
import "../../helpers/mocks/react-select";
import { renderWithForm } from "../../helpers/testHelpers";
import TransactionStatusField from "./TransactionStatusField";
import { TransactionStatus } from "../../models/transaction";

jest.mock("react-select-fetch", () => ({}));

describe("<TransactionStatusField /> component", () => {
  test("test static values", async () => {
    const { getAllByRole } = renderWithForm(
      <TransactionStatusField label="transaction.form.status" name="status" />
    );

    const options = getAllByRole("option");
    expect(options.length).toEqual(Object.keys(TransactionStatus).length);

    Object.keys(TransactionStatus).map((val, idx) => {
      expect(options[idx]).toHaveValue(TransactionStatus[val]);
    });
  });
});
