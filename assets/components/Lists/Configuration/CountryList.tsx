import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import BoolLabel from "../../Table/BoolLabel";
import CrudActions from "../../Common/CrudActions";
import { SortDirection } from "../../../models/table";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { Entity } from "../../../models/common";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DateFormatter from "../../Common/DateFormatter";
import DetailsButton from "../../Common/DetailsButton";

interface Country extends Entity {
  countryName: string;
  countryCode: string;
  flag: string;
  dialingCode: string;
  enabled: boolean;
  created: string;
}

const routePrefix = "/configuration/country";
const routeNamePrefix = "configuration_country";
const intlPrefix = "configuration.country";

const CountryList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<Country>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["countryName", "countryCode", "dialingCode", "flag"]}
      columns={[
        {
          id: "countryName",
          name: intl.formatMessage({
            id: `${intlPrefix}.countryName`,
            defaultMessage: "Name",
          }),
          render: (name, country): JSX.Element => (
            <a href={`${routePrefix}/${country.id}`}>{name}</a>
          ),
        },
        {
          id: "countryCode",
          name: intl.formatMessage({
            id: `${intlPrefix}.countryCode`,
            defaultMessage: "Country Code",
          }),
        },
        {
          id: "flag",
          name: intl.formatMessage({
            id: `${intlPrefix}.flag`,
            defaultMessage: "Flag",
          }),
        },
        {
          id: "dialingCode",
          name: intl.formatMessage({
            id: `${intlPrefix}.dialingCode`,
            defaultMessage: "Dialing Prefix",
          }),
        },
        {
          id: "enabled",
          name: intl.formatMessage({
            id: `${intlPrefix}.enabled`,
            defaultMessage: "Enabled",
          }),
          render: (enabled): JSX.Element => <BoolLabel value={enabled} />,
        },
        {
          id: "created",
          name: intl.formatMessage({
            id: `${intlPrefix}.created`,
            defaultMessage: "Created",
          }),
          render: (created) => <DateFormatter date={created} />,
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
                id: `${intlPrefix}.country`,
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
      sort={{ column: "countryCode", direction: SortDirection.asc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noCountry.header`,
            defaultMessage: "No Configuration for country",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noCountry.body`,
            defaultMessage: "Country added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(CountryList, "Available countries", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
