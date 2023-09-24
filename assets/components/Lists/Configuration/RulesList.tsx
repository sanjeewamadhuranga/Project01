import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import BoolLabel from "../../Table/BoolLabel";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/rules";
const routeNamePrefix = "configuration_rules";
const intlPrefix = "configuration.rules";

const RulesList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["name", "decision", "active", "event"]}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
          render: (name, feature) => (
            <a href={`${routePrefix}/${feature.id}`}>{name}</a>
          ),
        },
        {
          id: "decision",
          name: intl.formatMessage({
            id: `${intlPrefix}.decision`,
            defaultMessage: "Decision",
          }),
        },
        {
          id: "active",
          name: intl.formatMessage({
            id: `${intlPrefix}.active`,
            defaultMessage: "Active",
          }),
          render: (active: boolean): JSX.Element => (
            <BoolLabel value={active} />
          ),
        },
        {
          id: "event",
          name: intl.formatMessage({
            id: `${intlPrefix}.event`,
            defaultMessage: "Event",
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
                id: `${intlPrefix}.rules`,
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
            id: `${intlPrefix}.noRule.header`,
            defaultMessage: "No Configuration for Rules",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noRule.body`,
            defaultMessage: "Rules added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(RulesList, "Rules", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
