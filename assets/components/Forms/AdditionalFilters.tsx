import React, { Component, PropsWithChildren } from "react";
import { Col, Collapse, Row } from "react-bootstrap";
import { FormattedMessage } from "react-intl";

export interface Props {
  open?: boolean;
  valuesCount?: number;
}

interface State {
  open: boolean;
}

export default class AdditionalFilters extends Component<
  PropsWithChildren<Props>,
  State
> {
  readonly state: State = {
    open: this.props.open ?? false,
  };

  toggle(): void {
    this.setState({ open: !this.state.open });
  }

  render(): JSX.Element {
    const { children, valuesCount } = this.props;
    return (
      <Row>
        <Col sm={12}>
          <Collapse in={this.state.open}>
            <div>{children}</div>
          </Collapse>
        </Col>
        <Col sm={12} className={`text-center ${this.state.open ? "mt-2" : ""}`}>
          <div className="link-primary" onClick={this.toggle.bind(this)}>
            <strong className="form-filter-show">
              {!!valuesCount && (
                <>
                  <FormattedMessage
                    id={"common.actions.appliedCount"}
                    values={{ count: valuesCount }}
                  />
                  {" - "}
                </>
              )}
              <FormattedMessage
                id={`common.actions.${
                  this.state.open ? "hide" : "show"
                }AdditionalFilters`}
              />
            </strong>
            <span className="p-2">
              <i
                className={`fas fa-chevron-${this.state.open ? "up" : "down"}`}
              />
            </span>
          </div>
        </Col>
      </Row>
    );
  }
}
