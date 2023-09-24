import { SearchableListProps, withSearch } from "../SearchableList";
import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import React, { useState } from "react";
import { SortDirection } from "../../../models/table";
import CreateButton from "../../Common/CreateButton";
import { Entity } from "../../../models/common";
import { formatMoney } from "../../../services/currency";
import DisputeStateLabel from "./DisputeStateLabel";
import { Card } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";
import DateFormatter from "../../Common/DateFormatter";
import { TransactionStatus as Status } from "../../../models/transaction";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/compliance/disputes";
const routeNamePrefix = "compliance_dispute";

export interface DisputeRowCompany {
  id: string;
  name: string;
}

export interface DisputeRowTransaction {
  id: string;
  amount: number;
  currency: string;
  state: Status;
  balance: number;
}

export interface DisputeRowChargeback {
  id: string | null;
  amount: number | null;
  currency: string | null;
  state: Status | null;
  balance: number | null;
}

export enum DisputeState {
  new = "compliance.dispute_state.new",
  processing = "compliance.dispute_state.processing",
  closed = "compliance.dispute_state.closed",
}

export interface DisputeRow extends Entity {
  company: DisputeRowCompany;
  transaction: DisputeRowTransaction;
  chargeback: DisputeRowChargeback;
  disputeFee: number;
  handler: string;
  state: DisputeState;
  reason: string;
  comments: string;
  attachments: Array<string>;
}

interface State {
  filterData: Record<string, Array<string>>;
}

const intlPrefix = "compliance.disputes";

const options: { value: string[]; label: string }[] = [
  {
    value: [
      "compliance.dispute_state.new",
      "compliance.dispute_state.processing",
    ],
    label: "compliance.disputes.open",
  },
  {
    value: ["compliance.dispute_state.closed"],
    label: "compliance.disputes.closed",
  },
  { value: ["compliance.dispute_state.new"], label: "compliance.disputes.new" },
  {
    value: ["compliance.dispute_state.processing"],
    label: "compliance.disputes.processing",
  },
  { value: [], label: "compliance.disputes.all" },
];

const DisputeList = (props: SearchableListProps): JSX.Element => {
  const [state, setState] = useState<State>({
    filterData: {
      status: options[0].value,
    },
  });
  const intl = useIntl();

  return (
    <>
      <Card.Header>
        {options.map((option) => (
          <button
            className={`btn mr-2 py-2 ${
              state.filterData.status.length === option.value.length &&
              state.filterData.status.every(
                (value, index) => value === option.value[index]
              )
                ? "btn-primary text-white"
                : ""
            } form-filter-btn`}
            aria-readonly={true}
            defaultValue={option.value}
            key={option.label}
            onClick={() =>
              setState({
                filterData: {
                  status: option.value,
                },
              })
            }
          >
            <FormattedMessage id={option.label} />
          </button>
        ))}
      </Card.Header>
      <ServerTable<DisputeRow>
        source={`${routePrefix}/list`}
        serverSide={true}
        searchableFields={["company.name", "handler", "transaction.amount"]}
        columns={[
          {
            id: "state",
            name: intl.formatMessage({
              id: `${intlPrefix}.state`,
              defaultMessage: "State",
            }),
            render: (status): JSX.Element => (
              <DisputeStateLabel state={status} />
            ),
          },
          {
            id: "company",
            name: intl.formatMessage({
              id: `${intlPrefix}.company`,
              defaultMessage: "Company",
            }),
            sortable: false,
            render: (company): JSX.Element => (
              <a href={`/merchants/${company.id}`}>{company.name}</a>
            ),
          },
          {
            id: "transaction",
            name: intl.formatMessage({
              id: `${intlPrefix}.transaction`,
              defaultMessage: "Transaction",
            }),
            sortable: false,
            render: (transaction): JSX.Element => (
              <a href={`/transactions/${transaction.id}`}>
                {formatMoney(transaction.amount / 100, transaction.currency)}
              </a>
            ),
          },
          {
            id: "createdAt",
            name: intl.formatMessage({
              id: `${intlPrefix}.createdAt`,
              defaultMessage: "Date created",
            }),
            render: (createdAt, dispute): JSX.Element =>
              createdAt ? (
                <div>
                  <DateFormatter date={createdAt} />
                  <div className="small">{dispute.handler}</div>
                </div>
              ) : (
                <div className="small">{dispute.handler}</div>
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
        extraData={{ filters: { search: props.search, ...state.filterData } }}
        sort={{ column: "createdAt", direction: SortDirection.desc }}
        noResult={
          <NoResult
            header={intl.formatMessage({
              id: `${intlPrefix}.noDisputes.header`,
              defaultMessage: "No dispute found",
            })}
            body={intl.formatMessage({
              id: `${intlPrefix}.noDisputes.body`,
              defaultMessage: "Disputes will appear here",
            })}
          />
        }
        {...props}
      />
    </>
  );
};

export default withSearch(DisputeList, "Disputes", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
