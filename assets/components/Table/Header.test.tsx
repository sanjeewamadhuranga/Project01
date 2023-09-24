import React from "react";
import { render, fireEvent, act } from "@testing-library/react";
import TableHeader from "./Header";
import TableContext, { TableContextProps } from "./TableContext";
import { SortDirection } from "../../models/table";
import DateFormatter from "../Common/DateFormatter";

const routePrefix = "/configuration/country";

const columns = [
  {
    id: "countryName",
    name: "Name",
    render: (name, country): JSX.Element => (
      <a href={`${routePrefix}/${country.id}`}>{name}</a>
    ),
  },
  { id: "countryCode", name: "Country Code" },
  { id: "dialingCode", name: "Dialing Prefix" },
  {
    id: "created",
    name: "Created",
    render: (created: string) => <DateFormatter date={created} />,
  },
];

const tableContext: TableContextProps = {
  sortable: true,
  sort: {
    column: "countryCode",
    direction: SortDirection.asc,
  },
  pagination: {
    currentPage: 1,
    limit: 10,
    recordsTotal: 0,
    recordsFiltered: 0,
    type: "length_aware",
    cursor: null,
    nextCursor: null,
    previousCursor: null,
    hasNextPage: false,
    hasPreviousPage: false,
  },
  setSort: jest.fn(),
  setLimit: () => jest.fn(),
  setPage: () => jest.fn(),
  setCursor: () => jest.fn(),
  noResult: null,
  serverSide: true,
  reload: () => jest.fn(),
  isLoading: false,
  rows: [],
};

describe("<TableHeader /> component", () => {
  test("test displaying static data", () => {
    const { getByRole, getAllByRole } = render(
      <table>
        <TableHeader columns={columns} />
      </table>
    );

    expect(getByRole("button")).toHaveClass("bg-100 user-select-none");
    expect(getAllByRole("columnheader")).toHaveLength(columns.length);
  });

  test("test onClick event to check sorting function", async () => {
    const { getAllByTestId } = render(
      <TableContext.Provider value={tableContext}>
        <table>
          <TableHeader columns={columns} />
        </table>
      </TableContext.Provider>
    );
    const triggers = getAllByTestId("sort-trigger");

    expect(triggers[0].childNodes[0]).not.toHaveClass("fas fa-sort-up");
    expect(triggers[0].childNodes[0]).toHaveClass("fas fa-sort text-muted");

    await act(async () => {
      fireEvent.click(triggers[0]);
    });

    expect(tableContext.setSort).toHaveBeenCalledWith({
      column: "countryName",
      direction: "asc",
    });

    await act(async () => {
      fireEvent.click(triggers[1]);
    });

    expect(tableContext.setSort).toHaveBeenCalledWith({
      column: "countryCode",
      direction: "desc",
    });

    expect(triggers[1].childNodes[0]).toHaveClass("fas fa-sort-up");
  });
});
