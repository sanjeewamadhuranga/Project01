import React, { ReactElement, useContext } from "react";
import { Column, Columns, ColValue, Data, DataRow } from "../../models/table";
import TableContext, { TableContextProps } from "./TableContext";
import { HasLoadingState } from "../../models/common";
import { Placeholder } from "react-bootstrap";
import ErrorBoundary from "../Common/ErrorBoundary";

interface Props<T extends DataRow> extends HasLoadingState {
  columns: Columns<T>;
  data: Data<T>;
}

const Body = <T extends DataRow>(props: Props<T>): ReactElement => {
  const table = useContext(TableContext as React.Context<TableContextProps<T>>);
  const loaderRowPlaceholder = Array(
    Math.min(
      ...[table.pagination.limit, table.pagination.recordsFiltered].filter(
        (v) => v > 0
      )
    )
  ).fill(0);

  const renderCell = (
    column: Column<T>,
    columnIndex: number,
    row: T
  ): JSX.Element => {
    const colValue: ColValue = column.id !== null ? row[column.id] : null;

    return (
      <ErrorBoundary
        key={column.id ?? columnIndex.toString()}
        fallback={
          <Placeholder as="td" animation="glow" key={column.id ?? columnIndex}>
            <Placeholder
              style={{ width: `${Math.floor(Math.random() * 51) + 50}%` }}
            />
          </Placeholder>
        }
      >
        <td style={column?.style} className="table-body table-row-text">
          {column.render ? column.render(colValue, row) : colValue ?? "-"}
        </td>
      </ErrorBoundary>
    );
  };

  const renderCellLoader = (columnIndex: number): JSX.Element => (
    <Placeholder as="td" animation="glow" key={columnIndex}>
      <Placeholder
        style={{ width: `${Math.floor(Math.random() * 51) + 50}%` }}
      />
    </Placeholder>
  );

  const renderData = (): JSX.Element[] =>
    props.data.map((row, rowIndex) => (
      <tr key={row?.id ?? rowIndex} data-id={row.id ?? null}>
        {props.columns.map((column, columnIndex) =>
          renderCell(column, columnIndex, row)
        )}
      </tr>
    ));

  const renderNoResults = (): JSX.Element => (
    <tr>
      <td colSpan={props.columns.length} className="text-center">
        {table.noResult}
      </td>
    </tr>
  );

  const renderLoader = (): JSX.Element[] =>
    loaderRowPlaceholder.map((row, rowIndex) => (
      <tr key={rowIndex}>
        {props.columns.map((column, columnIndex) =>
          renderCellLoader(columnIndex)
        )}
      </tr>
    ));

  return (
    <tbody className="table-body">
      {props.isLoading
        ? renderLoader()
        : props.data.length > 0
        ? renderData()
        : renderNoResults()}
    </tbody>
  );
};

export default Body;
