import React, { Component } from "react";

import { DateRangePicker, RangeKeyDict } from "react-date-range";
import "react-date-range/dist/styles.css";
import "react-date-range/dist/theme/default.css";
import { Card } from "react-bootstrap";
import { FormattedMessage } from "react-intl";
import { getDateEnd } from "../../utils/dateTimeUtils";

interface State {
  startDate: Date;
  endDate: Date;
}

interface Props {
  setDate: (string) => void;
  close: () => void;
  startDate?: Date;
  endDate?: Date;
}

export default class DateCalendar extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      startDate: this.props.startDate ?? new Date(),
      endDate: this.props.endDate ?? new Date(),
    };

    this.onSetDate = this.onSetDate.bind(this);
    this.onSelectDates = this.onSelectDates.bind(this);
  }

  UNSAFE_componentWillReceiveProps(nextProps: Props) {
    if (
      this.props.endDate !== nextProps.endDate ||
      this.props.startDate !== nextProps.startDate
    ) {
      this.setState({
        startDate: this.props.startDate ?? new Date(),
        endDate: this.props.endDate ?? new Date(),
      });
    }
  }

  onSelectDates(values: RangeKeyDict): void {
    this.setState({
      startDate: values["selection"].startDate ?? new Date(),
      endDate:
        getDateEnd(values["selection"].endDate?.toString() ?? "") ?? new Date(),
    });
  }

  onSetDate(): void {
    this.props.setDate({
      min: this.state.startDate,
      max: this.state.endDate,
    });
    this.props.close();
  }

  render(): JSX.Element {
    const selectionRange = {
      ...this.state,
      key: "selection",
    };
    return (
      <Card className="react-calendar__card">
        <div className="border-bottom py-4 d-flex flex-row justify-content-center">
          <div className="mx-4">
            <DateRangePicker
              ranges={[selectionRange]}
              onChange={this.onSelectDates}
            />
          </div>
        </div>
        <div className="d-flex flex-row justify-content-between py-4 px-4">
          <p>
            <FormattedMessage
              id="common.calendarFooterInfo"
              values={{ timezone: new Date().toTimeString().slice(9) ?? "" }}
            />
          </p>
          <div className="">
            <span className="mx-4 cursor-pointer" onClick={this.props.close}>
              <FormattedMessage id="common.actions.cancel" />
            </span>
            <span className="">
              <button
                type="submit"
                className="btn btn-primary btn-sm"
                onClick={this.onSetDate}
              >
                <FormattedMessage id="common.actions.setDate" />
              </button>
            </span>
          </div>
        </div>
      </Card>
    );
  }
}
