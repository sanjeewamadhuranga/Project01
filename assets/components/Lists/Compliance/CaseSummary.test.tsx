import React from "react";
import { renderWithIntl } from "../../../helpers/testHelpers";
import CaseSummary, { CaseSummaryProps } from "./CaseSummary";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import { act, fireEvent, waitFor } from "@testing-library/react";
import Chance from "chance";

const chance = new Chance();

const renderComponent = (props: CaseSummaryProps) =>
  renderWithIntl(<CaseSummary {...props} />);

describe("<CaseSummary /> component", () => {
  let mockedAxios;
  beforeEach(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
    jest.resetAllMocks();
  });

  const setFilterByFieldValue = jest.fn();
  const summarySource = chance.url();

  test("test fetched data value", async () => {
    const response = {
      open: chance.natural(),
      inReview: chance.natural(),
      inApproval: chance.natural(),
      closed: chance.natural(),
    };
    mockedAxios.onGet(summarySource).reply(200, response);

    const {
      getByRole,
      getByText,
      getAllByRole,
      getAllByText,
      getAllByTestId,
      queryAllByTestId,
    } = renderComponent({
      summarySource,
      setFilterByFieldValue,
    });

    expect(getAllByTestId("placeholder")).toHaveLength(4);
    expect(getByRole("heading", { name: "Open cases" })).toBeInTheDocument();
    expect(getByRole("heading", { name: "In review" })).toBeInTheDocument();
    expect(getByRole("heading", { name: "In approval" })).toBeInTheDocument();
    expect(getByRole("heading", { name: "Closed" })).toBeInTheDocument();

    await waitFor(() => {
      expect(mockedAxios.history.get[0].url).toStrictEqual(summarySource);
      expect(queryAllByTestId("placeholder")).toHaveLength(0);
      expect(getAllByText("0")).toHaveLength(4);
    });
    await new Promise((r) => setTimeout(r, 2050));
    await waitFor(() => {
      expect(getByText(response.open)).toBeInTheDocument();
      expect(getByText(response.inReview)).toBeInTheDocument();
      expect(getByText(response.inApproval)).toBeInTheDocument();
      expect(getByText(response.closed)).toBeInTheDocument();
    });

    expect(getAllByRole("link", { name: /show cases/i })).toHaveLength(4);
  });

  test("test response does not return value, attempt clicking on link element", async () => {
    mockedAxios.onGet(summarySource).reply(200, { data: {} });
    const { getAllByRole } = renderComponent({
      summarySource,
      setFilterByFieldValue,
    });

    await act(async () => {
      fireEvent.click(getAllByRole("link", { name: /show cases/i })[0]);
    });
    expect(setFilterByFieldValue).toHaveBeenCalledWith("open");

    await act(async () => {
      fireEvent.click(getAllByRole("link", { name: /show cases/i })[1]);
    });
    expect(setFilterByFieldValue).toHaveBeenCalledTimes(2);
    expect(setFilterByFieldValue).toHaveBeenCalledWith("in_review");

    await act(async () => {
      fireEvent.click(getAllByRole("link", { name: /show cases/i })[2]);
    });
    expect(setFilterByFieldValue).toHaveBeenCalledTimes(3);
    expect(setFilterByFieldValue).toHaveBeenCalledWith("in_approval");

    await act(async () => {
      fireEvent.click(getAllByRole("link", { name: /show cases/i })[3]);
    });
    expect(setFilterByFieldValue).toHaveBeenCalledTimes(4);
    expect(setFilterByFieldValue).toHaveBeenCalledWith("closed");
  });
});
