import React, { PropsWithChildren } from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import RequestReportButton, { Props } from "./RequestReportButton";
import { act, fireEvent, waitFor } from "@testing-library/react";
import { ReportModule } from "../../models/reports";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import Chance from "chance";

const chance = new Chance();
const renderComponent = (props: PropsWithChildren<Props>) =>
  renderWithIntl(<RequestReportButton {...props} />);

describe("<RequestReportButton /> component", () => {
  let mockedAxios;

  beforeAll(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
  });

  test("attempt btn to invoke post request successfully", async () => {
    const title = chance.word();
    mockedAxios.onPost(`/report/request/${ReportModule.autocredit}`).reply(200);

    const { getByRole } = renderComponent({
      className: "test-class",
      urlParams: {
        name: title,
      },
      module: ReportModule.autocredit,
      children: <h3>{title}</h3>,
    });

    expect(getByRole("button")).toBeInTheDocument();
    expect(getByRole("button")).toHaveClass("test-class");
    expect(getByRole("heading", { level: 3, name: title })).toBeInTheDocument();

    await act(async () => {
      fireEvent.click(getByRole("button"));
    });

    await waitFor(() => {
      expect(mockedAxios.history.post[0].url).toStrictEqual(
        `/report/request/${ReportModule.autocredit}`
      );
      expect(mockedAxios.history.post[0].params).toStrictEqual({ name: title });
    });
  });

  test("attempt btn to invoke post request with error", async () => {
    const title = chance.word();
    mockedAxios.onPost(`/report/request/${ReportModule.autocredit}`).reply(404);

    const { getByRole } = renderComponent({
      urlParams: {
        name: title,
      },
      module: ReportModule.autocredit,
      children: <h3>{title}</h3>,
    });

    await act(async () => {
      fireEvent.click(getByRole("button"));
    });

    await waitFor(() => {
      expect(mockedAxios.history.post[0].url).toStrictEqual(
        `/report/request/${ReportModule.autocredit}`
      );
      expect(mockedAxios.history.post[0].params).toStrictEqual({ name: title });
    });
  });
});
