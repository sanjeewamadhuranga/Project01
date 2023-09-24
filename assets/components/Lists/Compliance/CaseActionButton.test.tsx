import React from "react";
import CaseActionButton, { CaseActionButtonProps } from "./CaseActionButton";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import { act, fireEvent, waitFor } from "@testing-library/react";
import { renderWithIntl } from "../../../helpers/testHelpers";
import { CaseStatus } from "./CaseStatusLabel";
import Chance from "chance";

const chance = new Chance();

const renderComponent = (props: CaseActionButtonProps) =>
  renderWithIntl(<CaseActionButton {...props} />);

describe("<CaseActionButton /> component", () => {
  let mockedAxios;
  beforeAll(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
  });

  const id = chance.guid();
  const routePrefix = chance.url();
  const email = chance.email();
  test("should display empty div, due to passed closed case status", () => {
    const { getByTestId } = renderComponent({
      id,
      status: CaseStatus.closed,
      routePrefix,
      email: null,
    });

    expect(getByTestId("empty-div")).toBeInTheDocument();
  });

  test("should display button, due to passed case status diff than closed", () => {
    mockedAxios.onPut(`/remittances/${id}/mark-as-paid`).reply(200, {});
    const { getByRole } = renderComponent({
      id,
      status: CaseStatus.open,
      routePrefix,
      email,
    });

    expect(getByRole("button")).toBeInTheDocument();
  });

  test("try click on button", async () => {
    const { getByRole } = renderComponent({
      id,
      status: CaseStatus.open,
      routePrefix,
      email: null,
    });

    await act(() => {
      fireEvent.click(getByRole("button"));
    });

    await waitFor(() =>
      expect(getByRole("button", { name: "Assign reviewer" })).toHaveAttribute(
        "href",
        `/compliance/case/${id}`
      )
    );
  });
});
