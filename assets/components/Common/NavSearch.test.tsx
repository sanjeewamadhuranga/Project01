import React from "react";
import NavSearch from "./NavSearch";
import MockAdapter from "axios-mock-adapter";
import { renderWithIntl } from "../../helpers/testHelpers";
import { act } from "react-dom/test-utils";
import { fireEvent, waitFor } from "@testing-library/react";
import axios from "axios";
import { Entity } from "../../models/common";
import { SerachType } from "./SearchResult";
import Chance from "chance";

const chance = new Chance();

const renderComponent = () => renderWithIntl(<NavSearch />);
describe("<NavSearch /> component", () => {
  test("should render static content", () => {
    const { getAllByRole, getByPlaceholderText } = renderComponent();

    expect(getByPlaceholderText("Search...")).toBeInTheDocument();
    expect(getAllByRole("link")[0]).toHaveTextContent("Transactions");
    expect(getAllByRole("link")[1]).toHaveTextContent("Merchants");
    expect(getAllByRole("link")[2]).toHaveTextContent("Administrators");
  });

  describe("test user interactions", () => {
    let mockedAxios;
    let searchValue;
    beforeAll(() => {
      mockedAxios = new MockAdapter(axios);
      searchValue = chance.string({ length: 3 });
    });

    afterEach(() => {
      mockedAxios.reset();
      jest.resetAllMocks();
    });

    test("typing search value in input, clear text value", async () => {
      const { getByRole, getAllByRole, getByPlaceholderText } =
        renderComponent();

      const searchInput = getByPlaceholderText("Search...");
      await act(async () => {
        fireEvent.change(searchInput, { target: { value: searchValue } });
      });

      await waitFor(() => {
        expect(searchInput).toHaveValue(searchValue);
        expect(getByRole("button")).toBeInTheDocument();
      });
      expect(mockedAxios.history.get).toHaveLength(3);
      expect(getAllByRole("link")[0]).toHaveAttribute(
        "href",
        `/transactions?filters[search]=${searchValue}`
      );
      expect(getAllByRole("link")[1]).toHaveAttribute(
        "href",
        `/merchants?filters[searchMerchant]=${searchValue}`
      );
      expect(getAllByRole("link")[2]).toHaveAttribute(
        "href",
        `/administrators?filters[search]=${searchValue}`
      );
      await act(async () => {
        fireEvent.click(getByRole("button"));
      });

      await waitFor(() => {
        expect(searchInput).toHaveValue("");
      });
    });

    test("typing search value in input, should display max results", async () => {
      const transactions = fake.transaction.transactionList();
      const merchants = fake.company.companyList();
      const administrators = fake.user.administratorList();
      mockedAxios
        .onGet("/search/transaction", { params: { query: searchValue } })
        .reply(200, transactions)
        .onGet("/search/merchant", { params: { query: searchValue } })
        .reply(200, merchants)
        .onGet("/search/administrator", { params: { query: searchValue } })
        .reply(200, administrators);

      const { getByPlaceholderText, getByTestId, getByText } =
        renderComponent();

      const searchInput = getByPlaceholderText("Search...");
      await act(async () => {
        fireEvent.change(searchInput, { target: { value: searchValue } });
      });

      await waitFor(() => expect(mockedAxios.history.get).toHaveLength(3));

      mockedAxios.history.get.forEach((request) => {
        expect(request.params).toStrictEqual({ query: searchValue });
      });

      function checkLinkItem<T extends Entity>(arr: T[], prefix: SerachType) {
        arr.slice(0, 3).forEach((item) => {
          if (prefix === "transactions") {
            expect(getByText(item.id)).toBeInTheDocument();
          }
          expect(getByTestId(`${prefix}-${item.id}`)).toHaveAttribute(
            "href",
            `/${prefix}/${item.id}`
          );
        });
      }

      checkLinkItem(transactions, "transactions");
      checkLinkItem(merchants, "merchants");
      checkLinkItem(administrators, "administrators");
    });

    test("test closing dropdown by esc key", async () => {
      const { getByPlaceholderText, getByRole } = renderComponent();

      const searchInput = getByPlaceholderText("Search...");
      await act(async () => {
        fireEvent.change(searchInput, { target: { value: searchValue } });
      });

      await waitFor(() => expect(getByRole("menu")).toHaveClass("show"));

      await act(async () => {
        fireEvent.keyDown(getByRole("banner"), {
          key: "Escape",
          code: "Escape",
          keyCode: 27,
          charCode: 27,
        });
      });

      await waitFor(() => expect(getByRole("menu")).not.toHaveClass("show"));
    });
  });
});
