import React from "react";
import TransactionStatusLabel from "../Transaction/TransactionStatus";
import { FormattedMessage } from "react-intl";
import { CompanyRow } from "../../models/company";
import { TransactionRow } from "../../models/transaction";
import { AdministratorRow } from "../../models/user";
import UserAvatar from "../User/UserAvatar";
import Highlighter from "react-highlight-words";
import { Badge, Placeholder } from "react-bootstrap";
import { formatMoney } from "../../services/currency";
import CompanyStatusLabel from "../Company/CompanyStatusLabel";

export type SerachType = "merchants" | "transactions" | "administrators";
type ResultType = CompanyRow | AdministratorRow | TransactionRow;

interface SearchResultListProps {
  search: string;
  type: SerachType;
  results?: Array<ResultType>;
  isLoading?: boolean;
}

function SearchResultList({
  results,
  search,
  type,
  isLoading = false,
}: SearchResultListProps) {
  const searchValues = search.split(" ");

  return (
    <>
      <h6 className="dropdown-header fw-medium text-uppercase px-card fs--2 pt-0 pb-2">
        <a
          className=""
          href={`/${type}?filters[${
            type === "merchants" ? "searchMerchant" : "search"
          }]=${searchValues}`}
        >
          <FormattedMessage id={`common.search.${type}`} />
        </a>
      </h6>
      {!isLoading && !!results ? (
        !results.length ? (
          search.length < 3 ? (
            <p className="text-center">
              <FormattedMessage id="common.search.typeCharacters" />
            </p>
          ) : (
            <p className="text-center">
              <FormattedMessage id="common.search.notFound" />
            </p>
          )
        ) : (
          results.map((item) =>
            type === "transactions" ? (
              <TransactionItem
                key={item.id}
                type={type}
                searchValues={searchValues}
                result={item as TransactionRow}
              />
            ) : type === "administrators" ? (
              <AvatarItem
                key={item.id}
                type={type}
                searchValues={searchValues}
                result={item as AdministratorRow}
              />
            ) : (
              <CompanyItem
                key={item.id}
                type={type}
                searchValues={searchValues}
                result={item as CompanyRow}
              />
            )
          )
        )
      ) : (
        [1, 2, 3].map((_i, idx) => (
          <Placeholder
            data-testid="placeholder"
            key={idx}
            as="div"
            className="px-3 pb-1"
            animation="glow"
          >
            <Placeholder className="w-100" style={{ height: "22px" }} />
          </Placeholder>
        ))
      )}
    </>
  );
}

interface SearchResultItemProps<T> {
  searchValues: string[];
  type: SerachType;
  result: T;
}

function TransactionItem({
  searchValues,
  type,
  result,
}: SearchResultItemProps<TransactionRow>) {
  return (
    <li>
      <a
        data-testid={`${type}-${result.id}`}
        className="dropdown-item px-card py-1 fs-0"
        href={`/${type}/${result.id}`}
      >
        <div className="d-flex align-items-center">
          <TransactionStatusLabel status={result.status} />
          <div style={{ marginLeft: 8 }} className="flex-1 fs--1 title">
            <Highlighter
              searchWords={searchValues}
              autoEscape={true}
              textToHighlight={result.id ?? ""}
            />
          </div>
          <span className="d-flex">
            {formatMoney(result.amount / 100, result.currency)}
            {!!result.payCurrency &&
              !!result.payAmount &&
              result.currency !== result.payCurrency && (
                <Badge
                  style={{ marginLeft: 8 }}
                  className="badge-soft-info text-wrap w-fit-content"
                  bg=""
                >
                  <FormattedMessage
                    id="transaction.list.paidAs"
                    values={{
                      value: formatMoney(
                        result?.payAmount / 100,
                        result.payCurrency
                      ),
                    }}
                  />
                </Badge>
              )}
          </span>
        </div>
      </a>
    </li>
  );
}

function CompanyItem({
  searchValues,
  type,
  result,
}: SearchResultItemProps<CompanyRow>) {
  const { businessEmail, tradingName, status } = result;
  return (
    <li>
      <a
        data-testid={`${type}-${result.id}`}
        className="dropdown-item px-card py-2"
        href={`/${type}/${result.id}`}
      >
        <div className="d-flex align-items-center">
          <div className="avatar me-2">
            <CompanyStatusLabel status={status} />
          </div>
          <div className="flex-1">
            <h6 className="mb-0 title">
              {tradingName && (
                <Highlighter
                  searchWords={searchValues}
                  autoEscape={true}
                  textToHighlight={tradingName ?? ""}
                />
              )}
            </h6>
            <p className="fs--2 mb-0 d-flex">
              {businessEmail && (
                <Highlighter
                  searchWords={searchValues}
                  autoEscape={true}
                  textToHighlight={businessEmail ?? ""}
                />
              )}
            </p>
          </div>
        </div>
      </a>
    </li>
  );
}

function AvatarItem({
  searchValues,
  result,
  type,
}: SearchResultItemProps<AdministratorRow>) {
  const { email } = result;
  return (
    <li>
      <a
        data-testid={`${type}-${result.id}`}
        className="dropdown-item px-card py-2"
        href={`/${type}/${result.id}`}
      >
        <div className="d-flex align-items-center">
          <div className="avatar me-2">
            <UserAvatar email={email} />
          </div>
          <div className="flex-1">
            <h6 className="mb-0 title">
              {email && (
                <Highlighter
                  searchWords={searchValues}
                  autoEscape={true}
                  textToHighlight={email ?? ""}
                />
              )}
            </h6>
          </div>
        </div>
      </a>
    </li>
  );
}

export default SearchResultList;
