import { useIntl } from "react-intl";
import ServerTable from "../../Table/ServerTable";
import React from "react";
import { SortDirection } from "../../../models/table";
import NoResult from "../../Table/NoResult";
import { Entity } from "../../../models/common";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import Routing from "../../../services/routing";
import DateFormatter from "../../Common/DateFormatter";

const intlPrefix = "config.holidayCalendar";
const routeNamePrefix = "configuration_holiday_calendar";

interface HolidayCalendarRow extends Entity {
  date: string | null;
  description: string | null;
}

const HolidayCalendarList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<HolidayCalendarRow>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      searchableFields={["date", "description"]}
      columns={[
        {
          id: "date",
          name: intl.formatMessage({
            id: `${intlPrefix}.date`,
            defaultMessage: "Date",
          }),
          render: (date) => <DateFormatter date={date} displayTime={false} />,
        },
        {
          id: "description",
          name: intl.formatMessage({
            id: `${intlPrefix}.description`,
            defaultMessage: "Description",
          }),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id): JSX.Element => (
            <CrudActions
              routeNamePrefix={routeNamePrefix}
              title={"holiday"}
              id={id}
            />
          ),
        },
      ]}
      sort={{ column: "date", direction: SortDirection.asc }}
      extraData={{ filters: { search: props.search } }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noHoliday.header`,
            defaultMessage: "No holidays found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noHoliday.body`,
            defaultMessage: "Holidays will appear here",
          })}
        />
      }
    />
  );
};

export default withSearch(HolidayCalendarList, "Holiday calendar", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
