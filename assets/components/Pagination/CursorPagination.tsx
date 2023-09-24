import React, { ReactElement, useContext } from "react";
import BPagination from "react-bootstrap/Pagination";
import TableContext from "../Table/TableContext";

const CursorPagination = (): ReactElement => {
  const table = useContext(TableContext);
  return (
    <BPagination className="mb-0">
      <BPagination.Prev
        onClick={() => table.setCursor(table.pagination.previousCursor)}
        disabled={table.pagination.previousCursor === null}
      />
      <BPagination.Next
        onClick={() => table.setCursor(table.pagination.nextCursor)}
        disabled={table.pagination.nextCursor === null}
      />
    </BPagination>
  );
};

export default CursorPagination;
