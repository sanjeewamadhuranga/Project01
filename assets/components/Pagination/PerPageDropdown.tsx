import React, { ChangeEvent, ReactElement, useContext } from "react";
import { FormGroup, FormLabel, FormSelect, Row } from "react-bootstrap";
import { FormattedMessage } from "react-intl";
import TableContext from "../Table/TableContext";

interface Props {
  perPageValues: Array<number>;
}

const PerPageDropdown = (props: Props): ReactElement => {
  const table = useContext(TableContext);
  return (
    <FormGroup as={Row}>
      <FormLabel className="table-footer-text mb-0" column="sm">
        <FormattedMessage id="common.actions.show" />
      </FormLabel>
      <FormSelect
        size="sm"
        className="col-auto w-auto table-footer-text"
        onChange={(e: ChangeEvent<HTMLSelectElement>) =>
          table.setLimit(parseInt(e.target.value))
        }
        data-testid="perpage-dropdown"
        value={table.pagination.limit}
      >
        {props.perPageValues.map((value) => (
          <option key={value} value={value}>
            {value}
          </option>
        ))}
      </FormSelect>
    </FormGroup>
  );
};

export default PerPageDropdown;
