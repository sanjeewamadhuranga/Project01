import React, { Component, ReactElement } from "react";
import {
  Columns,
  Data,
  DataRow,
  PaginationType,
  Sort,
  SortDirection,
} from "../../models/table";
import TableContext, { PaginationInfo } from "./TableContext";
import { HasLoadingState } from "../../models/common";
import NoResult from "./NoResult";
import settings from "../../services/settings";
import AppTableProvider from "./AppTableProvider";
import PerPageDropdown from "../Pagination/PerPageDropdown";
import JumpToPage from "../Pagination/JumpToPage";
import { Col, FormLabel, Row } from "react-bootstrap";
import Pagination from "../Pagination/Pagination";
import PluralLabel from "./PluralLabel";
import DataTable from "./DataTable";

interface Props<T extends DataRow> {
  entryName?: string;
  source: string;
  columns: Columns<T>;
  extraData: Record<string, unknown>;
  noResult: ReactElement | null;
  pagination: false | PaginationType;
  sortable: boolean;
  limit: number;
  sort: Sort<T>;
  perPageValues: number[];
  serverSide: boolean;
  search?: string | null;
  searchableFields: Array<string>;
  fetchCallback?: (fn: () => any) => any;
}

export interface State<T extends DataRow> extends HasLoadingState {
  draw: number;
  currentDrawn: number;
  rows: Data<T>;
  sort: Sort<T>;
  pagination: PaginationInfo;
}

class ServerTable<T extends DataRow = DataRow> extends Component<
  Props<T>,
  State<T>
> {
  static readonly defaultProps = {
    noResult: <NoResult />,
    pagination: "length_aware",
    sortable: true,
    extraData: {},
    limit: settings.paginationLimit,
    perPageValues: [10, 25, 50, 100],
    sort: { column: null, direction: SortDirection.asc },
    serverSide: true,
    searchableFields: [],
  };

  render(): JSX.Element {
    const {
      columns,
      entryName,
      perPageValues,
      pagination: paginationType,
      ...props
    } = this.props;

    return (
      <AppTableProvider {...props} paginationType={paginationType}>
        <TableContext.Consumer>
          {({ rows, sort, pagination, isLoading }) => (
            <>
              <DataTable<T>
                columns={columns}
                data={rows as Data<T>}
                sort={sort as Sort<T>}
                isLoading={isLoading}
              />
              {paginationType !== false && (
                <Row className="px-3 justify-content-end align-items-center table-footer">
                  {pagination.type === "length_aware" && (
                    <Col className="py-1" xs="auto">
                      <FormLabel
                        column="sm"
                        className="plural-label table-footer-text mb-0"
                      >
                        <PluralLabel
                          name={entryName ?? "common.entries"}
                          count={pagination.recordsFiltered}
                        />
                      </FormLabel>
                    </Col>
                  )}
                  {perPageValues.length > 1 && (
                    <Col className="py-1" xs="auto">
                      <PerPageDropdown perPageValues={perPageValues} />
                    </Col>
                  )}
                  <Col className="py-1" xs="auto">
                    <Pagination />
                  </Col>
                  {pagination.type === "length_aware" && (
                    <Col className="py-1" xs="auto">
                      <JumpToPage />
                    </Col>
                  )}
                </Row>
              )}
            </>
          )}
        </TableContext.Consumer>
      </AppTableProvider>
    );
  }
}

export default ServerTable;
