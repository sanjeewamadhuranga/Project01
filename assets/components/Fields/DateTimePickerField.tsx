import React, { Component } from "react";
import Flatpickr from "react-flatpickr";
import "flatpickr/dist/themes/airbnb.css";
import { FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";

interface Props extends FieldProps {
  isTimeEnable: boolean;
  numOfMonthsToShow: number;
}

export default class DateTimePickerField extends Component<Props> {
  render(): JSX.Element {
    const options = {
      mode: "range",
      showMonths: this.props.numOfMonthsToShow,
    };

    return (
      <FormGroup>
        <FormLabel>{this.props.label}</FormLabel>
        <Flatpickr
          data-enable-time={this.props.isTimeEnable}
          className="form-control"
          options={options}
        />
      </FormGroup>
    );
  }
}
