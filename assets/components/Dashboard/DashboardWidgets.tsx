import React from "react";
import SalesOverTimeWidget from "./Widgets/SalesOverTimeWidget";
import MerchantsStatsWidget from "./Widgets/MerchantsStatsWidget";
import { Col, Row } from "react-bootstrap";
import SimpleTransactionListing from "../Transaction/SimpleTransactionListing";

export default function DashboardWidgets(): JSX.Element {
  return (
    <>
      <Row>
        <Col>
          <SalesOverTimeWidget />
        </Col>
      </Row>
      <Row>
        <Col>
          <MerchantsStatsWidget />
        </Col>
      </Row>
      <Row>
        <Col>
          <SimpleTransactionListing viewAllUrl="/transactions" />
        </Col>
      </Row>
    </>
  );
}
