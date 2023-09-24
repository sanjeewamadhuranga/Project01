import React from "react";
import { renderWithIntl } from "../../../helpers/testHelpers";
import CloseDisputeButton, {
  CloseDisputeButtonProps,
} from "./CloseDisputeButton";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import { act, fireEvent, waitFor } from "@testing-library/react";
import Chance from "chance";

const chance = new Chance();

const renderComponent = (props: CloseDisputeButtonProps) =>
  renderWithIntl(<CloseDisputeButton {...props} />);

const reloadMock = jest.fn();

const location = window.location;
delete window.location;
window.location = { ...location, reload: reloadMock };

describe("<CloseDisputeButton /> component", () => {
  let mockedAxios;
  beforeEach(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
    reloadMock.mockClear();
  });

  const onConfirmationClose = jest.fn();
  const id = chance.guid();

  test("should render action button", () => {
    const { getByRole } = renderComponent({
      id,
      onConfirmationClose,
    });

    expect(getByRole("button", { name: "Close dispute" })).toBeInTheDocument();
  });

  test("attempt to dismiss modal, by canceling btn", async () => {
    const { getByRole, getByText, queryByRole } = renderComponent({
      id,
      onConfirmationClose,
    });

    const disputeBtn = getByRole("button", { name: "Close dispute" });

    await act(async () => {
      fireEvent.click(disputeBtn);
    });

    await waitFor(() => expect(getByRole("dialog")).toHaveClass("show"));

    expect(getByText("Confirm this action")).toBeInTheDocument();
    expect(
      getByRole("heading", { level: 2, name: "Close dispute" })
    ).toBeInTheDocument();

    const cancelBtn = getByRole("button", { name: "No, go back" });
    expect(cancelBtn).toBeInTheDocument();
    await act(async () => {
      fireEvent.click(cancelBtn);
    });

    await waitFor(() => expect(queryByRole("dialog")).toBeNull());
  });

  test("attempt to dispute modal by confirm btn", async () => {
    mockedAxios
      .onPost(`/compliance/disputes/${id}/close_dispute`)
      .reply(200, {});

    const { getByRole, queryByRole } = renderComponent({
      id,
      onConfirmationClose,
    });

    const disputeBtn = getByRole("button", { name: "Close dispute" });

    await act(async () => {
      fireEvent.click(disputeBtn);
    });

    await waitFor(() => expect(getByRole("dialog")).toHaveClass("show"));

    const confirmBtn = getByRole("button", { name: "Yes, close" });
    expect(confirmBtn).toBeInTheDocument();
    await act(async () => {
      fireEvent.click(confirmBtn);
    });

    await waitFor(() =>
      expect(mockedAxios.history.post[0].url).toStrictEqual(
        `/compliance/disputes/${id}/close_dispute`
      )
    );

    expect(onConfirmationClose).toHaveBeenCalled();
    await waitFor(() => expect(queryByRole("dialog")).toBeNull());
    expect(window.location.reload).toHaveBeenCalledTimes(1);
  });

  test("attempt to dismiss modal, by clicking outside", async () => {
    const { getByRole, queryByRole } = renderComponent({
      id,
      onConfirmationClose,
    });

    const disputeBtn = getByRole("button", { name: "Close dispute" });

    await act(async () => {
      fireEvent.click(disputeBtn);
    });

    await waitFor(() => expect(getByRole("dialog")).toHaveClass("show"));

    await act(async () => {
      fireEvent.click(getByRole("dialog"));
    });

    await waitFor(() => expect(queryByRole("dialog")).toBeNull());
  });
});
