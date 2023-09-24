import React from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import LengthAwarePagination, { Props } from "./LengthAwarePagination";
import { SortDirection } from "../../models/table";
import TableContext, { TableContextProps } from "../Table/TableContext";
import { fireEvent, waitFor } from "@testing-library/react";
import Chance from "chance";

const chance = new Chance();

const tableContext: TableContextProps = {
  pagination: {
    currentPage: 1,
    limit: 10,
    recordsTotal: 23,
    recordsFiltered: 23,
    type: "length_aware",
    cursor: null,
    nextCursor: null,
    previousCursor: null,
    hasNextPage: false,
    hasPreviousPage: false,
  },
  sortable: true,
  sort: {
    column: null,
    direction: SortDirection.desc,
  },
  setSort: jest.fn(),
  setLimit: jest.fn(),
  setPage: jest.fn(),
  setCursor: jest.fn(),
  noResult: null,
  serverSide: true,
  reload: jest.fn(),
  isLoading: false,
  rows: [],
};

const renderComponent = (
  props: Props,
  providerValues?: Partial<TableContextProps>
) =>
  renderWithIntl(
    <TableContext.Provider value={{ ...tableContext, ...providerValues }}>
      <LengthAwarePagination {...props} />
    </TableContext.Provider>
  );

describe("<LengthAwarePagination /> component", () => {
  const pageRange = chance.natural();
  const marginPages = chance.natural();

  test("previous btn should be disabled if current page is one", () => {
    const { getByRole, getByText, getAllByRole } = renderComponent({
      pageRange,
      marginPages,
    });
    expect(getByRole("list")).toHaveClass("pagination");
    expect(getAllByRole("listitem")[0]).toHaveClass("page-item disabled");
    expect(getByText("Previous")).toBeInTheDocument();
    expect(getByText("Previous")).toHaveClass("visually-hidden");
    expect(getByText("‹")).toHaveAttribute("aria-hidden", "true");
  });

  test("should display current page", () => {
    const { getByText, getAllByRole } = renderComponent({
      pageRange,
      marginPages,
    });
    expect(getAllByRole("listitem")[1]).toHaveClass("page-item active");
    expect(getByText("1")).toBeInTheDocument();
    expect(getByText("(current)")).toHaveClass("visually-hidden");
  });

  test("should render 5 listitems, due to 3 pages", () => {
    const { getAllByRole } = renderComponent({
      pageRange,
      marginPages,
    });
    expect(getAllByRole("listitem")).toHaveLength(5);
  });

  test("should render dots in listitems according to pageRange", () => {
    const { getByText } = renderComponent(
      {
        pageRange: 2,
        marginPages: 1,
      },
      {
        pagination: {
          ...tableContext.pagination,
          limit: 25,
          recordsTotal: 650,
          recordsFiltered: 650,
          currentPage: 1,
        },
      }
    );

    expect(getByText("…")).toBeInTheDocument();
    expect(getByText("More")).toBeInTheDocument();
  });

  test("should render pages in listitems according to pageRange", () => {
    const { getAllByRole } = renderComponent(
      {
        pageRange: 3,
        marginPages: 1,
      },
      {
        pagination: {
          ...tableContext.pagination,
          limit: 25,
          recordsTotal: 100,
          recordsFiltered: 100,
          currentPage: 4,
        },
      }
    );
    expect(getAllByRole("listitem")).toHaveLength(6);
  });

  describe("test user interactions", () => {
    test("attempt to go to the next page, by clicking btn", async () => {
      const { getAllByRole } = renderComponent({
        pageRange,
        marginPages,
      });

      fireEvent.click(getAllByRole("button")[2]);
      await waitFor(() => {
        expect(tableContext.setPage).toHaveBeenCalledWith(2);
      });
    });

    test("attempt to go to the previous page, by clicking btn", async () => {
      const { getAllByRole } = renderComponent(
        {
          pageRange,
          marginPages,
        },
        { pagination: { ...tableContext.pagination, currentPage: 2 } }
      );

      fireEvent.click(getAllByRole("button")[0]);
      await waitFor(() => {
        expect(tableContext.setPage).toHaveBeenCalledWith(1);
      });
    });

    test("attempt to go to the chosen page, by clicking page btn", async () => {
      const { getAllByRole } = renderComponent({
        pageRange,
        marginPages,
      });

      fireEvent.click(getAllByRole("button")[1]);
      await waitFor(() => {
        expect(tableContext.setPage).toHaveBeenCalledWith(3);
      });
    });
  });
});
