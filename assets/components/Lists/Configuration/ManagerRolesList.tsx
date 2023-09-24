import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import { Entity } from "../../../models/common";
import BoolLabel from "../../Table/BoolLabel";
import Routing from "../../../services/routing";

const routeNamePrefix = "configuration_manager_roles";
const intlPrefix = "configuration.manager_roles";

export interface ManagerRoleRow extends Entity {
  name: string;
  description: string;
  permissions: Array<string>;
}

const ManagerRolesList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<ManagerRoleRow>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["name", "id", "description", "companies.name"]}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
        },
        {
          id: "description",
          name: intl.formatMessage({
            id: `${intlPrefix}.description`,
            defaultMessage: "Description",
          }),
        },
        {
          id: "deleted",
          name: intl.formatMessage({
            id: `${intlPrefix}.isDeleted`,
            defaultMessage: "isDeleted?",
          }),
          render: (deleted: boolean): JSX.Element => (
            <BoolLabel value={deleted} />
          ),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id: string): JSX.Element => (
            <CrudActions
              routeNamePrefix={routeNamePrefix}
              title="role"
              id={id}
            />
          ),
        },
      ]}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noRole.header`,
            defaultMessage: "No roles",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noRole.body`,
            defaultMessage: "Manager roles will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(ManagerRolesList, "Administrator roles", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
