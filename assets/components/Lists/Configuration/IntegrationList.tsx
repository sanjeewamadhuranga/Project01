import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/integrations";
const routeNamePrefix = "configuration_integration";
const intlPrefix = "configuration.integration";

const IntegrationList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={Routing.generate(`${routeNamePrefix}_list`)}
      searchableFields={["name", "type", "id", "email"]}
      serverSide={false}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
          render: (name, integration): JSX.Element => (
            <a href={`${routePrefix}/${integration.id}`}>{name}</a>
          ),
        },
        {
          id: "type",
          name: intl.formatMessage({
            id: `${intlPrefix}.type`,
            defaultMessage: "Type",
          }),
        },
        {
          id: "id",
          sortable: false,
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Action",
          }),
          render: (id: string): JSX.Element => (
            <CrudActions
              title={intl.formatMessage({
                id: `${intlPrefix}.integration`,
              })}
              routeNamePrefix={routeNamePrefix}
              id={id}
            />
          ),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id): JSX.Element => (
            <DetailsButton id={id} routeNamePrefix={routeNamePrefix} />
          ),
        },
      ]}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noIntegration.header`,
            defaultMessage: "No integrations found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noIntegration.body`,
            defaultMessage: "Integrations added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(IntegrationList, "Integrations", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
