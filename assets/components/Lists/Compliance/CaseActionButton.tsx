import React from "react";
import { CaseStatus } from "./CaseStatusLabel";
import { useIntl } from "react-intl";
import { Button } from "react-bootstrap";
import Routing from "../../../services/routing";

export interface CaseActionButtonProps {
  status: CaseStatus;
  id: string;
  routePrefix: string;
  email: string | null;
}

const routeNamePrefix = "compliance_case";

const CaseActionButton = (props: CaseActionButtonProps): JSX.Element => {
  const intl = useIntl();
  const currentUserEmail = window?.APP_SETTINGS?.user;
  const labelMap = {
    [CaseStatus.open]: intl.formatMessage({
      id: "compliance.case.actions.assignReview",
    }),
    [CaseStatus.in_review]:
      props.email === currentUserEmail
        ? intl.formatMessage({
            id: "compliance.case.actions.review",
          })
        : intl.formatMessage({
            id: "compliance.case.actions.assumeReview",
          }),
    [CaseStatus.in_approval]:
      props.email === currentUserEmail
        ? intl.formatMessage({
            id: "compliance.case.actions.approve",
          })
        : props.email
        ? intl.formatMessage({
            id: "compliance.case.actions.assumeApprove",
          })
        : intl.formatMessage({
            id: "compliance.case.actions.assignApprove",
          }),
  };
  return props.status === CaseStatus.closed ? (
    <div data-testid="empty-div" />
  ) : (
    <Button
      size="sm"
      variant="falcon-default"
      href={Routing.generate(`${routeNamePrefix}_show`, { id: props.id })}
    >
      {labelMap[props.status]}
    </Button>
  );
};

export default CaseActionButton;
