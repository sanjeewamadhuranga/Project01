import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DateFormatter from "../../Common/DateFormatter";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/betafeature";
const routeNamePrefix = "configuration_betafeature";

const intlPrefix = "configuration.betaFeature";

const BetaFeatureList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={Routing.generate(`${routeNamePrefix}_list`)}
      searchableFields={["title", "code", "id"]}
      serverSide={false}
      columns={[
        {
          id: "title",
          name: intl.formatMessage({
            id: `${intlPrefix}.title`,
            defaultMessage: "Title",
          }),
          render: (title, feature) => (
            <a href={`${routePrefix}/${feature.id}`}>{title}</a>
          ),
        },
        {
          id: "code",
          name: intl.formatMessage({
            id: `${intlPrefix}.code`,
            defaultMessage: "Code",
          }),
        },
        {
          id: "created",
          name: intl.formatMessage({
            id: `${intlPrefix}.created`,
            defaultMessage: "Created",
          }),
          render: (created: string) => <DateFormatter date={created} />,
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
                id: `${intlPrefix}.betaFeature`,
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
            id: `${intlPrefix}.noBetaFeature.header`,
            defaultMessage: "No Configuration for beta features",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noBetaFeature.body`,
            defaultMessage: "Beta features added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(BetaFeatureList, "Beta features", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
