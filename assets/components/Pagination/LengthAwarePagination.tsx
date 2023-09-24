import React, { ReactElement, useContext } from "react";
import BPagination from "react-bootstrap/Pagination";
import TableContext from "../Table/TableContext";

export interface Props {
  pageRange: number;
  marginPages: number;
}
const LengthAwarePagination = (props: Props): ReactElement => {
  const table = useContext(TableContext);
  const selected = table.pagination.currentPage;
  const totalPages = Math.ceil(
    table.pagination.recordsFiltered / table.pagination.limit
  );
  const handlePageSelected = (page) => {
    table.setPage(page);
  };

  const handlePrevious = () => {
    handlePageSelected(Math.max(1, selected - 1));
  };

  const handleNext = () => {
    handlePageSelected(Math.min(totalPages, selected + 1));
  };

  const pageItem = (page) => (
    <BPagination.Item
      key={page}
      active={page === selected}
      onClick={() => handlePageSelected(page)}
    >
      {page}
    </BPagination.Item>
  );

  const pages = () => {
    const selectedIndex = selected - 1;
    const items: Array<JSX.Element> = [];

    if (totalPages <= props.pageRange) {
      for (let page = 1; page <= totalPages; page++) {
        items.push(pageItem(page));
      }

      return items;
    }

    let leftSide = props.pageRange / 2;
    let rightSide = props.pageRange - leftSide;

    if (selectedIndex > totalPages - props.pageRange / 2) {
      rightSide = totalPages - selectedIndex;
      leftSide = props.pageRange - rightSide;
    } else if (selectedIndex < props.pageRange / 2) {
      leftSide = selectedIndex;
      rightSide = props.pageRange - leftSide;
    }

    let breakView: JSX.Element | null = null;

    for (let index = 0; index < totalPages; index++) {
      const page = index + 1;

      if (page <= props.marginPages) {
        items.push(pageItem(page));
        continue;
      }

      if (page > totalPages - props.marginPages) {
        items.push(pageItem(page));
        continue;
      }

      if (
        index >= selectedIndex - leftSide &&
        index <= selectedIndex + rightSide
      ) {
        items.push(pageItem(page));
        continue;
      }

      if (items[items.length - 1] !== breakView) {
        breakView = <BPagination.Ellipsis key={page} />;
        items.push(breakView);
      }
    }

    return items;
  };

  return (
    <BPagination className="mb-0">
      <BPagination.Prev
        onClick={handlePrevious}
        disabled={table.pagination.currentPage === 1}
        className="pagination"
      />
      {pages()}
      <BPagination.Next
        className="pagination"
        onClick={handleNext}
        disabled={table.pagination.currentPage === totalPages}
      />
    </BPagination>
  );
};

export default LengthAwarePagination;
