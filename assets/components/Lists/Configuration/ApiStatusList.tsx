import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import PlatFormIcon from "../../Common/PlatFormIcon";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import BoolLabel from "../../Table/BoolLabel";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DateFormatter from "../../Common/DateFormatter";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/status";
const routeNamePrefix = "configuration_status";

const intlPrefix = "configuration.apiStatus";

const ApiStatusList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={[
        "title",
        "platform",
        "action",
        "apiVersion",
        "appVersion",
        "created",
      ]}
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
          id: "platform",
          name: intl.formatMessage({
            id: `${intlPrefix}.platform`,
            defaultMessage: "Platform",
          }),
          render: (platform) => <PlatFormIcon platform={platform} />,
        },
        {
          id: "action",
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Action",
          }),
          render: (action) => <a href={`${action}`}>{action}</a>,
        },
        {
          id: "apiVersion",
          name: intl.formatMessage({
            id: `${intlPrefix}.apiVersion`,
            defaultMessage: "API version",
          }),
        },
        {
          id: "appVersion",
          name: intl.formatMessage({
            id: `${intlPrefix}.appVersion`,
            defaultMessage: "App version",
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
              title="Api Status"
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
            id: `${intlPrefix}.noApiStatus.header`,
            defaultMessage: "No Configuration for Api Status",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noApiStatus.body`,
            defaultMessage: "Api status added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(ApiStatusList, "Notify app builds", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
