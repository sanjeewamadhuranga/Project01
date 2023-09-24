import { SearchableListProps, withSearch } from "../SearchableList";
import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import React from "react";
import LogDetailsModal from "../../Modals/LogDetailsModal";
import { LogRow } from "../../../models/configuration";
import { SortDirection } from "../../../models/table";
import { useIntl } from "react-intl";
import DateFormatter from "../../Common/DateFormatter";

const intlPrefix = "configuration.integration";

const LogsList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<LogRow>
      source={`/configuration/logs/list`}
      columns={[
        {
          id: "created",
          name: intl.formatMessage({
            id: `${intlPrefix}.created`,
            defaultMessage: "Date",
          }),
          render: (created: string) => <DateFormatter date={created} />,
        },
        {
          id: "user",
          name: intl.formatMessage({
            id: `${intlPrefix}.user`,
            defaultMessage: "User",
          }),
        },
        {
          id: "action",
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Type",
          }),
        },
        {
          id: "objectClass",
          name: intl.formatMessage({
            id: `${intlPrefix}.objectClass`,
            defaultMessage: "Object",
          }),
        },
        {
          id: "id",
          sortable: false,
          name: "",
          render: (id: string, dataRow: LogRow): JSX.Element => {
            return <LogDetailsModal log={dataRow} />;
          },
        },
      ]}
      extraData={{ filters: { search: props.search } }}
      sort={{ column: "created", direction: SortDirection.desc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noLogs.header`,
            defaultMessage: "No logs found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noLogs.body`,
            defaultMessage: "Logs added will appear here",
          })}
        />
      }
    />
  );
};

export default withSearch(LogsList, "Audit logs");
