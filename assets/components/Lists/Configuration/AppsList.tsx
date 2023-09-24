import React from "react";
import { useIntl } from "react-intl";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import { SearchableListProps, withSearch } from "../SearchableList";
import JustifyEnd from "../../Common/JustifyEnd";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const intlPrefix = "configuration.apps";

interface Props extends SearchableListProps {
  source: string;
  companyid: string;
}

const AppList = (props: Props): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={props.source}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
        },
        {
          id: "appId",
          name: intl.formatMessage({
            id: `${intlPrefix}.appId`,
            defaultMessage: "App Id",
          }),
        },
        {
          id: "domain",
          name: intl.formatMessage({
            id: `${intlPrefix}.domain`,
            defaultMessage: "Domain",
          }),
        },
        {
          id: "appModel",
          name: intl.formatMessage({
            id: `${intlPrefix}.appModel`,
            defaultMessage: "Model",
          }),
        },
        {
          id: "currency",
          name: intl.formatMessage({
            id: `${intlPrefix}.currency`,
            defaultMessage: "Currency",
          }),
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
          name: "",
          render: (id: string): JSX.Element => (
            <JustifyEnd>
              <DetailsButton
                url={Routing.generate(`merchants_apps_show`, {
                  id: props.companyid,
                  app: id,
                })}
              ></DetailsButton>
            </JustifyEnd>
          ),
        },
      ]}
      extraData={{ filters: { search: props.search } }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noApps.header`,
            defaultMessage: "No Apps found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noApps.body`,
            defaultMessage: "Apps added will appear here",
          })}
        />
      }
    />
  );
};

export default withSearch(AppList, "Apps and terminals");
