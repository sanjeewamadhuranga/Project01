import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import { SearchableListProps, withSearch } from "../SearchableList";
import { useIntl } from "react-intl";
import BoolLabel from "../../Table/BoolLabel";
import MigrationButton from "../../Migration/MigrationButton";
import { SortDirection } from "../../../models/table";

const routePrefix = "/configuration/migrations";
const intlPrefix = "configuration.migrations";

interface MigrationRow {
  name: string;
  executed: boolean;
  available: boolean;
  description: string | null;
}

const MigrationsList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<MigrationRow>
      source={`${routePrefix}/list`}
      serverSide={false}
      searchableFields={["name"]}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
        },
        {
          id: "executed",
          name: intl.formatMessage({
            id: `${intlPrefix}.executed`,
            defaultMessage: "Executed",
          }),
          render: (executed: boolean): JSX.Element => (
            <BoolLabel value={executed} />
          ),
        },
        {
          id: "available",
          name: intl.formatMessage({
            id: `${intlPrefix}.available`,
            defaultMessage: "Available",
          }),
          render: (available: boolean): JSX.Element => (
            <BoolLabel value={available} />
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
          id: null,
          sortable: false,
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Action",
          }),
          render: (name, row): JSX.Element =>
            row.available && !row.executed ? (
              <>
                <MigrationButton
                  label="Migrate"
                  url={`${routePrefix}/${row.name}/migrate`}
                />
              </>
            ) : row.available && row.executed ? (
              <MigrationButton
                label="Rollback"
                url={`${routePrefix}/${row.name}/rollback`}
              />
            ) : (
              <></>
            ),
        },
      ]}
      sort={{ column: "name", direction: SortDirection.desc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noRule.header`,
            defaultMessage: "No Configuration for Migrations",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noRule.body`,
            defaultMessage: "Migrations added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(MigrationsList, "Migrations");
