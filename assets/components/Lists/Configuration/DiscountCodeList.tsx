import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/discount-codes";
const routeNamePrefix = "configuration_discount_code";
const intlPrefix = "configuration.discountCode";

const DiscountCodeList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      searchableFields={["title", "code", "id"]}
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      columns={[
        {
          id: "title",
          name: intl.formatMessage({
            id: `${intlPrefix}.title`,
            defaultMessage: "Title",
          }),
          render: (title, code) => (
            <a href={`${routePrefix}/${code.id}`}>{title}</a>
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
          id: "id",
          sortable: false,
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Action",
          }),
          render: (id: string): JSX.Element => (
            <CrudActions
              title={intl.formatMessage({
                id: `${intlPrefix}.discountCode`,
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
            id: `${intlPrefix}.noDiscountCode.header`,
            defaultMessage: "No discount codes found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noDiscountCode.body`,
            defaultMessage: "Discount codes added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(DiscountCodeList, "Discount codes", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
