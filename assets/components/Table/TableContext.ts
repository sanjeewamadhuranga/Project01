import { createContext, ReactElement, useContext } from "react";
import {
  Data,
  DataRow,
  PaginationType,
  Sort,
  SortDirection,
} from "../../models/table";
import once from "lodash/once";

export interface TableContextProps<T extends DataRow = DataRow> {
  sortable: boolean;
  sort: Sort<T>;
  setSort: (sort: Sort<T>) => void;
  setLimit: (perPage: number) => void;
  setPage: (page: number) => void;
  setCursor: (cursor: null | string) => void;
  noResult: ReactElement | null;
  serverSide: boolean;
  pagination: PaginationInfo;
  reload: () => void;
  isLoading: boolean;
  rows: Data<T>;
}

export interface PaginationInfo {
  currentPage: number;
  limit: number;
  recordsTotal: number;
  recordsFiltered: number;
  type: PaginationType;
  cursor: string | null;
  nextCursor: string | null;
  previousCursor: string | null;
  hasNextPage: boolean;
  hasPreviousPage: boolean;
}

const noop = (): void => {}; // eslint-disable-line @typescript-eslint/no-empty-function

const TableContextDefault = {
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
  sortable: true,
  sort: {
    column: null,
    direction: SortDirection.desc,
  },
  setSort: noop,
  setLimit: noop,
  setPage: noop,
  setCursor: noop,
  noResult: null,
  serverSide: true,
  reload: noop,
  isLoading: false,
  rows: [],
};

export const createTableContext = once(<T extends DataRow = DataRow>() =>
  createContext<TableContextProps<T>>(
    TableContextDefault as TableContextProps<T>
  )
);

const TableContext = createContext<TableContextProps>(
  TableContextDefault as TableContextProps
);

export const useStateContext = <T extends DataRow = DataRow>() =>
  useContext(createTableContext<T>());

export default TableContext;
