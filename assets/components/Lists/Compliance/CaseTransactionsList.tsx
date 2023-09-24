import { SearchableListProps } from "../SearchableList";
import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import React from "react";
import { Entity } from "../../../models/common";
import { formatMoney } from "../../../services/currency";
import { Button } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";
import DateFormatter from "../../Common/DateFormatter";

const routePrefix = "/compliance/case";

interface CaseTransactionRow extends Entity {
  provider: string;
  amount: number;
  currency: string;
  rate: null | number;
  commissionAmount: null | number;
  costCurrency: null | string;
  netAmount: null | number;
  initiatorEmail: string;
  initiatorName: string;
}

interface SearchableListPropsWithId extends SearchableListProps {
  id: string;
}

const intlPrefix = "compliance.case.transaction";

const CaseTransactionsList = (
  props: SearchableListPropsWithId
): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<CaseTransactionRow>
      source={`${routePrefix}/${props.id}/transactions`}
      entryName="transaction"
      serverSide={false}
      columns={[
        {
          id: "createdAt",
          name: intl.formatMessage({
            id: `${intlPrefix}.createdAt`,
            defaultMessage: "Processing Date",
          }),
          render: (createdAt) => <DateFormatter date={createdAt} />,
        },
        {
          id: "provider",
          name: intl.formatMessage({
            id: `${intlPrefix}.provider`,
            defaultMessage: "Provider",
          }),
        },
        {
          id: "amount",
          name: intl.formatMessage({
            id: `${intlPrefix}.amount`,
            defaultMessage: "Amount",
          }),
          render: (amount, row): string =>
            formatMoney(amount / 100, row.currency),
        },
        {
          id: "rate",
          name: intl.formatMessage({
            id: `${intlPrefix}.rate`,
            defaultMessage: "Rate",
          }),
          render: (rate): string =>
            rate ? Number.parseFloat((rate / 100).toString()).toFixed(2) : "",
        },
        {
          id: "commissionAmount",
          name: intl.formatMessage({
            id: `${intlPrefix}.commissionAmount`,
            defaultMessage: "Commission",
          }),
          render: (commission, row): string =>
            commission && row.costCurrency
              ? formatMoney(commission / 100, row.costCurrency)
              : "",
        },
        {
          id: "netAmount",
          name: intl.formatMessage({
            id: `${intlPrefix}.netAmount`,
            defaultMessage: "Net Amount",
          }),
          render: (netAmount, row): string =>
            netAmount && row.costCurrency
              ? formatMoney(netAmount / 100, row.costCurrency)
              : "",
        },
        {
          id: "initiatorEmail",
          name: intl.formatMessage({
            id: `${intlPrefix}.initiatorEmail`,
            defaultMessage: "Initiator Email",
          }),
        },
        {
          id: "initiatorName",
          name: intl.formatMessage({
            id: `${intlPrefix}.initiatorName`,
            defaultMessage: "Initiator Name",
          }),
        },
        {
          id: "id",
          sortable: false,
          name: "",
          render: (): JSX.Element => (
            //todo: add details when transaction view will be done
            <Button className={"btn btn-sm btn-falcon-default"}>
              <FormattedMessage id="common.actions.details" />
            </Button>
          ),
        },
      ]}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noTransaction.header`,
            defaultMessage: "No transactions found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noTransaction.body`,
            defaultMessage: "Transactions will appear here",
          })}
        />
      }
    />
  );
};

export default CaseTransactionsList;
