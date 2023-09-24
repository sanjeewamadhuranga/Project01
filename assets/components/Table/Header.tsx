import React, { ReactElement, useContext } from "react";
import {
  Column,
  ColumnId,
  Columns,
  DataRow,
  Sort,
  SortDirection,
} from "../../models/table";
import TableContext, { TableContextProps } from "./TableContext";

interface Props<T extends DataRow> {
  columns: Columns<T>;
}

const TableHeader = <T extends DataRow>(props: Props<T>): ReactElement => {
  const table = useContext(TableContext as React.Context<TableContextProps<T>>);

  const isSortable = (column: Column<T>): boolean =>
    table.sortable && column.id !== null && (column.sortable ?? true);

  const className = (column: Column<T>): string => {
    if (table.sort.column === column.id) {
      return table.sort.direction === SortDirection.asc
        ? "fas fa-sort-up"
        : "fas fa-sort-down";
    }

    return "fas fa-sort text-muted";
  };

  const toggleSort = (column: Column<T>): void => {
    if (isSortable(column)) {
      table.setSort(getNextSortValue(column));
    }
  };

  const getNextSortValue = (column: Column<T>): Sort<T> => {
    let direction =
      table.sort.direction === SortDirection.desc
        ? SortDirection.asc
        : SortDirection.desc;
    let columnName: ColumnId<T> = column.id;

    if (table.sort.column !== column.id) {
      direction = SortDirection.asc;
    } else if (direction === SortDirection.asc) {
      columnName = null;
    }

    return { column: columnName, direction };
  };

  const renderColumn = (column: Column<T>, index: number): ReactElement => (
    <th key={index} scope="col" onClick={toggleSort.bind(this, column)}>
      <div className="d-flex align-items-end">
        <div>{column.name}</div>
        <div data-testid="sort-trigger" className="ms-2">
          {isSortable(column) && <span className={className(column)} />}
        </div>
      </div>
    </th>
  );

  return (
    <thead>
      <tr className="bg-100 user-select-none table-header" role="button">
        {props.columns.map(renderColumn)}
      </tr>
    </thead>
  );
};

export default TableHeader;
