import { useCallback, useState } from "react";
import { PaginationInfo } from "../components/Table/TableContext";
import { PaginationType } from "../models/table";
import { setPaginationLimit } from "../services/settings";

type Props = {
  limit: number;
  type: PaginationType | false;
};

export const usePagination = ({ limit, type }: Props) => {
  const [pagination, setPagination] = useState<PaginationInfo>({
    currentPage: 1,
    limit,
    recordsTotal: 0,
    recordsFiltered: 0,
    type: !type ? "length_aware" : type,
    cursor: null,
    nextCursor: null,
    previousCursor: null,
    hasNextPage: false,
    hasPreviousPage: false,
  });

  const setLimit = useCallback(
    (limit: number): void => {
      setPagination({
        ...pagination,
        limit: limit,
        currentPage: 1,
        cursor: null,
      });

      setPaginationLimit(limit);
    },
    [pagination]
  );

  const setPage = useCallback(
    (page: number): void => {
      setPagination({
        ...pagination,
        currentPage: page,
      });
    },
    [pagination]
  );

  const setCursor = useCallback(
    (cursor: null | string): void => {
      setPagination({
        ...pagination,
        cursor,
      });
    },
    [pagination]
  );

  return {
    pagination,
    setPagination,
    setCursor,
    setLimit,
    setPage,
  };
};
