import React from "react";
import { Columns, Data, DataRow, Sort } from "../../models/table";
import Header from "./Header";
import Body from "./Body";
import { Table } from "react-bootstrap";
import { HasLoadingState } from "../../models/common";
import settings from "../../services/settings";

interface Props<T extends DataRow> extends HasLoadingState {
  columns: Columns<T>;
  data: Data<T>;
  sort: Sort<T>;
}

const DataTable = <T extends DataRow>(props: Props<T>): JSX.Element => (
  <Table
    hover
    responsive
    size={settings.layout.condensed ? "sm" : undefined}
    className="m-0"
  >
    <Header<T> columns={props.columns} />
    <Body<T>
      columns={props.columns}
      data={props.data}
      isLoading={props.isLoading}
    />
  </Table>
);

export default DataTable;
