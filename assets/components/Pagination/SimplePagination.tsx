import React, { ReactElement, useContext } from "react";
import BPagination from "react-bootstrap/Pagination";
import TableContext from "../Table/TableContext";

const SimplePagination = (): ReactElement => {
  const table = useContext(TableContext);
  return (
    <BPagination className="mb-0">
      <BPagination.Prev
        onClick={() =>
          table.setPage(Math.max(1, table.pagination.currentPage - 1))
        }
        disabled={!table.pagination.hasPreviousPage}
      />
      <BPagination.Next
        onClick={() => table.setPage(table.pagination.currentPage + 1)}
        disabled={!table.pagination.hasNextPage}
      />
    </BPagination>
  );
};

export default SimplePagination;
