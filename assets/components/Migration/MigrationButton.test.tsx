import React from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import MigrationButton, { MigrationButtonProps } from "./MigrationButton";
import { act, fireEvent, waitFor } from "@testing-library/react";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import Chance from "chance";

const chance = new Chance();

const renderComponent = (props: MigrationButtonProps) =>
  renderWithIntl(<MigrationButton {...props} />);

describe("<MigrationButton /> component", () => {
  let mockedAxios;
  beforeAll(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
  });
  const label = chance.name();
  const url = chance.url();
  test("test clicking button, invoke request", async () => {
    const merchants = fake.company.companyList();
    mockedAxios
      .onPut("/merchants/quick-search")
      .reply(200, { items: merchants });
    const { getByRole } = renderComponent({
      label,
      url,
    });

    const btn = getByRole("button");
    expect(btn).toBeInTheDocument();
    expect(btn).toHaveTextContent(label);
    expect(btn).toHaveClass("btn btn-sm btn-falcon-default");
    await act(async () => {
      fireEvent.click(btn);
    });

    await waitFor(() => {
      expect(mockedAxios.history.put[0].url).toStrictEqual(url);
    });
  });
});
