import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import { MerchantRoleRow } from "../../../models/configuration";
import { Badge } from "react-bootstrap";
import BoolLabel from "../../Table/BoolLabel";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";

const routeNamePrefix = "configuration_roles";
const intlPrefix = "configuration.roles";

const RolesList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<MerchantRoleRow>
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
          render: (description: string, role: MerchantRoleRow): JSX.Element => (
            <>
              <p>{description}</p>
              {role.companies.map((company) => (
                <Badge
                  pill={true}
                  key={company.id}
                  bg="primary"
                  className="me-1"
                >
                  {company.name}
                </Badge>
              ))}
            </>
          ),
        },
        {
          id: "default",
          name: intl.formatMessage({
            id: `${intlPrefix}.default`,
            defaultMessage: "Default",
          }),
          render: (isDefault: boolean): JSX.Element => (
            <BoolLabel value={isDefault} />
          ),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id: string): JSX.Element => (
            <CrudActions
              routeNamePrefix={routeNamePrefix}
              title={intl.formatMessage({
                id: `${intlPrefix}.role`,
              })}
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
            defaultMessage: "Merchant roles will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(RolesList, "Merchant roles", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
