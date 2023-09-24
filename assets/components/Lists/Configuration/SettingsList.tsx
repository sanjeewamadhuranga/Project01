import React, { useState } from "react";
import ServerTable from "../../Table/ServerTable";
import { SortDirection } from "../../../models/table";
import { withSearch, SearchableListProps } from "../SearchableList";
import { Entity } from "../../../models/common";
import { Dropdown, ButtonGroup, Row, Col, RowProps } from "react-bootstrap";
import CrudActions from "../../Common/CrudActions";
import NestedValue from "../../Common/NestedValue";
import { FormattedMessage, useIntl } from "react-intl";
import Routing from "../../../services/routing";
import GeneralModal from "../../Modals/GeneralModal";

interface Setting extends Entity {
  name: string;
  value: unknown;
}

const routePrefix = "/configuration/settings";
const routeNamePrefix = "configuration_settings";
const intlPrefix = "configuration.settings";

const SettingsList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();

  return (
    <ServerTable<Setting>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["name", "value.*"]}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
          render: (name, row) => <ConfigWithDescription row={row} />,
        },
        {
          id: "value",
          name: intl.formatMessage({
            id: `${intlPrefix}.value`,
            defaultMessage: "Value",
          }),
          render: (value) => <NestedValue value={value} />,
        },
        {
          id: "name",
          name: "",
          sortable: false,
          render: (name) => (
            <CrudActions
              routeNamePrefix={routeNamePrefix}
              title={intl.formatMessage({
                id: `${intlPrefix}.setting`,
              })}
              id={""}
              extraParams={{ name }}
            />
          ),
        },
      ]}
      sort={{ column: "name", direction: SortDirection.asc }}
      {...props}
    />
  );
};

export default withSearch(SettingsList, "Configurations", () => (
  <Dropdown as={ButtonGroup} className="btn-falcon-default">
    <Dropdown.Toggle variant="falcon-default" size="sm">
      <i className="fas fa-plus" />{" "}
      <FormattedMessage id={`${intlPrefix}.create`} />
    </Dropdown.Toggle>
    <Dropdown.Menu>
      <Dropdown.Item href={`${routePrefix}/create/plain`}>
        <FormattedMessage id={`${intlPrefix}.plain`} />
      </Dropdown.Item>
      <Dropdown.Item href={`${routePrefix}/create/collection`}>
        <FormattedMessage id={`${intlPrefix}.collection`} />
      </Dropdown.Item>
      <Dropdown.Item href={`${routePrefix}/create/object`}>
        <FormattedMessage id={`${intlPrefix}.object`} />
      </Dropdown.Item>
      <Dropdown.Item href={`${routePrefix}/create/bool`}>
        <FormattedMessage id={`${intlPrefix}.bool`} />
      </Dropdown.Item>
    </Dropdown.Menu>
  </Dropdown>
));

interface Prop {
  row: RowProps;
}

const ConfigWithDescription = ({ row }: Prop) => {
  const [isOpen, setIsOpen] = useState(false);

  return (
    <>
      <div>
        <a className="cursor-pointer" onClick={() => setIsOpen(true)}>
          {row.name}
        </a>
      </div>
      {!!isOpen && (
        <GeneralModal
          size="lg"
          show={isOpen}
          hideModal={() => setIsOpen(false)}
          header={row.name}
          headerStyle={{ backgroundColor: "#f8f9fa" }}
        >
          <Row>
            <Col md={7} className="text-650 w-100">
              {/* Retrieve the correct attribute for description from the row*/}
              {row.description ?? (
                <FormattedMessage id={`${intlPrefix}.noDescription`} />
              )}
            </Col>
          </Row>
        </GeneralModal>
      )}
    </>
  );
};
