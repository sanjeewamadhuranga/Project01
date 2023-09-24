import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const routeNamePrefix = "configuration_providers";
const intlPrefix = "configuration.provider";

const ProviderList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      searchableFields={["title", "description", "value"]}
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      columns={[
        {
          id: "title",
          name: intl.formatMessage({
            id: `${intlPrefix}.title`,
            defaultMessage: "Title",
          }),
          render: (title, provider) => (
            <a
              href={Routing.generate(`${routeNamePrefix}_show`, {
                id: provider?.id,
              })}
            >
              {title}
            </a>
          ),
        },
        {
          id: "description",
          name: intl.formatMessage({
            id: `${intlPrefix}.description`,
            defaultMessage: "Description",
          }),
        },
        {
          id: "value",
          name: intl.formatMessage({
            id: `${intlPrefix}.value`,
            defaultMessage: "Value",
          }),
        },
        {
          id: "group",
          name: intl.formatMessage({
            id: `${intlPrefix}.group`,
            defaultMessage: "Group",
          }),
        },
        {
          id: "id",
          sortable: false,
          name: "",
          render: (id: string): JSX.Element => (
            <CrudActions
              title={intl.formatMessage({
                id: `${intlPrefix}.provider`,
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
            id: `${intlPrefix}.noProvider.header`,
            defaultMessage: "No providers found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noProvider.body`,
            defaultMessage: "Providers added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(ProviderList, "Payment providers", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
