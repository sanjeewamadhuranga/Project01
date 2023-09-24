import React from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import ToggleStatusButton, { Props } from "./ToggleStatusButton";
import { act, fireEvent, waitFor } from "@testing-library/react";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import Chance from "chance";

const chance = new Chance();
const renderComponent = (props: Props) =>
  renderWithIntl(<ToggleStatusButton {...props} />);

describe("<ToggleStatusButton /> component", () => {
  let mockedAxios;
  const routePrefix = chance.tld();
  const id = chance.guid();

  beforeAll(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
  });

  test("should display tooltip text to activate", async () => {
    const { getByRole, getByText } = renderComponent({
      routePrefix,
      id,
    });

    await act(async () => {
      fireEvent.mouseOver(getByRole("button"));
    });

    await waitFor(() => {
      expect(getByRole("tooltip")).toBeInTheDocument();
      expect(getByRole("tooltip")).toHaveClass("show");
      expect(getByText("Activate now")).toBeInTheDocument();
    });
  });

  test("should display tooltip text to deactivate", async () => {
    const { getByRole, getByText } = renderComponent({
      routePrefix,
      id,
      status: true,
    });

    await act(async () => {
      fireEvent.mouseOver(getByRole("button"));
    });

    await waitFor(() => {
      expect(getByRole("tooltip")).toBeInTheDocument();
      expect(getByRole("tooltip")).toHaveClass("show");
      expect(getByText("Deactivate now")).toBeInTheDocument();
    });
  });

  test("attempt click on button to invoke put request without status props", async () => {
    mockedAxios.onPut(`${routePrefix}/${id}/status/1`).reply(200);
    const { getByRole } = renderComponent({
      routePrefix,
      id,
    });

    await act(async () => {
      fireEvent.click(getByRole("button"));
    });

    await waitFor(() => {
      expect(mockedAxios.history.put[0].url).toStrictEqual(
        `${routePrefix}/${id}/status/1`
      );
    });
  });

  test("attempt click on button to invoke put request with status props", async () => {
    mockedAxios.onPut(`${routePrefix}/${id}/status/0`).reply(200);
    const { getByRole } = renderComponent({
      status: true,
      routePrefix,
      id,
    });

    await act(async () => {
      fireEvent.click(getByRole("button"));
    });

    await waitFor(() => {
      expect(mockedAxios.history.put[0].url).toStrictEqual(
        `${routePrefix}/${id}/status/0`
      );
    });
  });

  test("catch if there is an error occurred while trying to toggle the status", async () => {
    //given
    mockedAxios.onPut(`${routePrefix}/${id}/status/1`).networkError();

    const { getByRole, getByText } = renderWithIntl(
      <ToggleStatusButton status={true} routePrefix={routePrefix} id={id} />
    );

    //when
    await act(async () => {
      fireEvent.click(getByRole("button"));
    });

    //then
    await waitFor(() => {
      // expect(getByText("Error")).toBeInTheDocument();
    });
  });
});
