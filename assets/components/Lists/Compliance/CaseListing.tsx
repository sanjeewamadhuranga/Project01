import React, { useState } from "react";
import { Card } from "react-bootstrap";
import CardHeader from "react-bootstrap/CardHeader";
import CaseList from "./CaseList";
import { CaseStatus } from "./CaseStatusLabel";
import { ChoiceOption } from "../../../models/form";
import CaseSummary from "./CaseSummary";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";

const intlPrefix = "compliance.case.status";
const routeNamePrefix = "compliance_case";

interface CaseListingProps extends SearchableListProps {
  onlyme: string;
}

const CaseListing = (props: CaseListingProps): JSX.Element => {
  const intl = useIntl();
  const options: Array<ChoiceOption> = [
    { value: "all", label: intl.formatMessage({ id: `${intlPrefix}.all` }) },
    {
      value: CaseStatus.open,
      label: intl.formatMessage({ id: `${intlPrefix}.open` }),
    },
    {
      value: CaseStatus.in_review,
      label: intl.formatMessage({ id: `${intlPrefix}.in_review` }),
    },
    {
      value: CaseStatus.in_approval,
      label: intl.formatMessage({ id: `${intlPrefix}.in_approval` }),
    },
    {
      value: CaseStatus.closed,
      label: intl.formatMessage({ id: `${intlPrefix}.closed` }),
    },
  ];
  const [status, setStatus] = useState<string>("all");

  return (
    <>
      <CaseSummary
        setFilterByFieldValue={setStatus}
        summarySource={
          props.onlyme === "1"
            ? "/stats/compliance/my_tasks/overview"
            : "/stats/compliance/overview"
        }
      />

      <Card className="md-3">
        <CardHeader>
          {options.map((option) => (
            <button
              className={`btn mr-2 py-2 ${
                option.value === status ? "btn-primary text-white" : ""
              } form-filter-btn`}
              aria-readonly={true}
              defaultValue={option.value}
              key={option.value}
              onClick={() => setStatus(option.value.toString())}
            >
              {option.label}
            </button>
          ))}
        </CardHeader>

        <CaseList
          extraData={{
            search: props.search,
            status: status,
            onlyMe: props.onlyme === "1",
          }}
        />
      </Card>
    </>
  );
};

export default withSearch(
  CaseListing,
  "Cases",
  () => <CreateButton routeNamePrefix={routeNamePrefix} />,
  true,
  true
);
