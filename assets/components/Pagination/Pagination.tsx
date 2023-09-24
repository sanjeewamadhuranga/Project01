import React, { ReactElement, useContext } from "react";
import TableContext from "../Table/TableContext";
import LengthAwarePagination from "./LengthAwarePagination";
import CursorPagination from "./CursorPagination";
import SimplePagination from "./SimplePagination";

const Pagination = (): ReactElement => {
  const table = useContext(TableContext);

  if (table.pagination.type === "length_aware") {
    return <LengthAwarePagination pageRange={2} marginPages={1} />;
  }

  if (table.pagination.type === "cursor") {
    return <CursorPagination />;
  }

  if (table.pagination.type === "simple") {
    return <SimplePagination />;
  }

  return <></>;
};

export default Pagination;
