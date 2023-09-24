import React, { ReactElement, useContext } from "react";
import { Col, FormControl, FormGroup, FormLabel, Row } from "react-bootstrap";
import { FormattedMessage } from "react-intl";
import TableContext from "../Table/TableContext";

const JumpToPage = (): ReactElement => {
  const table = useContext(TableContext);

  const handlePageSelected = (e) => {
    table.setPage(Math.min(Math.max(e.target.value, 1), totalPages()));
  };

  const totalPages = (): number =>
    Math.ceil(table.pagination.recordsFiltered / table.pagination.limit);

  return (
    <FormGroup as={Row}>
      <FormLabel column="sm" className="pe-1 table-footer-text mb-0">
        <FormattedMessage id="common.pagination.jumpToPage" />:
      </FormLabel>
      <Col sm="auto" className="ps-1">
        <FormControl
          role="textbox"
          className="w-auto"
          size="sm"
          type="number"
          min={1}
          max={totalPages()}
          defaultValue={table.pagination.currentPage}
          onInput={handlePageSelected}
        />
      </Col>
    </FormGroup>
  );
};

export default JumpToPage;
