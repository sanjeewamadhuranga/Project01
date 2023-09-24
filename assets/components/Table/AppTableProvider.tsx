import React, {
  ReactElement,
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";
import axios, { CancelTokenSource } from "axios";
import { Data, DataRow, PaginationType, Sort } from "../../models/table";
import TableContext, { TableContextProps } from "./TableContext";
import orderBy from "lodash/orderBy";
import { usePagination } from "../../hooks/pagination";
import { getTableData } from "../../services/request";
import Fuse from "fuse.js";

interface Props<T extends DataRow = DataRow> {
  source: string;
  extraData: Record<string, unknown>;
  paginationType: false | PaginationType;
  sortable: boolean;
  limit: number;
  sort: Sort<T>;
  serverSide: boolean;
  search?: string | null;
  searchableFields: Array<string>;
  children?: React.ReactNode;
  noResult: ReactElement | null;
  fetchCallback?: (fn: () => any) => any;
}

type DrawState = {
  draw: number;
  currentDrawn: number;
};

function AppTableProvider<T extends DataRow>({
  limit,
  paginationType,
  search,
  extraData,
  serverSide,
  searchableFields,
  ...props
}: Props<T>) {
  const { pagination, setPagination, setCursor, setLimit, setPage } =
    usePagination({
      type: paginationType,
      limit,
    });
  const token = useRef<CancelTokenSource | undefined>(undefined);
  const [sort, setSort] = useState<Sort<T>>(props.sort);
  const [isLoading, setLoading] = useState<boolean>(false);
  const [rows, setRows] = useState<Data<T>>([]);
  const [draws, setDraws] = useState<DrawState>({ draw: 1, currentDrawn: 0 });

  useEffect(() => {
    setSort(props.sort);
  }, [props.sort]);

  useEffect(() => {
    if (!!props.fetchCallback) {
      props.fetchCallback(getServerData);
    }
  }, [props.fetchCallback]);

  const getServerData = useCallback(async (): Promise<void> => {
    const incrementedDraw = draws.draw + 1;
    setDraws({ ...draws, draw: incrementedDraw });
    if (!!token && !!token.current) {
      token.current?.cancel();
    }
    setLoading(true);
    token.current = axios.CancelToken.source();
    const data = await getTableData<T>(
      props.source,
      {
        draw: draws.draw,
        sort,
        search,
        extraData,
        pagination,
      },
      token.current,
      serverSide
    );

    if (!data || (!!data && data.draw <= draws.currentDrawn)) {
      return;
    }

    setPagination({
      ...pagination,
      recordsFiltered: serverSide
        ? data?.pagination?.filteredCount ?? 0
        : data.data.length,
      recordsTotal: serverSide
        ? data?.pagination?.totalCount ?? 0
        : data.data.length,
      type: serverSide
        ? data?.pagination?.type ?? "length_aware"
        : "length_aware",
      nextCursor: data?.pagination?.nextCursor ?? null,
      previousCursor: data?.pagination?.previousCursor ?? null,
      hasNextPage: data?.pagination?.nextPage ?? false,
      hasPreviousPage: data?.pagination?.previousPage ?? false,
    });
    setDraws({ draw: incrementedDraw, currentDrawn: data.draw ?? draws.draw });
    setRows(data.data);

    setLoading(false);
  }, [
    draws,
    extraData,
    pagination,
    props.source,
    search,
    serverSide,
    setPagination,
    sort,
  ]);

  useEffect(() => {
    if (!!props.fetchCallback) {
      props.fetchCallback(getServerData);
    }
  }, [props.fetchCallback]);

  useEffect(() => {
    if (!serverSide) {
      getServerData();
    }
  }, []);

  useEffect(() => {
    if (serverSide) {
      getServerData();
    }
  }, [
    sort,
    pagination.currentPage,
    pagination.limit,
    pagination.cursor,
    extraData,
  ]);

  const localItems = useMemo(
    () =>
      new Fuse<T>([], {
        keys: searchableFields,
        threshold: 0.2,
      }),
    [searchableFields]
  );

  const getFilteredRows = useCallback((): Array<T> => {
    if (!search) return rows;

    localItems.setCollection(rows);

    return localItems.search(search).map(({ item }) => item);
  }, [search, rows, localItems]);

  const getSlice = useCallback((): Array<T> => {
    const offset = (pagination.currentPage - 1) * pagination.limit;
    return orderBy(
      getFilteredRows() as any,
      [sort.column],
      [sort.direction]
    ).slice(offset, offset + pagination.limit);
  }, [
    getFilteredRows,
    pagination.currentPage,
    pagination.limit,
    sort.column,
    sort.direction,
  ]);

  return (
    <TableContext.Provider
      value={
        {
          sortable: props.sortable,
          sort,
          pagination: {
            ...pagination,
            recordsFiltered: serverSide
              ? pagination.recordsFiltered
              : getFilteredRows().length,
          },
          setSort: (sort: Sort<T>) => setSort(sort),
          setLimit,
          setPage,
          setCursor,
          noResult: props.noResult,
          serverSide,
          reload: getServerData,
          isLoading,
          rows: serverSide ? rows : getSlice(),
        } as TableContextProps
      }
    >
      {props.children}
    </TableContext.Provider>
  );
}

export default AppTableProvider;
